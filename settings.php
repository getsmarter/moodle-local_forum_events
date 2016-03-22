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

defined('MOODLE_INTERNAL') || die;

// Add a category to the Site Admin menu
$ADMIN->add('localplugins', new admin_category('local_segment', get_string('pluginname', 'local_segment')));

//General settings page
$temp = new admin_settingpage('local_segment_general',  'Settings', 'local/segment:manage');

  // Enable tracking
  $name = 'local_segment/enabletracking';
  $title = 'Enable tracking';
  $description = 'Enable or disable Segment event tracking.';
  $default = 0;
  $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
  $temp->add($setting);

  // Segment write key
  $name = 'local_segment/writekey';
  $title = 'Write key';
  $description = 'The Segment API write key for the project you want to send events to.';
  $default = '';
  $setting = new admin_setting_configtext($name, $title, $description, $default);
  $temp->add($setting);

  // Default properties
  $name = 'local_segment/defaultproperties';
  $title = 'Default properties';
  $description = 'The default value for the properties of an event.';
  $default =
"{
  \"email\": \"\$user->email\",
  \"first_name\": \"\$user->firstname\",
  \"last_name\": \"\$user->lastname\"
}";
  $setting = new admin_setting_configtextarea($name, $title, $description, $default);
  $temp->add($setting);

$ADMIN->add('local_segment', $temp);

// Events index
$temp = new admin_externalpage('local_segment_events',  'Events', '/local/segment/events/index.php', 'local/segment:view');

$ADMIN->add('local_segment', $temp);
