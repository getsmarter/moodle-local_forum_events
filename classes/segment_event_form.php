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

require_once('segment_event.php');

class segment_event_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $eventsdata = report_eventlist_list_generator::get_all_events_list();

        $events[''] = '';

        foreach ($eventsdata as $key => $value) {
            $events[$key] = $value['raweventname'];
        }

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('select', 'event', 'Event', $events, array('class' => 'chosen-select', 'data-placeholder' => 'Choose an event'));

        $mform->addElement('text', 'name', 'Name');
        $mform->setType('name', PARAM_NOTAGS);

        $mform->addElement('select', 'type', 'Type', segment_event::event_types());
        $mform->setDefault('type', '1');

        $mform->addElement('select', 'active', 'Status', segment_event::active_options());
        $mform->setDefault('active', '1');

        $mform->addElement('textarea', 'properties', 'Properties', 'rows="10" cols="15"');
        $mform->setType('properties', PARAM_NOTAGS);
        $mform->setDefault('properties', get_config('local_segment', 'defaultproperties'));

        $this->add_action_buttons(true, 'Save');

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if($data['event'] == '') {
            $errors['event'] = 'Event must be selected';
        }

        if($data['name'] == '') {
            $errors['name'] = 'Name cannot be blank';
        }

        if($data['type'] == '') {
            $errors['type'] = 'Type must be selected';
        }

        if($data['active'] == '') {
            $errors['active'] = 'Status must be selected';
        }

        if($data['properties'] == '') {
            $errors['properties'] = 'Properties cannot be blank';
        }

        return $errors;
    }
}
