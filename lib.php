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

require_once($CFG->dirroot.'/local/forum_events/classes/forum_events_event.php');
require_once ($CFG->dirroot."/mod/forum/lib.php");

function forum_events_process_moodle_event(\core\event\base $moodle_event) {
  global $DB;
  global $PAGE;

  if(get_config('local_forum_events', 'enabletracking') == 1) {

    $forum_events_events = forum_events_event::forum_events_events($moodle_event->eventname);

    $course = $DB->get_record('course', array('id' => $moodle_event->courseid));
    $other = (object)$moodle_event->other;

    foreach ($forum_events_events as $key => $forum_events_event) {

      $subject = eval('return "' . str_replace('"', '\"', $forum_events_event->forum_subject) . '";');
      $body = eval('return "' . str_replace('"', '\"', $forum_events_event->forum_body) . '";');
      create_general_discussion_forum_post($course->id, $subject, $body);
    }
  }
}

function create_general_discussion_forum_post($courseid, $topic_name, $message) {
  global $DB;
  $forum = $DB->get_record('forum', array('course' => $courseid, 'name' => 'General course announcements'));

  $discussion = new stdClass();

  $discussion->course        = $forum->course;
  $discussion->forum         = $forum->id;
  $discussion->name          = $topic_name;
  $discussion->assessed      = $forum->assessed;
  $discussion->message       = $message;

  $discussion->messageformat = $forum->introformat;
  $discussion->messagetrust  = trusttext_trusted(context_course::instance($forum->course));
  $discussion->mailnow       = true;
  $discussion->groupid       = -1;

  $user = get_role_user_forum_post($courseid);
  forum_add_discussion($discussion,null,null,$user->id);
}

function get_role_user_forum_post($course_id) {
  global $DB;
  $context = context_course::instance($course_id);
  $role_id = get_config('local_forum_events', 'forumrole');

  $users = get_role_users($role_id, $context);
  $course_coach = current($users);
  return $course_coach;
}
