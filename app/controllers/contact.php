<?php
include_once '../app/services/DataBaseService.php';
class Contact extends Controller {

  
  public function __construct() {
    
  }
  public function insertData() {
    $DBCon = new DatabaseService();
    $data = $_POST['data'];
    $insert = $DBCon->insert("contacts", $data);
    if($insert){
				$json['status'] = 101;
				$json['msg'] = "Data Successfully Inserted";
			}
			else{
				$json['status'] = 102;
				$json['msg'] = "Data Not Inserted";
			}
    echo json_encode($json);
  }
  public function getData() {

  }
}
