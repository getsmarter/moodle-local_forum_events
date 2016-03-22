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

// Setup Moodle page
require_once('../../../config.php');
require_once('../classes/segment_event.php');

require_login();
require_capability('local/segment:view', context_system::instance());
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title("Segment Events");
$PAGE->set_url($CFG->wwwroot.'/local/segment/events/index.php');

// Render page
echo $OUTPUT->header();
echo $OUTPUT->heading('Segment Events');

echo html_writer::start_tag('table', array('class' => 'generaltable events'));

echo html_writer::start_tag('tr');
echo html_writer::tag('th', 'Event');
echo html_writer::tag('th', 'Name');
echo html_writer::tag('th', 'Type');
echo html_writer::tag('th', 'Status');
echo html_writer::tag('th', 'Actions');
echo html_writer::end_tag('tr');

$events = $DB->get_records('local_segment');

foreach ($events as $key => $value) {
    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $value->event);
    echo html_writer::tag('td', $value->name);
    echo html_writer::tag('td', segment_event::event_types()[$value->type]);
    echo html_writer::tag('td', segment_event::active_options()[$value->active]);
    $actionlinks = '';
    if(has_capability('local/segment:manage', context_system::instance())) {
        $editurl = new moodle_url('/local/segment/events/form.php', array('id' => $value->id));
        $actionlinks .= html_writer::link($editurl, 'Edit');

        $actionlinks .= ' | ';

        $deleteurl = new moodle_url('/local/segment/events/delete.php', array('id' => $value->id));
        $actionlinks .= html_writer::link($deleteurl, 'Delete');
    }
    echo html_writer::tag('td', $actionlinks);
    echo html_writer::end_tag('tr');
}

echo html_writer::end_tag('table');

$neweventurl = new moodle_url('/local/segment/events/form.php');
echo html_writer::link($neweventurl, 'New Event', array('class' => 'btn'));

echo $OUTPUT->footer();
