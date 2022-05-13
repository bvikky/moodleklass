<?php
// This file is part of Ranking block for Moodle - http://moodle.org/
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
 * Ranking block global lib
 *
 * @package    block_ranking
 * @copyright  2017 Willian Mano http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define ('DEFAULT_POINTS', 2);

// Store the courses contexts.
$coursescontexts = array();

/**
 * Return the list of students in the course ranking
 *
 * @param int $limit
 * @return mixed
 */
function block_ranking_get_students($limit = null) {
    global $COURSE, $DB, $PAGE, $SESSION;
	$companyid = $SESSION->currenteditingcompany;

	// Get block ranking configuration.
    //$cfgranking = get_config('block_ranking');
	//$cfgranking = get_company_config('block_ranking',$companyid);        //v582 25022022 config to company_config
	
	  $cfgranking = $DB->get_records_menu('ranking_settings', array('courseid' => $plugin, 'companyid'=>$companyid), '', 'name,value');
	  
	// Get limit from default configuration or instance configuration.
    if (!$limit) {
        if (isset($cfgranking->rankingsize) && trim($cfgranking->rankingsize) != '') {
            $limit = $cfgranking->rankingsize;
        } else {
            $limit = 10;
        }
    }

    $context = $PAGE->context;

    $userfields = user_picture::fields('u', array('username'));
    $sql = "SELECT
                DISTINCT $userfields, r.points
            FROM
                {user} u
            INNER JOIN {role_assignments} a ON a.userid = u.id
            INNER JOIN {ranking_points} r ON r.userid = u.id AND r.courseid = :r_courseid
            INNER JOIN {context} c ON c.id = a.contextid
            WHERE a.contextid = :contextid
            AND a.userid = u.id
            AND a.roleid = :roleid
            AND c.instanceid = :courseid
            AND r.courseid = :crsid
            ORDER BY r.points DESC, u.firstname ASC
            LIMIT " . $limit;
    $params['contextid'] = $context->id;
    $params['roleid'] = 5;
    $params['courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;
    $params['r_courseid'] = $COURSE->id;

    $users = array_values($DB->get_records_sql($sql, $params));

    return $users;
}

/**
 * Get the students points based on a time interval
 *
 * @param int $limit
 * @param int $datestart
 * @param int $dateend
 * @return mixed
 */
function block_ranking_get_students_by_date($limit = 0, $datestart, $dateend) {
    global $COURSE, $DB, $PAGE, $SESSION;
	$companyid = $SESSION->currenteditingcompany;
    // Get block ranking configuration.
    $cfgranking = get_config('block_ranking');
	 //$cfgranking = get_company_config('block_ranking',$companyid);     //v582 25022022 config to company_config

    // Get limit from default configuration or instance configuration.
    if (!$limit) {
        if (isset($cfgranking->rankingsize) && trim($cfgranking->rankingsize) != '') {
            $limit = $cfgranking->rankingsize;
        } else {
            $limit = 10;
        }
    }

    $context = $PAGE->context;

    $userfields = user_picture::fields('u', array('username'));
    $sql = "SELECT
                DISTINCT $userfields,
                sum(rl.points) as points
            FROM
                {user} u
            INNER JOIN {role_assignments} a ON a.userid = u.id
            INNER JOIN {ranking_points} r ON r.userid = u.id AND r.courseid = :r_courseid
            INNER JOIN {ranking_logs} rl ON rl.rankingid = r.id
            INNER JOIN {context} c ON c.id = a.contextid
            WHERE a.contextid = :contextid
            AND a.userid = u.id
            AND a.roleid = :roleid
            AND c.instanceid = :courseid
            AND r.courseid = :crsid
            AND rl.timecreated BETWEEN :weekstart AND :weekend
            GROUP BY u.id
            ORDER BY points DESC, u.firstname ASC
            LIMIT " . $limit;

    $params['contextid'] = $context->id;
    $params['roleid'] = 5;
    $params['courseid'] = $COURSE->id;
    $params['r_courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;
    $params['weekstart'] = $datestart;
    $params['weekend'] = $dateend;

    $users = array_values($DB->get_records_sql($sql, $params));

    return $users;
}

/**
 * Build the ranking table to be viewd in the course
 *
 * @param array $rankinglastmonth List of students of the last month ranking
 * @param array $rankinglastweek List of students of the last week ranking
 * @param array $rankinggeral List of students to be print in ranking block
 * @return string
 */
function block_ranking_print_students($rankinglastmonth, $rankinglastweek, $rankinggeral) {
    global $PAGE,$USER, $OUTPUT;

    $tablelastweek = generate_table($rankinglastweek);
    $tablelastmonth = generate_table($rankinglastmonth);
    $tablegeral = generate_table($rankinggeral);
		//week base user points
		if(empty($tablelastweek['user']))
		{
			$weekposition='-';
            $weekuserimage=$OUTPUT->user_picture($USER, array('size' => 120, 'alttext' => false));
			$weekusername=$USER->firstname;
            $weekuserpoints='-';			
		}
		if(!empty($tablelastweek['user']))
		{
			$weekposition=$tablelastweek['user']['position'];
            $weekuserimage=$tablelastweek['user']['userimage'];
			$weekusername=$tablelastweek['user']['username'];
            $weekuserpoints=$tablelastweek['user']['userpoints'];			
		}
		
		//month base user points
		if(empty($tablelastmonth['user']))
		{
			$monthposition='-';
            $monthuserimage=$OUTPUT->user_picture($USER, array('size' => 120, 'alttext' => false));
			$monthusername=$USER->firstname;
            $monthuserpoints='-';			
		}
		if(!empty($tablelastmonth['user']))
		{
			$monthposition=$tablelastmonth['user']['position'];
            $monthuserimage=$tablelastmonth['user']['userimage'];
			$monthusername=$tablelastmonth['user']['username'];
            $monthuserpoints=$tablelastmonth['user']['userpoints'];			
		}
		//general user points
		if(empty($tablegeral['user']))
		{
			$genrlposition='-';
            $genrluserimage=$OUTPUT->user_picture($USER, array('size' => 120, 'alttext' => false));
			$genrlusername=$USER->firstname;
            $genrluserpoints='-';			
		}
		if(!empty($tablegeral['user']))
		{
			$genrlposition=$tablegeral['user']['position'];
            $genrluserimage=$tablegeral['user']['userimage'];
			$genrlusername=$tablegeral['user']['username'];
            $genrluserpoints=$tablegeral['user']['userpoints'];			
		}
		
		

    return ' <div id="ranking-tabs">
			
					<ul>
						<li><a href="#semanal" class="btn btn-log btn-block btn-thm2">'.get_string('weekly', 'block_ranking').'</a></li>
						<li><a href="#mensal" class="btn btn-log btn-block btn-thm2">'.get_string('monthly', 'block_ranking').'</a></li>
						<li><a href="#geral" class="btn btn-log btn-block btn-thm2">'.get_string('general', 'block_ranking').'</a></li>
					</ul>
				
                <div>
                    <div id="semanal">
						<div class="ranking-user">
							<div class="userimage">'.$weekuserimage.'</div>
							<div class="row">
								<div class="col-md-4 user-details">
									<p class="rank-details">Position</p><p class="rank-details">'.$weekposition.'</p>
								</div>
								<div class="col-md-4 user-details">'.$weekusername.'</div>
								<div class="col-md-4 user-details">
									<p class="rank-details">Points</p><p class="rank-details">'.$weekuserpoints.'</p>
								</div>
							</div>
						</div>
						<div class="rank-table">'.$tablelastweek['ranklist'].'</div>
					</div>
					
                    <div id="mensal">
						<div class="ranking-user">
							<div class="userimage">'.$monthuserimage.'</div>
							<div class="row">
								<div class="col-md-4 user-details">
									<p class="rank-details">Position</p><p class="rank-details">'.$monthposition.'</p>
								</div>
								<div class="col-md-4 user-details">'.$monthusername.'</div>
								<div class="col-md-4 user-details">
									<p class="rank-details">Points</p><p class="rank-details">'.$monthuserpoints.'</p>
								</div>
							</div>
						</div>
						<div class="rank-table">'.$tablelastmonth['ranklist'].'</div>
					</div>
					
                    <div id="geral">
						<div class="ranking-user">
							<div class="userimage">'.$genrluserimage.'</div>
							<div class="row">
								<div class="col-md-4 user-details">
									<p class="rank-details">Position</p><p class="rank-details">'.$genrlposition.'</p>
								</div>
								<div class="col-md-4 user-details">'.$genrlusername.'</div>
								<div class="col-md-4 user-details">
									<p class="rank-details">Points</p><p class="rank-details">'.$genrluserpoints.'</p>
								</div>
							</div>
						</div>
						<div class="rank-table">'.$tablegeral['ranklist'].'</div>
					</div>
                </div>
            </div>';
}

/**
 * Print the student individual ranking points
 *
 * @return string
 */
function block_ranking_print_individual_ranking() {
    global $USER, $COURSE;

    if (!is_student($USER->id)) {
        return '';
    }

    $weekstart = strtotime(date('d-m-Y', strtotime('-'.date('w').' days')));
    $lastweekpoints = block_ranking_get_student_points_by_date($USER->id, $weekstart, time());
    $lastweekpoints = $lastweekpoints->points != null ? $lastweekpoints->points : '0';
    $lastweekpoints = $lastweekpoints . " " . strtolower(get_string('table_points', 'block_ranking'));

    $monthstart = strtotime(date('Y-m-01'));
    $lastmonthpoints = block_ranking_get_student_points_by_date($USER->id, $monthstart, time());
    $lastmonthpoints = $lastmonthpoints->points != null ? $lastmonthpoints->points : '0';
	$lastmonthposition= $lastmonthpoints->points;
    $lastmonthpoints = $lastmonthpoints . " " . strtolower(get_string('table_points', 'block_ranking'));
	
    $totalpoints = block_ranking_get_student_points($USER->id);
    $totalpoints = $totalpoints->points != null ? $totalpoints->points : '0';
    $totalpoints = $totalpoints . " " . strtolower(get_string('table_points', 'block_ranking'));
    $table = new html_table();
    $table->attributes = array("class" => "rankingTable table table-striped generaltable");
    $table->head = array(
                        get_string('weekly', 'block_ranking'),
                        get_string('monthly', 'block_ranking'),
                        get_string('general', 'block_ranking')
                    );

    $row = new html_table_row();
    $row->cells = array($lastweekpoints, $lastmonthpoints, $totalpoints);
    $table->data[] = $row;

    $individualranking = html_writer::table($table);
    return "<h4>".get_string('your_score', 'block_ranking').":</h4>" . $individualranking;
}

/**
 * Get the student points
 *
 * @param int $userid
 * @return mixed
 */
function block_ranking_get_student_points($userid) {
    global $COURSE, $DB;

    $sql = "SELECT
                sum(rl.points) as points
            FROM
                {user} u
            INNER JOIN {ranking_points} r ON r.userid = u.id AND r.courseid = :courseid
            INNER JOIN {ranking_logs} rl ON rl.rankingid = r.id
            WHERE u.id = :userid
            AND r.courseid = :crsid";

    $params['userid'] = $userid;
    $params['courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;

    return $DB->get_record_sql($sql, $params);
}

/**
 * Get the student points based on a time interval
 *
 * @param int $userid
 * @param int $datestart
 * @param int $dateend
 * @return mixed
 */
function block_ranking_get_student_points_by_date($userid, $datestart, $dateend) {
    global $COURSE, $DB;

    $sql = "SELECT
                sum(rl.points) as points
            FROM
                {user} u
            INNER JOIN {ranking_points} r ON r.userid = u.id AND r.courseid = :courseid
            INNER JOIN {ranking_logs} rl ON rl.rankingid = r.id
            WHERE u.id = :userid
            AND r.courseid = :crsid
            AND rl.timecreated BETWEEN :weekstart AND :weekend";

    $params['userid'] = $userid;
    $params['courseid'] = $COURSE->id;
    $params['crsid'] = $COURSE->id;
    $params['weekstart'] = $datestart;
    $params['weekend'] = $dateend;

    return $DB->get_record_sql($sql, $params);
}

/**
 * Return a table of ranking based on data passed
 *
 * @param mixed $data
 * @return mixed
 */
function generate_table($data) {
    global $USER, $OUTPUT;
		$datacount=count($data);
    if (empty($data)) {
        	$ranking['ranklist']=get_string('nostudents', 'block_ranking');
			$userrank= array(
                        'position'=>'-',
                        'userimage'=>$OUTPUT->user_picture($USER, array('size' => 120, 'alttext' => false)),
						'username'=>$USER->firstname,
                        'userpoints'=>'-'
                    );
			$ranking['user']=$userrank;
		return $ranking;
    }

    $table = new html_table();
    $table->attributes = array("class" => "rankingTable table table-striped generaltable");
	
	if (($datacount==1) && ($data[0]->id == $USER->id)) {
    $table->head ='';
	}
	
	else{
		 $table->head = array(
                        get_string('table_position', 'block_ranking'),
                        get_string('table_name', 'block_ranking'),
                        get_string('table_points', 'block_ranking')
                    );
	}
    $lastpos = 1;
    $lastpoints = current($data)->points;
    for ($i = 0; $i < count($data); $i++) {
        $row = new html_table_row();
        if ($lastpoints > $data[$i]->points) {
            $lastpos++;
            $lastpoints = $data[$i]->points;
        }
		if ($data[$i]->id != $USER->id) {
	
				$row->cells = array(
								$lastpos,
								$OUTPUT->user_picture($data[$i], array('size' => 24, 'alttext' => false)) . ' '.$data[$i]->firstname,
								$data[$i]->points ?: '-'
							);
		}
			
        // Verify if the logged user is one user in ranking.
        if ($data[$i]->id == $USER->id) {
			$userrank= array(
                        'position'=>$lastpos,
                        'userimage'=>$OUTPUT->user_picture($data[$i], array('size' => 120, 'alttext' => false)),
						'username'=>$data[$i]->firstname,
                        'userpoints'=>$data[$i]->points ?: '-'
                    );
            //$row->attributes = array('class' => 'itsme');
        }
		
        $table->data[] = $row;
		//$ranking['ranklist']=html_writer::table($table);
		//$ranking['user']=$userrank;
    }
		
	$ranking['user']=$userrank;
	$ranking['ranklist']=html_writer::table($table);	
    //return html_writer::table($table);
	return $ranking;
}

/**
 * Get the groups total points
 *
 * @return array
 */
function block_ranking_get_total_points_by_group() {
    global $COURSE, $DB;

    $sql = "SELECT
              g.name as groupname, SUM(rp.points) as points
            FROM {ranking_points} rp
            INNER JOIN {user} u ON u.id = rp.userid
            INNER JOIN {groups_members} gm ON gm.userid = rp.userid
            INNER JOIN {groups} g ON g.id = gm.groupid
            WHERE rp.courseid = :courseid
            GROUP BY g.name";

    $params['courseid'] = $COURSE->id;

    return array_values($DB->get_records_sql($sql, $params));
}

/**
 * Return the total of students of a group
 *
 * @return array
 */
function block_ranking_get_total_students_by_group() {
    global $COURSE, $DB;

    $sql = "SELECT
            g.name as groupname, count(g.name) as qtd
            FROM mdl_ranking_points rp
            INNER JOIN mdl_user u ON u.id = rp.userid
            INNER JOIN mdl_groups_members gm ON gm.userid = rp.userid
            INNER JOIN mdl_groups g ON g.id = gm.groupid
            WHERE rp.courseid = :courseid
            GROUP BY g.name";

    $params['courseid'] = $COURSE->id;

    return array_values($DB->get_records_sql($sql, $params));
}

/**
 * Get the groups total points
 *
 * @return mixed
 */
function block_ranking_get_average_points_by_group() {
    global $COURSE, $DB;

    $groups = block_ranking_get_total_points_by_group();

    $groupsmembersnumber = block_ranking_get_total_students_by_group();

    foreach ($groups as $key => $value) {
        foreach ($groupsmembersnumber as $group) {
            if ($value->groupname == $group->groupname) {
                $groups[$key]->points = $value->points / $group->qtd;
                continue 2;
            }
        }
    }

    return $groups;
}

/**
 * Return the group points evolution
 *
 * @param int $groupid
 * @return array
 */
function block_ranking_get_points_evolution_by_group($groupid) {
    global $DB;

    $sql = "SELECT
              rl.id, rl.points, rl.timecreated
            FROM {ranking_logs} rl
            INNER JOIN {ranking_points} rp ON rp.id = rl.rankingid
            INNER JOIN {groups_members} gm ON gm.userid = rp.userid
            INNER JOIN {groups} g ON g.id = gm.groupid
            WHERE g.id = :groupid AND rl.timecreated > :lastweek";

    $lastweek = time() - (7 * 24 * 60 * 60);

    $params['groupid'] = $groupid;
    $params['lastweek'] = $lastweek;

    return array_values($DB->get_records_sql($sql, $params));
}

/**
 * Return a graph of groups points evolution
 *
 * @param int $groupid
 * @return \core\chart_bar
 */
function block_ranking_create_group_points_evolution_chart($groupid) {
    $records = block_ranking_get_points_evolution_by_group($groupid);

    $pointsbydate = [];

    // Pega o primeiro registro, tira do array e soma os pontos na data.
    if (count($records)) {
        $firstrecord = array_shift($records);
        $lastdate = date('d-m-Y', $firstrecord->timecreated);

        $pointsbydate[$lastdate] = $firstrecord->points;
    }

    // Percorre os demais registros.
    if (count($records)) {
        foreach ($records as $points) {
            $currentdate = date('d-m-Y', $points->timecreated);

            if ($lastdate != $currentdate && !array_key_exists($currentdate, $pointsbydate)) {
                $lastdate = $currentdate;

                // Cria novo indice de novo data com valor zero.
                $pointsbydate[$lastdate] = 0;
            }

            $pointsbydate[$lastdate] += $points->points;
        }
    }

    if (empty($pointsbydate)) {
        return '';
    }

    $chart = new \core\chart_line(); // Create a bar chart instance.
    $chart->set_smooth(true);
    $series = new \core\chart_series(get_string('graph_group_evolution_title', 'block_ranking'), array_values($pointsbydate));
    $series->set_type(\core\chart_series::TYPE_LINE);
    $chart->add_series($series);
    $chart->set_labels(array_keys($pointsbydate));

    return $chart;
}

/**
 * Return a graph of groups points
 *
 * @return \core\chart_bar
 */
function block_ranking_create_groups_points_chart() {
    $groups = block_ranking_get_total_points_by_group();

    $labels = [];
    $values = [];
    foreach ($groups as $key => $value) {
        $labels[] = $value->groupname;
        $values[] = $value->points;
    }

    $chart = new \core\chart_bar(); // Create a bar chart instance.
    $series = new \core\chart_series(get_string('graph_groups', 'block_ranking'), $values);
    $chart->add_series($series);
    $chart->set_labels($labels);

    return $chart;
}

/**
 * Return a graph of groups points average
 *
 * @return \core\chart_bar
 */
function block_ranking_create_groups_points_average_chart() {
    $groups = block_ranking_get_average_points_by_group();

    $labels = [];
    $values = [];
    foreach ($groups as $key => $value) {
        $labels[] = $value->groupname;
        $values[] = $value->points;
    }

    $chart = new \core\chart_bar(); // Create a bar chart instance.
    $series = new \core\chart_series(get_string('graph_groups_avg', 'block_ranking'), $values);
    $chart->add_series($series);
    $chart->set_labels($labels);

    return $chart;
}

/**
 * Verify if the user is a student
 *
 * @param int $userid
 * @return bool
 */
function is_student($userid) {
    return user_has_role_assignment($userid, 5);
}