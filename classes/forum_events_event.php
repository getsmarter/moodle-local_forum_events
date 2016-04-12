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

class forum_events_event {

  public static function active_options() {
      return array('0' => 'Inactive', '1' => 'Active');
  }

  public static function forum_events_events($event_name) {
    global $DB;

    $sql = "
    SELECT
        e.id,
        e.event,
        e.name,
        e.forum_subject,
        e.forum_body
    FROM
        {local_forum_events} e
    WHERE
        e.active = ? AND
        e.event = ?
    ";

    return $DB->get_records_sql($sql, array('1', $event_name));
  }
}
