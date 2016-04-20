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
 * forum_events
 *
 * @package    local_forum_events
 * @copyright  2014 GetSmarter {@link http://www.getsmarter.co.za}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('forum_events_event.php');

class forum_events_event_form extends moodleform {
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

        $mform->addElement('select', 'active', 'Status', forum_events_event::active_options());
        $mform->setDefault('active', '1');

        $mform->addElement('textarea', 'forum_subject', 'forum Subject', 'rows="3" cols="15"');
        $mform->setType('forum_subject', PARAM_NOTAGS);
        $mform->setDefault('forum_subject', get_config('local_forum_events', 'defaultproperties_subject'));

        $mform->addElement('editor', 'forum_body', 'Forum Body');
        $mform->setType('forum_body', PARAM_RAW);
        $mform->setDefault('forum_body', array('text' => get_config('local_forum_events', 'defaultproperties_body'),'format' => FORMAT_HTML));

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

        if($data['active'] == '') {
            $errors['active'] = 'Status must be selected';
        }

        if($data['forum_body'] == '') {
            $errors['properties'] = 'Properties cannot be blank';
        }

        if($data['forum_subject'] == '') {
            $errors['properties'] = 'Properties cannot be blank';
        }

        return $errors;
    }
}
