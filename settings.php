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
 * Plugin settings for the local_expirefreetrial plugin.
 *
 * @package   local_expirefreetrial
 * Nisarg Patel
 * 21st January 2024
 */
require_once(__DIR__ . '/../../config.php');
// Ensure the configurations for this site are set
if ($hassiteconfig) {
    $settings = new admin_settingpage('local_expirefreetrial', 'Free Access');
    $ADMIN->add('localplugins', $settings);
    // Add a setting field to the settings for this page
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_expirefreetrial/s_local_expirefreetrial_startsemester',
        // This is the friendly title for the config, which will be displayed
        'Date of Start of Semester',
        // This is helper text for this config field
        'yyyy-mm-dd',
        // This is the default value
        NULL,
        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        // This is the reference you will use to your configuration
        'local_expirefreetrial/s_local_expirefreetrial_endtrial',
        // This is the friendly title for the config, which will be displayed
        'Date of End of Trial Period',
        // This is helper text for this config field
        'yyyy-mm-dd',
        // This is the default value
        NULL,
        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
}
?>
<?php
/*
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
$(function () { 
    $("#id_s_local_expirefreetrial_s_local_expirefreetrial_startsemester").datepicker({  
        autoclose: true,  
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    });
    $("#id_s_local_expirefreetrial_s_local_expirefreetrial_endtrial").datepicker({  
        autoclose: true,  
        todayHighlight: true,
        format: 'yyyy-mm-dd'
    }); 
}); 
</script>
*/
?>
