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

namespace block_ranking\forms;

defined('MOODLE_INTERNAL') || die;

use \iomad;
use \company;
use \moodle_url;
require_once('lib.php');
require_login();
class editranking_form extends \moodleform {
	 protected $companyid;
	 protected $rankingrecord;
	 
	  public function __construct($actionurl,$companyid,$rankingrecord) {
        global $DB,$SESSION;
		
//echo "123456<pre>";print_r($rankingrecord);exit;
//echo $SESSION->currenteditingcompany;exit;
		$this->companyid = $SESSION->currenteditingcompany;
		$this->rankingrecord = $rankingrecord;
       
        parent::__construct($actionurl);
    }
		public function definition() {
        global $CFG, $PAGE, $DB;
        $context = \context_system::instance();

        $mform = & $this->_form;

        $strnumeric = get_string('numeric');
		
		$mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
		
		$mform->addElement('hidden', 'companyid', $this->companyid);
        $mform->setType('companyid', PARAM_INT);
		
		$mform->addElement('text', 'rankingsize', get_string('rankingsize', 'block_ranking'));
		$mform->setType('rankingsize', PARAM_INT);  
        $mform->setDefault('rankingsize', "10");
        $mform->addRule('rankingsize', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'assignpoints', get_string('assignpoints', 'block_ranking'));
		$mform->setType('assignpoints', PARAM_INT);  
        $mform->setDefault('assignpoints', "2");
        $mform->addRule('assignpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'assignmentpoints', get_string('assignmentpoints', 'block_ranking'));
		$mform->setType('assignmentpoints', PARAM_INT);  
        $mform->setDefault('assignmentpoints', "2");
        $mform->addRule('assignmentpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'bookpoints', get_string('bookpoints', 'block_ranking'));
		$mform->setType('bookpoints', PARAM_INT);  
        $mform->setDefault('bookpoints', "2");
        $mform->addRule('bookpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'chatpoints', get_string('chatpoints', 'block_ranking'));
		$mform->setType('chatpoints', PARAM_INT);  
        $mform->setDefault('chatpoints', "2");
        $mform->addRule('chatpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'choicepoints', get_string('choicepoints', 'block_ranking'));
		$mform->setType('choicepoints', PARAM_INT);  
        $mform->setDefault('choicepoints', "2");
        $mform->addRule('choicepoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'customcertpoints', get_string('customcertpoints', 'block_ranking'));
		$mform->setType('customcertpoints', PARAM_INT);  
        $mform->setDefault('customcertpoints', "2");
        $mform->addRule('customcertpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'datapoints', get_string('datapoints', 'block_ranking'));
		$mform->setType('datapoints', PARAM_INT);  
        $mform->setDefault('datapoints', "2");
        $mform->addRule('datapoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'feedbackpoints', get_string('feedbackpoints', 'block_ranking'));
		$mform->setType('feedbackpoints', PARAM_INT);  
        $mform->setDefault('feedbackpoints', "2");
        $mform->addRule('feedbackpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'folderpoints', get_string('folderpoints', 'block_ranking'));
		$mform->setType('folderpoints', PARAM_INT);  
        $mform->setDefault('folderpoints', "2");
        $mform->addRule('folderpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'forumpoints',get_string('forumpoints', 'block_ranking'));
		$mform->setType('forumpoints', PARAM_INT);  
        $mform->setDefault('forumpoints', "2");
        $mform->addRule('forumpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'glossarypoints',get_string('glossarypoints', 'block_ranking'));
		$mform->setType('glossarypoints', PARAM_INT);  
        $mform->setDefault('glossarypoints', "2");
        $mform->addRule('glossarypoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'h5pactivitypoints',get_string('h5pactivitypoints', 'block_ranking'));
		$mform->setType('h5pactivitypoints', PARAM_INT);  
        $mform->setDefault('h5pactivitypoints', "2");
        $mform->addRule('h5pactivitypoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'imscppoints',get_string('imscppoints', 'block_ranking'));
		$mform->setType('imscppoints', PARAM_INT);  
        $mform->setDefault('imscppoints', "2");
        $mform->addRule('imscppoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'iomadcertificatepoints',get_string('iomadcertificatepoints', 'block_ranking'));
		$mform->setType('iomadcertificatepoints', PARAM_INT);  
        $mform->setDefault('iomadcertificatepoints', "2");
        $mform->addRule('iomadcertificatepoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'labelpoints',get_string('labelpoints', 'block_ranking'));
		$mform->setType('labelpoints', PARAM_INT);  
        $mform->setDefault('labelpoints', "2");
        $mform->addRule('labelpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'lessonpoints',get_string('lessonpoints', 'block_ranking'));
		$mform->setType('lessonpoints', PARAM_INT);  
        $mform->setDefault('lessonpoints', "2");
        $mform->addRule('lessonpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'ltipoints',get_string('ltipoints', 'block_ranking'));
		$mform->setType('ltipoints', PARAM_INT);  
        $mform->setDefault('ltipoints', "2");
        $mform->addRule('ltipoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'pagepoints',get_string('pagepoints', 'block_ranking'));
		$mform->setType('pagepoints', PARAM_INT);  
        $mform->setDefault('pagepoints', "2");
        $mform->addRule('pagepoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'quizpoints',get_string('quizpoints', 'block_ranking'));
		$mform->setType('quizpoints', PARAM_INT);  
        $mform->setDefault('quizpoints', "2");
        $mform->addRule('quizpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'resourcepoints', get_string('resourcepoints','block_ranking'));
		$mform->setType('resourcepoints', PARAM_INT);  
        $mform->setDefault('resourcepoints', "2");
        $mform->addRule('resourcepoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'scormpoints', get_string('scormpoints','block_ranking'));
		$mform->setType('scormpoints', PARAM_INT);  
        $mform->setDefault('scormpoints', "2");
        $mform->addRule('scormpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'surveypoints', get_string('surveypoints','block_ranking'));
		$mform->setType('surveypoints', PARAM_INT);  
        $mform->setDefault('surveypoints', "2");
        $mform->addRule('surveypoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'trainingeventpoints', get_string('trainingeventpoints','block_ranking'));
		$mform->setType('trainingeventpoints', PARAM_INT);  
        $mform->setDefault('trainingeventpoints', "2");
        $mform->addRule('trainingeventpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'urlpoints', get_string('urlpoints','block_ranking'));
		$mform->setType('urlpoints', PARAM_INT);  
        $mform->setDefault('urlpoints', "2");
        $mform->addRule('urlpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'wikipoints', get_string('wikipoints','block_ranking'));
		$mform->setType('wikipoints', PARAM_INT);  
        $mform->setDefault('wikipoints', "2");
        $mform->addRule('wikipoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'workshoppoints',get_string('workshoppoints', 'block_ranking'));
		$mform->setType('workshoppoints', PARAM_INT);  
        $mform->setDefault('workshoppoints', "2");
        $mform->addRule('workshoppoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'zoompoints',get_string('zoompoints', 'block_ranking'));
		$mform->setType('zoompoints', PARAM_INT);  
        $mform->setDefault('zoompoints', "2");
        $mform->addRule('zoompoints', null, 'numeric', null, 'client');
		
		$mform->addElement('text', 'defaultpoints',get_string('defaultpoints', 'block_ranking'));
		$mform->setType('defaultpoints', PARAM_INT);  
        $mform->setDefault('defaultpoints', "2");
        $mform->addRule('defaultpoints', null, 'numeric', null, 'client');
		
		$mform->addElement('select', 'enable_multiple_quizz_attempts', get_string('enable_multiple_quizz_attempts', 'block_ranking'),array(
                        '1' => get_string('yes', 'block_ranking'),
                        '0' => get_string('no', 'block_ranking')
                    ));
		$mform->setDefault('enable_multiple_quizz_attempts', $plugin);
		$this->add_action_buttons();
	}
	public function get_data() {
        $data = parent::get_data();
        return $data;
    }
}
?>