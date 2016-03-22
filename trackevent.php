<?php

/**
 * Segment
 * Generates a json array of posts with their likes and thanks in a forum discussion
 *
 * @package   segment
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @author    Mikhail Janowski <mikhail@getsmarter.co.za>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/segment/lib.php');

load_segment();

$eventName = required_param('event', PARAM_TEXT);
$videoName = required_param('video', PARAM_TEXT);
$language = optional_param('language', 0, PARAM_TEXT);
$moduleName = required_param('module', PARAM_TEXT);
$courseName = required_param('course', PARAM_TEXT);


$result = new stdClass();
$result->result = false; // set in case uncaught error happens
$result->content = 'Unknown error';

//Only allow to get actions if logged in
if(isloggedin()) {

  $sql = "
  SELECT
      u.fieldid, u.data
  FROM
      {user_info_data} u
  WHERE
      u.userid = $USER->id
  ";
  $details = $DB->get_records_sql($sql);

  $year_of_birth = '';
  $gender = '';
  $level_of_education = '';
  $race = '';
  $home_language = '';
  $university_attended = '';

  foreach ($details as $key => $value) {
    switch($key){
      case 26:
        $year_of_birth = $value->data;
        break;
      case 28:
        $gender = $value->data;
        break;
      case 29:
        $level_of_education = $value->data;
        break;
      case 30:
        $race = $value->data;
        break;
      case 31:
        $home_language = $value->data;
        break;
      case 33:
        $university_attended = $value->data;
        break;
      default:
        // do nothing
    }
  }

  // identify
  Analytics::identify(array(
    "userId" => $USER->email,
    "traits" => array(
      "first_name" => $USER->firstname,
      "last_name" => $USER->lastname,
      "email" => $USER->email,
      "year_of_birth" => $year_of_birth,
      "gender" => $gender,
      "level_of_education" => $level_of_education,
      "race" => $race,
      "home_language" => $home_language,
      "university_attended" => $university_attended
    )
  ));

  // track
  Analytics::track(array(
    "userId" => $USER->email,
    "event" => $eventName,
    "properties" => array(
      "video" => $videoName,
      "course" => $courseName,
      "module" => $moduleName,
      "language" => $language,
      "year_of_birth" => $year_of_birth,
      "gender" => $gender,
      "level_of_education" => $level_of_education,
      "race" => $race,
      "home_language" => $home_language,
      "university_attended" => $university_attended
    )
  ));

  $result->result = true;
  $result->content = "Event fired";
  $result->eventName = $eventName;
  $result->language = $language;
  $result->video = $videoName;
  $result->module = $moduleName;
  $result->course = $courseName;
}
else {
  $result->result = false;
  $result->content = 'Your session has timed out. Please login again.';
}

//echo '<pre>'.print_r($posts, true).'</pre>';
header('Content-type: application/json');
echo json_encode($result);
