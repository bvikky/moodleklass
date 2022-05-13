<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_iomad_approve_access
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/local/iomad/lib/company.php');
require_once($CFG->dirroot.'/local/iomad/lib/user.php');
require_once($CFG->dirroot.'/local/iomad/lib/iomad.php');

class iomad_approve_access {
    /**
     * Checks if the current user has any outstanding approvals.
     *
     * returns Boolean
     *
     **/
     public static function has_users() {
        global $CFG, $DB, $USER;

        // Do we have a companyid?
        if (!$companyid = iomad::get_my_companyid(context_system::instance(), false)) {
            return false;
        }

        // Check if we can have users of my type.
        if (is_siteadmin($USER->id)) {
            $approvaltype = 'both';
        } else {
            // What type of manager am I?
            if ($DB->get_records('company_users', array('userid' => $USER->id, 'managertype' => 1, 'companyid' => $companyid))) {
                $approvaltype = 'company';
            } else if ($DB->get_records('company_users', array('userid' => $USER->id, 'managertype' => 2, 'companyid' => $companyid))) {
                $approvaltype = 'manager';
            } else {
                return false;
            }
        }

        // If we have at least manager type approval,
        if ($approvaltype == 'both' || $approvaltype == 'manager') {

            // then get the list of users I am responsible for.
            $myuserids = company::get_my_users_list($companyid);
            if (!empty($myuserids) && $DB->get_records_sql("SELECT beae.* FROM {block_iomad_approve_access} beae
                                                   RIGHT JOIN {trainingevent} cc ON cc.id=beae.activityid
                                                   AND cc.approvaltype in (1,3)
                                                   WHERE beae.companyid=:companyid AND beae.manager_ok = 0
                                                   AND beae.userid != :myuserid
                                                   AND beae.userid
                                                   IN ($myuserids)", array('companyid' => $companyid, 'myuserid' => $USER->id))) {
                return true;
            }
        }

        // If we have at least company type approval,
        if ($approvaltype == 'both' || $approvaltype == 'company') {

            // then get the list of users I am responsible for.
            $myuserids = company::get_my_users_list($companyid);
            if (!empty($myuserids) && $DB->get_records_sql("SELECT beae.* FROM {block_iomad_approve_access} beae
                                      RIGHT JOIN {trainingevent} cc ON cc.id=beae.activityid
                                      WHERE beae.companyid=:companyid
                                      AND beae.userid != :myuserid
                                      AND beae.userid IN ($myuserids)
                                      AND (
                                       cc.approvaltype in (2,3)
                                       AND beae.tm_ok = 0 )
                                      OR (
                                       cc.approvaltype = 1
                                       AND beae.manager_ok = 0)", array('companyid' => $companyid, 'myuserid' => $USER->id))) {
                return true;
            }
        }

        // Hasn't returned yet, return false as default.
        return false;
    }

    /**
     * Gets the list of outstanding approvals for the current user.
     *
     * returns array
     *
     **/
    public static function get_my_users() {
        global $CFG, $DB, $USER;

        // Do we have a companyid?
        if (!$companyid = iomad::get_my_companyid(context_system::instance(), false)) {
            return false;
        }

        // If I'm a site admin I can approve any type.
        if (is_siteadmin($USER->id)) {
            $approvaltype = 'both';
        } else {
            // Work out what type of manager I am, if any?
            if ($manageruser = $DB->get_record_select('company_users', 'userid = :userid AND companyid = :companyid AND managertype > 0', array('userid' => $USER->id, 'companyid' => $companyid))) {
                if ($manageruser->managertype == 2) {

                    // Department manager.
                    $approvaltype = 'manager';
                } else if ($manageruser->managertype == 1) {

                    // Company manager.
                    $approvaltype = 'company';
                }
            } else {

                // Not a manager.
                return false;
            }
        }

        // Get the list of users I am responsible for.
        $myuserids = company::get_my_users_list($companyid);
        if (!empty($myuserids)) {
            if ($approvaltype == 'manager') {
                //  Need to deal with departments here.
                if ($userarray = $DB->get_records_sql("SELECT beae.* FROM {block_iomad_approve_access} beae
                                                   RIGHT JOIN {trainingevent} cc ON cc.id=beae.activityid
                                                   AND cc.approvaltype in (1,3)
                                                   WHERE beae.companyid=:companyid AND beae.manager_ok = 0
                                                   AND beae.userid != :myuserid
                                                   AND beae.userid
                                                   IN ($myuserids)", array('companyid' => $companyid, 'myuserid' => $USER->id))) {
                    return $userarray;
                }
            }

            // Get the users who need company type approval.
            if ($approvaltype == 'company') {
                if ($userarray = $DB->get_records_sql("SELECT beae.* FROM {block_iomad_approve_access} beae
                                                   RIGHT JOIN {trainingevent} cc ON cc.id=beae.activityid
                                                   WHERE beae.companyid=:companyid
                                                   AND beae.userid != :myuserid
                                                   AND beae.userid IN ($myuserids)
                                                   AND (
                                                    cc.approvaltype in (2,3)
                                                    AND beae.tm_ok = 0 )
                                                   OR (
                                                    cc.approvaltype = 1
                                                    AND beae.manager_ok = 0)",
                                                    array('companyid' => $companyid, 'myuserid' => $USER->id))) {
                    return $userarray;
                }
            }

            // Get the users who need manager type approval.
            if ($approvaltype == 'both') {
                if ($userarray = $DB->get_records_sql("SELECT * FROM {block_iomad_approve_access}
                                                       WHERE companyid=:companyid
                                                       AND (tm_ok = 0 OR manager_ok = 0)
                                                       AND userid != :myuserid
                                                       AND userid IN ($myuserids)",
                                                       array('companyid' => $companyid, 'myuserid' => $USER->id))) {
                    return $userarray;
                }
            }
        }

        // Default return nothing.  We shouldn't get here.
        return array();
    }

    /**
     * Assigns an approved user to a training event.
     *
     * Inputs-
     *        $user = stdclass();
     *        $event = stdclass();
     *
     **/
    public static function register_user($user, $event) {
        global $DB;

        // Set up the trainingevent record.
        $trainingeventrecord = new stdclass();
        $trainingeventrecord->userid = $user->id;
        $trainingeventrecord->trainingeventid = $event->id;

        // Do we already have this?
        if (!$DB->get_record('trainingevent_users', array('userid' => $user->id, 'trainingeventid' => $event->id))) {

            // If not insert it.
            if (!$DB->insert_record('trainingevent_users', $trainingeventrecord)) {

                // Throw an error if that doesn't work.
                print_error(get_string('updatefailed', 'block_iomad_approve_access'));
            }
        }
    }
}
