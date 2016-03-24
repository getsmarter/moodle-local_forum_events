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

global $PAGE;
// $PAGE->requires->js_call_amd('local_email_events/email_events', 'init');
require_once($CFG->dirroot.'/local/email_events/classes/email_events_event.php');

function email_events_process_moodle_event(\core\event\base $moodle_event) {
  global $DB;
  global $PAGE;

  if(get_config('local_email_events', 'enabletracking') == 1) {

    $email_events_events = email_events_event::email_events_events($moodle_event->eventname);
    $user = get_complete_user_data('id', $moodle_event->userid);
    if (isset($user)) {
      $user_profile = (object)$user->profile;
    }

    if (!empty($moodle_event->relateduserid)) {
      $related_user = get_complete_user_data('id', $moodle_event->relateduserid);
      $related_user_profile = (object)$related_user->profile;
    }

    $course = $DB->get_record('course', array('id' => $moodle_event->courseid));
    $other = (object)$moodle_event->other;

    $roles = get_email_role($moodle_event->courseid);
    $role = current($roles);

    foreach ($email_events_events as $key => $email_events_event) {
      $subject = eval('return "' . str_replace('"', '\"', $email_events_event->email_subject) . '";');
      $body = eval('return "' . str_replace('"', '\"', $email_events_event->email_body) . '";');
      $bodyhtml = text_to_html($body, null, false, true);

      email_to_user($user, $role, $subject, $body, $bodyhtml);
    }
  }
}

function get_email_role($course_id) {
  global $DB;
  $context = context_course::instance($course_id);
  $role_id = get_config('local_email_events', 'emailrole');
  $users = get_role_users($role_id, $context);
  return $users;
}
