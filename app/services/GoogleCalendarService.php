<?php
ob_start();
require_once '../app/services/GoogleAuthService.php';

class GoogleCalendarService
{
    // Calendar constants
    const CAL_BASE_URL = 'https://www.googleapis.com/calendar/v3/calendars/';

    public function __construct($auth_service){
            $this->auth_service = $auth_service;
    }
    /*
     *   @param (string) calendar_id - the calendar id, if its blank it will revert to the primary one
     *   @return (object) Returns all calendar events for this calendar
     */
    public function get_events($calendar_id = null)
    {
        $calendar_id = ($calendar_id == null ? 'primary' : $calendar_id);
        $url = self::CAL_BASE_URL . $calendar_id . '/events';
        $events = $this->auth_service->make_request($url, 'GET', 'normal', array('access_token' => $this->auth_service->api_config['access_token']));
        $response = $this->auth_service->formatResponse($events);
        if ($response['headers']['respHttpCode'] !== 200) {
            throw new Exception('Error : Failed to get calendar list');
        }
        return $response;
    }

    /*
     *   @param (string) calendar_id - the calendar id, if its blank it will revert to the primary one
     *   @param (string) event_id - the event id, must be present or the request is useless
     *   @return (object) Returns a calendar event for this calendar
     */
    public function get_event($calendar_id = null, $event_id)
    {
        if (!$event_id) {
            return array('error' => 'No Event ID specified');
        }

        $calendar_id = ($calendar_id == null ? 'primary' : $calendar_id);
        $url = self::CAL_BASE_URL . $calendarID . '/events/' . $event_id;
        $response = $this->auth_service->make_request($url, 'GET', 'normal', array('access_token' => $this->auth_service->api_config['access_token']));
        if ($response['headers']['respHttpCode'] !== 200) {
            throw new Exception('Error : Failed to get calendar list');
        }
        return $response;
    }

    /*
     *   Fetches a List of Calendars
     *   @return (object) Returns a list of Calendars
     */
    public function get_calendars()
    {
        $url = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
        $request = $this->auth_service->make_request($url, 'GET', 'normal',
            array('fields' => 'items(id,summary,timeZone)', 'minAccessRole' => 'owner'));
        $response = $this->auth_service->formatResponse($request);
        if ($response['headers']['respHttpCode'] !== 200) {
            throw new Exception('Error : Failed to get calendar list');
        }
        return $response;
    }

    /*  Create a Calendar
     *   @param (array) array
    summary (string)
    description (string)
    time_zone (string)
     *   @return (object) Returns a calendar event for this calendar
     */
    public function create_calendar($data)
    {
        $url = self::CAL_BASE_URL;
        $info = array('summary' => $data['summary'], 'description' => $data['description'], 'timeZone' => $_SESSION['user_timezone']);
        $request = $this->auth_service->make_request($url, 'POST', 'json', $info);
        $response = $this->auth_service->formatResponse($request);
        if ($response['headers']['respHttpCode'] !== 200) {
            throw new Exception('Error : Failed to create calendar');
        }
        return $this->auth_service->formatResponse($response);
    }

    /*
     * Deletes a Calendar
     * @param (array) array
    calendar_id (string)
     *   @return (object) Returns a calendar event for this calendar
     */
    public function delete_calendar($data)
    {
        $url = self::CAL_BASE_URL . $data['calendar_id'];

        $request = $this->auth_service->make_request($url, 'DELETE', 'json', null);
        $response = $this->auth_service->formatResponse($request);
        if ($response['headers']['respHttpCode'] !== 204) {
            throw new Exception('Error : Failed to delete calendar ' . $data['calendar_id']);
        }
        return $this->auth_service->formatResponse($response);
    }

    public function get_user_timezone()
    {
        $data = [];
        $url = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
        $time_zone = $this->auth_service->make_request($url, 'GET', 'normal', $data);
        return $this->auth_service->formatResponse($time_zone)['value'];
    }

