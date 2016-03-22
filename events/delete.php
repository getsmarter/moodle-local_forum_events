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
 * Segment
 *
 * @package    local_segment
 * @copyright  2014 GetSmarter {@link http://www.getsmarter.co.za}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'../../../config.php');

require_login();
require_capability('local/segment:manage', context_system::instance());
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Delete Segment Event");
$PAGE->set_url($CFG->wwwroot.'/local/segment/events/delete.php');

// Set incoming parameters
$id = required_param('id', PARAM_INT);
$delete = optional_param('delete', '', PARAM_ALPHANUM);

$event = $DB->get_record('local_segment', array('id' => $id));
$indexurl = new moodle_url('/local/segment/events/index.php');

// Render page
echo $OUTPUT->header();
echo $OUTPUT->heading('Delete Segment Event');

if($event) {
    if ($delete === md5($event->properties)) {
        require_sesskey();
        $DB->delete_records('local_segment', array('id' => $id));
        redirect($indexurl);
    } else {
        $message = "Are you sure you want to delete this segment event?<br /><br />$event->name<br />$event->event";
        $deleteurl = new moodle_url('/local/segment/events/delete.php', array('id' => $event->id, 'delete' => md5($event->properties)));
        echo $OUTPUT->confirm($message, $deleteurl, $indexurl);
    }
} else {
    throw new dml_exception("A record with id $id does not exist.");
}

echo $OUTPUT->footer();
