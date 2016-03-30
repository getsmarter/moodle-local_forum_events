<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class sendgrid_webhook_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function process_sendgrid_events_parameters() {
        return new external_function_parameters(
            array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Sengrid webhook successful"', VALUE_DEFAULT, 'Sendgrid webhook successful '))
            );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static $event_name;
    public static function process_sendgrid_events($welcomemessage = 'Sendgrid webhook successful') {
        global $USER;
        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::process_sendgrid_events_parameters(),
            array('welcomemessage' => $welcomemessage));

        $json = file_get_contents('php://input');
        $array = json_decode($json);
        $context = context_system::instance();
        foreach ($array as $email_event) {
            $event = $email_event->event;

            self::$event_name = $email_event->event;

            $data = array(
                'context' => $context,
                'other' => array(
                    'email' => $email_event->email,
                    'timestamp' => $email_event->timestamp
                    ));

            if (isset($email_event->ip)) {
                $data['other']['ip'] = $email_event->ip;
            }
            if (isset($email_event->{"smtp-id"})) {
                $data['other']['smtp-id'] = $email_event->{"smtp-id"};
            }
            if (isset($email_event->tls)) {
                $data['other']['tls'] = $email_event->tls;
            }
            if (isset($email_event->cert_err)) {
                $data['other']['cert_err'] = $email_event->cert_err;
            }

            $event = \local_email_events\event\email_event::create($data);
            error_log(print_r($event, 1));
            $event->trigger();
        }

        return $params['welcomemessage'] . $USER->firstname ;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function process_sendgrid_events_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }
}
