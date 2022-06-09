<?php
require_once '../app/services/GoogleMailBoxService.php';
include_once '../app/services/DataBaseService.php';

class Emails extends Controller {

  public function __construct() {

  }
  
  public function index($auth_service) {
    $DBCon = new DatabaseService();
    $email = $DBCon->selectOne("contacts", 1, "email");
    if($email){
		
    }
			else{
				
		}
    // $mailbox_service = new GoogleMailBoxService($auth_service);
    // $data = [];
    // $data['mail_lists'] = $mailbox_service->get_mails();
    // echo $data['mail_lists'];
    echo $email;
    return parent::view('emails/index', $email);
  }
}