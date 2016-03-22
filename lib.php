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

global $PAGE;
$PAGE->requires->js_call_amd('local_segment/segment', 'init');

function process_moodle_event(\core\event\base $moodle_event) {
  global $DB;
  global $PAGE;

  if(get_config('local_segment', 'enabletracking') == 1) {

    load_segment();

    $segment_events = segment_event::segment_events($moodle_event->eventname);

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

    foreach ($segment_events as $key => $segment_event) {

      $properties = eval('return "' . str_replace('"', '\"', $segment_event->properties) . '";');
      $properties_array = json_decode($properties, true);

      if (empty($user->email)) {
        $user_email = $related_user->email;
      } else {
        $user_email = $user->email;
      }

      segment_event::send($segment_event->type, $user_email, $segment_event->name, $properties_array);

    }
  }
}

function load_segment() {
  global $CFG;

  require_once($CFG->dirroot.'/local/segment/classes/segment_event.php');
  require_once($CFG->dirroot.'/local/segment/analytics-php/lib/Segment.php');

  class_alias('Segment', 'Analytics');
  $write_key = get_config('local_segment', 'writekey');

  Analytics::init($write_key, array(
    'consumer'      => 'socket',
    'debug'         => true,
    'error_handler' => function ($code, $msg) { error_log($msg); }
  ));

}
