<?php
require_once '../app/services/GoogleCalendarService.php';
require_once '../app/services/GoogleContactService.php';
class Dashboard extends Controller
{
    public static function index($auth_service)
    {
        $calendar_service = new GoogleCalendarService($auth_service);
        $data = [];
        $data['calendar_lists'] = $calendar_service->get_calendars()['items'];
        return parent::view('dashboard/index', $data);
    }
// Get google contacts
    public static function getContacts($auth_service) {
        $contact_service = new GoogleContactService($auth_service);
        $result = $contact_service->get_contacts();
        echo json_encode(['contacts' => $result['feed']['entry']]);
    }
// create google calendar
    public static function create($auth_service)
    {
        $calendar_service = new GoogleCalendarService($auth_service);
        if (!isset($_SESSION['user_timezone'])) {
            $_SESSION['user_timezone'] = $calendar_service->get_user_timezone();
        }
        $result = $calendar_service->create_calendar($_POST['data']);
        echo json_encode(['calendar_id' => $result['id']]);
    }
// delete google calendar
    public static function delete($auth_service)
    {
        $calendar_service = new GoogleCalendarService($auth_service);
        $data = $_POST['data'];
        $result = $calendar_service->delete_calendar($data);
        echo json_encode(['deleted' => 1]);
    }

}
