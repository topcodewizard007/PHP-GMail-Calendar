<?php
ob_start();
require_once '../app/services/GoogleAuthService.php';

class GoogleMailBoxService {
  // https://gmail.googleapis.com/gmail/v1/users/{userId}/mails/{id}
  const MAIL_BASE_URL = "https://gmail.googleapis.com/gmail/v1/users/";
  const SEND_BASE_URL = "https://www.googleapis.com/upload/gmail/v1/users/";

  public function __construct($auth_service) {
     $this->auth_service = $auth_service;
  }

  public function get_mails() {
    $url = self::MAIL_BASE_URL.'...@gmail.com/messages/1';
    echo($url);
    $mails = $this->auth_service->make_request($url, 'GET', 'normal', array('access_token' => $this->auth_service->api_config['access_token']));
    $response = $this->auth_service->formatResponse($mails);

    return $response;
  }

  public function send_mails($data) {
    $url = self::SEND_BASE_URL.'...@gmail.com/messages/send?uploadType=media';
    $mails = $this->auth_service->make_request($url, 'POST', 'json', $data);
    $response = $this->auth_service->formatResponse($mails);

    return $response;
  }
}