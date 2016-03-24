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
 * email_events
 *
 * @package    local_email_events
 * @copyright  2014 GetSmarter {@link http://www.getsmarter.co.za}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Setup Moodle page
require_once($CFG->dirroot.'../../../config.php');

require_login();
require_capability('local/email_events:manage', context_system::instance());
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Email Event");
$PAGE->set_url($CFG->wwwroot.'/local/email_events/events/form.php');
$PAGE->requires->js_call_amd('local_email_events/email_events', 'event_form');
$PAGE->requires->css('/local/email_events/chosen.css');
$PAGE->requires->css('/local/email_events/style.css');

// Set incoming parameters
$id = optional_param('id', 0, PARAM_INT);

require_once("$CFG->libdir/formslib.php");
require_once('../classes/email_events_form.php');

$mform = new email_events_event_form();
$indexurl = new moodle_url('/local/email_events/events/index.php');

//Form processing and displaying
if ($mform->is_cancelled()) {

    // Form cancelled - redirect to index page
    redirect($indexurl);

} else if ($datafromform = $mform->get_data()) {

    // Form submitted - create or update
    if($datafromform->id) {
        $DB->update_record('local_email_events', $datafromform);
    } else {
    $id = $DB->insert_record('local_email_events', $datafromform);
    }
    redirect($indexurl);

} else {

    // Form displayed - display empty or populated form
    if($id) {
        $dataforform = $DB->get_record('local_email_events', array('id' => $id));

        if($dataforform) {
            $mform->set_data($dataforform);
        } else {
            throw new dml_exception("A record with id $id does not exist.");
        }
    }

}

// Render page
echo $OUTPUT->header();
echo $OUTPUT->heading('email_events Event');

echo html_writer::tag('p', 'Choose an event defined within Moodle or a plugin and then edit the Name, Status, Subject and Body you want to send to email_events when that event occurs. For the variables that you can use in the Subject and Body click the link below the selected event');

$eventlist = html_writer::link(new moodle_url('/report/eventlist/index.php'), 'Event List');
echo html_writer::tag('p', "See the $eventlist for details about all available events.");

$mform->display();

echo html_writer::tag('p', 'The Subject and Body field also has access to $moodle_event, $course, $user and $user_profile (custom profile fields).', array('class' => 'description'));

echo $OUTPUT->footer();
