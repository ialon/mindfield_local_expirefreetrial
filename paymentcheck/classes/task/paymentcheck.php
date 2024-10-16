<?php
namespace local_paymentcheck\task;
require_once(__DIR__ . '../../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/editlib.php');  
require_once($CFG->libdir . '/adminlib.php');
include_once($CFG->libdir . '/dml/moodle_database.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");
defined('MOODLE_INTERNAL') || die();

class paymentcheck extends \core\task\scheduled_task {
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'local_paymentcheck');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;
        $objUserEnrolmentReserve = $DB->get_records_sql( 'SELECT uer.* FROM {user_enrolments_reserve} uer WHERE uer.reservedat < UNIX_TIMESTAMP( CURRENT_TIMESTAMP - INTERVAL 5 MINUTE )' );
        foreach( $objUserEnrolmentReserve as $keyUER => $valUER ){
            $objEnrol = $DB->get_record_sql( 'SELECT e.* FROM {enrol} e WHERE e.id = ?', [ $valUER->enrolid ] );
            $courseId = $objEnrol->courseid;
            $objPaypalEnrol = $DB->get_record_sql( 'SELECT ep.* FROM {enrol_paypal} ep WHERE ep.courseid = ? AND ep.userid = ?', [ $courseId, $valUER->userid ] );
            $revertFreeTrial = 1;
            if( ( isset( $objPaypalEnrol->payment_status ) ) && ( $objPaypalEnrol->payment_status == "Completed" ) ){
                $revertFreeTrial = 0;
            }
            if( $revertFreeTrial == 1){
                $instance = $DB->get_record('enrol', array('id'=>$valUER->enrolid), '*', MUST_EXIST);
                $plugin = enrol_get_plugin($instance->enrol);
                $plugin->enrol_user($instance, $valUER->userid, null, $valUER->timestart, $valUER->timeend);
                $DB->execute("DELETE FROM {user_enrolments_reserve} WHERE id = :id", [ "id" => $valUER->id ] );
            }
        }
    }
}
?>