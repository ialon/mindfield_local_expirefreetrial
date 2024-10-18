<?php
namespace local_expirefreetrial\task;
require_once(__DIR__ . '../../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/editlib.php');  
require_once($CFG->libdir . '/adminlib.php');
include_once($CFG->libdir . '/dml/moodle_database.php');
defined('MOODLE_INTERNAL') || die();

class expirefreetrial extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_expirefreetrial');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;
        $objPluginValues = $DB->get_records_sql("SELECT * FROM {config_plugins} WHERE plugin = :plugin ", [
            'plugin' => 'local_expirefreetrial',
        ]);
        $startSemester = "";
        $endTrial = "";
        foreach( $objPluginValues as $keyPV => $valPV ){
            if( $valPV->name == "s_local_expirefreetrial_startsemester" ){
                $startSemester = $valPV->value;
            }
            elseif( $valPV->name == "s_local_expirefreetrial_endtrial" ){
                $endTrial = $valPV->value;
            }
        }
        $strToTimeStartSemester = strtotime($startSemester);
        $strToTimeStartdate30days = strtotime('+30 days', strtotime($startSemester));
        $strToendTrial = strtotime($endTrial." 23:59:00");
        $objCourses = $DB->get_record_sql( 'SELECT GROUP_CONCAT(id) as courseids FROM {course} WHERE startdate >= ? AND startdate <= ?', [ $strToTimeStartSemester, $strToTimeStartdate30days] );
	$arrcourseids = explode(",", $objCourses->courseids, -1);
	$cnt = 0;
	$strQry = "(";
	foreach( $arrcourseids as $keyaci => $valaci ){
            if( $cnt == 0 ){
	       $strQry .= " courseid = $valaci ";
	    }
	    else{
               $strQry .= " OR courseid = $valaci ";
	    }
	    $cnt++;
	}
	//file_put_contents("/opt/studentfirstmedia/moodle/cache/log.txt", "strQry1=>".print_r($strQry, true)."<=\n", FILE_APPEND);
	if( $cnt == 0 ){
	   $strQry .= " 1 = 1 ";
	}
	$strQry .= ")";
	//$strQry = "( courseid = 211 )";
	//file_put_contents("/opt/studentfirstmedia/moodle/cache/log.txt", "strqry=>".print_r($strQry, true)."<=\n", FILE_APPEND);
	$objEnrols = $DB->get_record_sql("SELECT GROUP_CONCAT(id) as enrolids FROM {enrol} WHERE $strQry AND enrol = ? AND enrolenddate < ? ", [ 'self', $strToTimeStartdate30days ] );
//	file_put_contents("/opt/studentfirstmedia/moodle/cache/log.txt", "LINE=>".__LINE__."=>objEnrols =>".print_r( $objEnrols, true )."<=\n", FILE_APPEND);
	//	file_put_contents("/opt/studentfirstmedia/moodle/cache/log.txt", "LINE=>".__LINE__."=>objEnrolCourse=>".print_r( $objEnrolCourse, true )."<=", FILE_APPEND);
	$arrenrolids = explode(",", $objEnrols->enrolids);
	$cntEnrol = 0;
	$strQryEnrol = "(";
	foreach( $arrenrolids as $keye => $vale ){
	     if( $cntEnrol == 0 ){
	     	$strQryEnrol .= " enrolid = $vale ";
	     }
	     else{
	     	$strQryEnrol .= " OR enrolid = $vale ";
	     }
	     $cntEnrol++;
	}
	if( $cntEnrol == 0 ){
	    $strQryEnrol .= " 1 = 1 ";
	}
	$strQryEnrol .= ")";
        $DB->execute("UPDATE {user_enrolments} SET timeend = ? WHERE $strQryEnrol", [ $strToendTrial ] );
    }
}
?>
