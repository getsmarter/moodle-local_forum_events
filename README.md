# Segment

Moodle plugin that hooks into the Moodle Events API and sends the events to a Segment.io project.

It has settings for changing:

* Enabling/disabling sending of events
* Segment project writekey
* Default event properties

Each event has:

* Moodle event name
* Segment event name
* Segment event type
* Segment event properties
* Active/inactive


## Installation

1. Download the plugin from https://github.com/getsmarter/moodle-local_segment
2. Copy files to /local/segment and visit your Admin Notification page to complete the installation.
3. Go to Site Administration > Plugins > Local Plugins > Segment.io > Events to manage events.

## Documentation

Futher documentation can be found at https://github.com/getsmarter/moodle-local_segment/wiki
