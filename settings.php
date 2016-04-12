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

defined('MOODLE_INTERNAL') || die;

// Add a category to the Site Admin menu
$ADMIN->add('localplugins', new admin_category('local_forum_events', get_string('pluginname', 'local_forum_events')));

//General settings page
$temp = new admin_settingpage('local_forum_events_general',  'Settings', 'local/forum_events:manage');

  // Enable tracking
$name = 'local_forum_events/enabletracking';
$title = 'Enable tracking';
$description = 'Enable or disable forum_events event tracking.';
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);

  // Default properties
$name = 'local_forum_events/defaultproperties_subject';
$title = 'Default forum Subject';
$description = 'The default value for the subject of an forum.';
$default =
"VLE forum subject";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

  // Default properties
$name = 'local_forum_events/defaultproperties_body';
$title = 'Default forum Body';
$description = 'The default value for the body of an forum.';
$default =
"VLE forum body";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

$name = 'local_forum_events/forumrole';
$title = 'forum from role';
$description = 'Select the role who will create the forum';
$default = 5;
$context = context_course::instance(1); // site wide course context
$roles = get_assignable_roles($context);
$setting = new admin_setting_configselect($name, $title, $description, $default, $roles);
$temp->add($setting);


$ADMIN->add('local_forum_events', $temp);

// Events index
$temp = new admin_externalpage('local_forum_events_events',  'Events', '/local/forum_events/events/index.php', 'local/forum_events:view');

$ADMIN->add('local_forum_events', $temp);
