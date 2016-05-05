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
    $coursesection = $DB->get_record('course_sections', array('id' => $moodle_event->objectid));
    $other = (object)$moodle_event->other;

    foreach ($forum_events_events as $key => $forum_events_event) {
      $subject = build_forum_string($forum_events_event->forum_subject, $course, $coursesection, $other);
      $body = build_forum_string($forum_events_event->forum_body, $course, $coursesection, $other);
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

function build_forum_string($message, $course, $coursesection, $other) {
  if(isset($other)){
    if(isset($other->course_coach)){
      $message = str_replace("{course_coach}", $other->course_coach, $message);
    }
    if(isset($other->course_coach_email)){
      $message = str_replace("{course_coach_email}", $other->course_coach_email, $message);
    }
    if(isset($other->course_coach_first_name)){
      $message = str_replace("{course_coach_first_name}", $other->course_coach_first_name, $message);
    }
    if(isset($other->student_name)){
      $message = str_replace("{student_name}", $other->student_name, $message);
    }
    if(isset($other->student_username)){
      $message = str_replace("{student_username}", $other->student_username, $message);
    }
    if(isset($other->student_email)){
      $message = str_replace("{student_email}", $other->student_email, $message);
    }
    if(isset($other->student_id)){
      $message = str_replace("{student_id}", $other->student_id, $message);
    }
    if(isset($other->final_results)){
      $message = str_replace("{final_results}", $other->final_results, $message);
    }
    if(isset($other->final_access)){
      $message = str_replace("{final_access}", $other->final_access, $message);
    }

  }
  if(isset($course)){
    if(isset($course->startdate)){
      $message = str_replace("{course_start_date}", date('d/m/Y', $course->startdate), $message);
    }
    if(isset($course->fullname)){
      $message = str_replace("{course_fullname}", $course->fullname, $message);
    }
  }
  if(isset($coursesection)){
    if(isset($coursesection->name)){
      $message = str_replace("{course_section_name}", $coursesection->name, $message);
    }
  }

  return $message;
}

function get_role_user_forum_post($course_id) {
  global $DB;
  $context = context_course::instance($course_id);
  $role_id = get_config('local_forum_events', 'forumrole');

  $users = get_role_users($role_id, $context);
  $course_coach = current($users);
  return $course_coach;
}
