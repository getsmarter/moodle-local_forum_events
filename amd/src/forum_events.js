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
 * Version details
 *
 * @package    local_segment
 * @copyright  2014 GetSmarter {@link http://www.getsmarter.co.za}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module local_segment/segment
 */
 define(['jquery', 'local_forum_events/chosen_jquery'], function($) {

  var module = {};

  module.event_form = function() {
    $('#fitem_id_event #id_event').chosen();

    $('#fitem_id_event #id_event').chosen().change(function() {
      if($( "#id_event" )) {
        $('#event_link').remove();
        var url = $(location).attr('origin') + "/report/eventlist/eventdetail.php?eventname=" + $( "#id_event" ).val();
        $('<div id="event_link"> <br> <a href=' + url + '>Click to see the variables associated with this Event</a></div>').insertBefore('#fitem_id_name');
      }
    });
  };

  return module;
});
