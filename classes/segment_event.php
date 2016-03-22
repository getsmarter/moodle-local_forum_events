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

class segment_event {

  public static function event_types() {
      return array('0' => 'Identify', '1' => 'Track', '2' => 'Page', '3' => 'Alias');
  }

  public static function active_options() {
      return array('0' => 'Inactive', '1' => 'Active');
  }

  public static function segment_events($event_name) {
    global $DB;

    $sql = "
    SELECT
        e.id,
        e.event,
        e.name,
        e.type,
        e.properties
    FROM
        {local_segment} e
    WHERE
        e.active = ? AND
        e.event = ?
    ";

    return $DB->get_records_sql($sql, array('1', $event_name));
  }

  public static function send($type, $user_id, $event_name, $properties) {
    switch ($type) {
      case '0':
        self::segment_identify($user_id, $properties);
        break;
      case '1':
        self::segment_track($user_id, $event_name, $properties);
        break;
      case '2':
        self::segment_page($user_id, $event_name, $properties);
        break;
      case '3':
        self::segment_alias($properties);
        break;
    }
  }

  public static function segment_identify($user_id, $properties) {
    Analytics::identify(array(
      'userId' => $user_id,
      'traits' => $properties
    ));
  }

  public static function segment_track($user_id, $event_name, $properties) {
    Analytics::track(array(
      'userId' => $user_id,
      'event' => $event_name,
      'properties' => $properties
    ));
  }

  public static function segment_page($user_id, $event_name, $properties) {
    global $PAGE;
    $properties['title'] = $PAGE->title;
    $properties['url'] = $PAGE->url->out(true);
    $properties['path'] = $PAGE->url->get_path();
    Analytics::page(array(
      'userId' => $user_id,
      'name' => $event_name,
      'properties' => $properties
    ));
  }

  public static function segment_alias($properties) {
    // $properties must be an array with "previousId" and "userId"
    Analytics::alias($properties);
  }

}
