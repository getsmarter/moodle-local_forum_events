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

defined('MOODLE_INTERNAL') || die;

// Add a category to the Site Admin menu
$ADMIN->add('localplugins', new admin_category('local_email_events', get_string('pluginname', 'local_email_events')));

//General settings page
$temp = new admin_settingpage('local_email_events_general',  'Settings', 'local/email_events:manage');

  // Enable tracking
$name = 'local_email_events/enabletracking';
$title = 'Enable tracking';
$description = 'Enable or disable email_events event tracking.';
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$temp->add($setting);

  // Default properties
$name = 'local_email_events/defaultproperties_subject';
$title = 'Default Email Subject';
$description = 'The default value for the subject of an email.';
$default =
"VLE Email subject";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

  // Default properties
$name = 'local_email_events/defaultproperties_body';
$title = 'Default Email Body';
$description = 'The default value for the body of an email.';
$default =
"VLE Email body";
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$temp->add($setting);

$name = 'local_email_events/emailrole';
$title = 'Email from role';
$description = 'Select the role who will create the email';
$default = 5;
$context = context_course::instance(1); // site wide course context
$roles = get_assignable_roles($context);
$setting = new admin_setting_configselect($name, $title, $description, $default, $roles);
$temp->add($setting);


$ADMIN->add('local_email_events', $temp);

// Events index
$temp = new admin_externalpage('local_email_events_events',  'Events', '/local/email_events/events/index.php', 'local/email_events:view');

$ADMIN->add('local_email_events', $temp);
