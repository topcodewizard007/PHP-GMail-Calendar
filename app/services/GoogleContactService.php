<?php
ob_start();
require_once '../app/services/GoogleAuthService.php';

class GoogleContactService {
  
  // Contact constants
  const MAX_RESULT = 2;
  const CONTACT_BASE_URL = "https://www.google.com/m8/feeds/contacts/default/full?max-results=";
  public function __construct($auth_service) {
     $this->auth_service = $auth_service;
  }

  public function get_contacts() {
    $url = self::CONTACT_BASE_URL.self::MAX_RESULT.'&alt=json&v=3.0&oauth_token='.$this->auth_service->api_config['access_token'];
    $contacts = $this->auth_service->make_request($url, 'GET', 'normal', array('access_token' => $this->auth_service->api_config['access_token']));
    $response = $this->auth_service->formatResponse($contacts);
    return $response;
  }
}