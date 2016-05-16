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

function forum_events_process_moodle_event(\core\event\base $moodleevent) {
  global $DB;

  if (get_config('local_forum_events', 'enabletracking') == 1) {

    $forumeventsevents = forum_events_event::forum_events_events($moodleevent->eventname);

    if (!empty($forumeventsevents)) {

      $course = $DB->get_record('course', array('id' => $moodleevent->courseid));

      if ($moodleevent->objecttable == 'course_sections') {
        $coursesection = $DB->get_record('course_sections', array('id' => $moodleevent->objectid));
      } else {
        $coursesection = null;
      }

      $other = (object)$moodleevent->other;

      $coursecoach = forum_events_course_coach($course->id);

      foreach ($forumeventsevents as $key => $forumeventsevent) {
        $subject = build_forum_string($forumeventsevent->forum_subject, $course, $coursesection, $other, $coursecoach);
        $body = build_forum_string($forumeventsevent->forum_body, $course, $coursesection, $other, $coursecoach);
        create_general_discussion_forum_post($course->id, $subject, $body, $coursecoach);
      }
    }
  }
}

function create_general_discussion_forum_post($courseid, $topicname, $message, $coursecoach) {
  global $DB;
  $forum = $DB->get_record('forum', array('course' => $courseid, 'name' => 'General course announcements'));

  $discussion = new stdClass();

  $discussion->course        = $forum->course;
  $discussion->forum         = $forum->id;
  $discussion->name          = $topicname;
  $discussion->assessed      = $forum->assessed;
  $discussion->message       = $message;

  $discussion->messageformat = $forum->introformat;
  $discussion->messagetrust  = trusttext_trusted(context_course::instance($forum->course));
  $discussion->mailnow       = true;
  $discussion->groupid       = -1;

  forum_add_discussion($discussion, null, null, $coursecoach->id);
}

function build_forum_string($message, $course, $coursesection, $other, $coursecoach) {
  if (!empty($other)) {
    if (isset($other->final_results)) {
      $message = str_replace("{final_results}", $other->final_results, $message);
    }
    if (isset($other->final_access)) {
      $message = str_replace("{final_access}", $other->final_access, $message);
    }
  }

  if (!empty($coursecoach)) {
    $message = str_replace("{course_coach}", fullname($coursecoach, true), $message);
    $message = str_replace("{course_coach_email}", $coursecoach->email, $message);
    $message = str_replace("{course_coach_first_name}", $coursecoach->firstname, $message);
  }

  if (!empty($course)) {
    if(isset($course->startdate)){
      $message = str_replace("{course_start_date}", date('j F Y', $course->startdate), $message);
    }
    if(isset($course->fullname)){
      $message = str_replace("{course_fullname}", $course->fullname, $message);
    }
  }

  if (!empty($coursesection)) {
    if (isset($coursesection->name)) {
      $message = str_replace("{course_section_name}", $coursesection->name, $message);
    }
  }

  return $message;
}

function forum_events_course_coach($courseid) {
  global $DB;
  $context = context_course::instance($courseid);
  $roleid = get_config('local_forum_events', 'forumrole');

  $users = get_role_users($roleid, $context);
  $coursecoach = current($users);
  return $coursecoach;
}