    /*
     * Create an Event
     *   @param (string) calendar_id - the calendar id, if its blank it will revert to the primary one
     *   @param (array) array
    calendar_id (string)
    start_time (string) - yyyy-dd-mm
    end_time (string) - yyyy-dd-mm
    summary (string)
    description (string)
     *   @return (object) Returns a calendar event for this calendar
     */
    public function create_event($array)
    {
        $calendar_id = ($array['calendar_id'] == null ? 'primary' : $array['calendar_id']);
        $data = array(
            "kind" => "calendar#event",
            "summary" => $array['title'],
            "description" => $array['description'],
            "colorId" => "4",
            // extended properties
            'reminders' => [
            'useDefault' => FALSE,
            'overrides' => [
                ['method' => 'email', 'minutes' => 24 * 60],
                ['method' => 'popup', 'minutes' => 10],
                ],
            ],
            'extendedProperties' => [
                'private' => [
                    'myProp1' => "no",
                    'myProp2' => "myProp2Value",
                ],
                'shared' => [
                    'myProp3' => 'the prop 3',
                    'myProp4' => 'the prop 4',
                ]
            ]
        );
        if ($array['all_day'] == 1) {
            $data['start'] = array('date' => $array['event_date']);
            $data['end'] = array('date' => $array['event_date']);
        } else {
            $data['start'] = array('dateTime' => $array['start_time'], 'timeZone' => $array['time_zone']);
            $data['end'] = array('dateTime' => $array['end_time'], 'timeZone' => $array['time_zone']);
        }
        $url = self::CAL_BASE_URL . $calendar_id . '/events';
        $events = $this->auth_service->make_request($url, 'POST', 'json', $data);
        return $this->auth_service->formatResponse($events);
    }

    /*
     * Deletes an Event
     * @param (array) array
    calendar_id (string)
    event_id (string) - dd-mm-yyyy
     *   @return (object) Returns a calendar event for this calendar
     */
    public function delete_event($array)
    {
        if (!$array['event_id']) {
            throw new Exception('Error : no event specified');
        }

        $calendar_id = ($array['calendar_id'] == null ? 'primary' : $array['calendar_id']);
        $event_id = $array['event_id'];

        $url = self::CAL_BASE_URL . $calendar_id . '/events/' . $event_id;
        $events = $this->auth_service->make_request($url, 'DELETE', 'json', null);
        return $this->auth_service->formatResponse($events);
    }

    /*
     *   Update an Event
     *   @param (array) array
    calendar_id (string)
    start_time (string) - dd-mm-yyyy
    end_time (string) - dd-mm-yyyy
     *   @return (object) Returns a calendar event for this calendar
     */
    public function update_event($array)
    {
        if (!$array['event_id']) {
            throw new Exception('Error : no event specified');
        }
        $data = array(
            "kind" => "calendar#event",
            "summary" => $array['title'],
            "description" => $array['description'],
            "colorId" => "4",
            // extended properties
            'reminders' => [
            'useDefault' => FALSE,
            'overrides' => [
                ['method' => 'email', 'minutes' => 24 * 60],
                ['method' => 'popup', 'minutes' => 10],
                ],
            ],
            'extendedProperties' => [
                'private' => [
                    'myProp1' => "no",
                    'myProp2' => "myProp2Value",
                ],
                'shared' => [
                    'myProp3' => 'the prop 3',
                    'myProp4' => 'the prop 4',
                ]
            ]
        );
        // $data = array('summary' => $array['title']);
        // $data = array('description' => $array['description']);
        $calendar_id = ($array['calendar_id'] == null ? 'primary' : $array['calendar_id']);
        $event_id = $array['event_id'];

        if ($array['all_day'] == 1) {
            $data['start'] = array('date' => $array['event_date']);
            $data['end'] = array('date' => $array['event_date']);
        } else {
            $data['start'] = array('dateTime' => $array['start_time'], 'timeZone' => $array['time_zone']);
            $data['end'] = array('dateTime' => $array['end_time'], 'timeZone' => $array['time_zone']);
        }

        $url = self::CAL_BASE_URL . $calendar_id . '/events/' . $event_id;
        $events = $this->auth_service->make_request($url, 'PUT', 'json', $data);
        return $this->auth_service->formatResponse($events);
    }
}
