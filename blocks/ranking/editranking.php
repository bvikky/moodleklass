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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

global $DB,$SESSION;
$companyid = $SESSION->currenteditingcompany;
$courseid = required_param('courseid', PARAM_INT);
//$courseid = $this->courseid;
$params = [
    'companyid' => $companyid,
	'courseid'=>$courseid,
];
$context = context_system::instance();
require_login();
// Correct the navbar.
$linktext = get_string('edit_ranking', 'block_ranking');
$linkurl = new moodle_url('/blocks/ranking/editranking.php', $params);
$dashboardurl = new moodle_url('/my');
// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);
$output = $PAGE->get_renderer('block_ranking');
$redirectmessage = "Ranking points saved";

$companylist = new moodle_url('/my', $urlparams);
// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
if (empty($CFG->defaulthomepage)) {
    $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new moodle_url($CFG->wwwroot . '/my'));
}

$PAGE->navbar->add($linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), $params);
$returnurl = $baseurl;

$stredit = get_string('editranking', 'block_ranking');

$baseurl = new moodle_url('editranking.php', $params);
if (is_siteadmin($USER))
	{
		$actranking=$DB->get_records_sql("SELECT * FROM mdl_ranking_settings  WHERE companyid = $companyid AND courseid=$courseid");
	}
else{
	$actranking=$DB->get_records_sql("SELECT * FROM mdl_ranking_settings  WHERE companyid = $companyid AND courseid=$courseid");
	}

  $rankcount=count($actranking);
 
  if($rankcount!=0)
  {
	$rankingrecord = new stdClass;
  
	$rankingrecord->companyid=$companyid;
	$rankingrecord->courseid=$courseid;
  
	foreach ($actranking as $actrankings) 
	{
		$rankingrecord->id = $actrankings->id;
		if($actrankings->name == "rankingsize")
		$rankingrecord->rankingsize = $actrankings->value;
		if($actrankings->name == "assignpoints")
		$rankingrecord->assignpoints = $actrankings->value;
		if($actrankings->name == "assignmentpoints")
		$rankingrecord->assignmentpoints = $actrankings->value;
		if($actrankings->name == "bookpoints")
		$rankingrecord->bookpoints = $actrankings->value;
		if($actrankings->name == "chatpoints")
		$rankingrecord->chatpoints = $actrankings->value;
		if($actrankings->name == "choicepoints")
		$rankingrecord->choicepoints = $actrankings->value;
		if($actrankings->name == "customcertpoints")
		$rankingrecord->customcertpoints = $actrankings->value;
		if($actrankings->name == "datapoints")
		$rankingrecord->datapoints = $actrankings->value;
		if($actrankings->name == "feedbackpoints")
		$rankingrecord->feedbackpoints = $actrankings->value;
		if($actrankings->name == "folderpoints")
		$rankingrecord->folderpoints = $actrankings->value;
		if($actrankings->name == "forumpoints")
		$rankingrecord->forumpoints = $actrankings->value;
		if($actrankings->name == "glossarypoints")
		$rankingrecord->glossarypoints = $actrankings->value;
		if($actrankings->name == "h5pactivitypoints")
		$rankingrecord->h5pactivitypoints = $actrankings->value;
		if($actrankings->name == "imscppoints")
		$rankingrecord->imscppoints = $actrankings->value;
		if($actrankings->name == "iomadcertificatepoints")
		$rankingrecord->iomadcertificatepoints = $actrankings->value;
		if($actrankings->name == "labelpoints")
		$rankingrecord->labelpoints = $actrankings->value;
		if($actrankings->name == "lessonpoints")
		$rankingrecord->lessonpoints = $actrankings->value;
		if($actrankings->name == "ltipoints")
		$rankingrecord->ltipoints = $actrankings->value;
		if($actrankings->name == "pagepoints")
		$rankingrecord->pagepoints = $actrankings->value;
		if($actrankings->name == "quizpoints")
		$rankingrecord->quizpoints = $actrankings->value;
		if($actrankings->name == "resourcepoints")
		$rankingrecord->resourcepoints = $actrankings->value;
		if($actrankings->name == "scormpoints")
		$rankingrecord->scormpoints = $actrankings->value;
		if($actrankings->name == "surveypoints")
		$rankingrecord->surveypoints = $actrankings->value;
		if($actrankings->name == "trainingeventpoints")
		$rankingrecord->trainingeventpoints = $actrankings->value;
		if($actrankings->name == "urlpoints")
		$rankingrecord->urlpoints = $actrankings->value;
		if($actrankings->name == "wikipoints")
		$rankingrecord->wikipoints = $actrankings->value;
		if($actrankings->name == "workshoppoints")
		$rankingrecord->workshoppoints = $actrankings->value;
		if($actrankings->name == "zoompoints")
		$rankingrecord->zoompoints = $actrankings->value;
		if($actrankings->name == "defaultpoints")
		$rankingrecord->defaultpoints = $actrankings->value;
		if($actrankings->name == "enable_multiple_quizz_attempts")
		$rankingrecord->enable_multiple_quizz_attempts = $actrankings->value;
		
	}
  }
// Set up the form.
$mform = new block_ranking\forms\editranking_form($PAGE->url,$companyid,$rankingrecord);
$mform->set_data($rankingrecord);
$data1 = new stdClass;
if ($mform->is_cancelled()) {
    //go back to dashboard
	redirect($dashboardurl);
    die;
} else if($fromform = $mform->get_data()) {
	$formdata=(array)$mform->get_data();
	$data=array_slice($formdata,2,-1);
	$datacount=count($data);
	$i=0;
	foreach ($data as $key => $new)
	{
		$data1->courseid=$formdata['courseid'];
		$data1->name=$key;
		$data1->value=$new;
		$data1->companyid=$formdata['companyid'];
		if($rankcount>0)
		{
			$rankid = $DB->get_record('ranking_settings', array('companyid' => $formdata['companyid'],$formdata['courseid'],'name'=>$key));
			$data1->id=$rankid->id;
				if($added =$DB->update_record('ranking_settings', $data1))
				{
					$i++;
				}
		}
		else
		{
			if($added = $DB->insert_record('ranking_settings', $data1))
			{
				$i++;
			}
		}
	}
	if($datacount==$i){
		redirect($linkurl, $redirectmessage, \core\output\notification::NOTIFY_SUCCESS);
	}

 /* echo '<br> insert data<pre>';
  print_r($data1);
  exit;*/
}
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

?>