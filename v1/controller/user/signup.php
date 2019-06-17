<?php

require_once '../../model/Response.php' ;
require_once '../Database/DB.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){ // cheacking if the requist is of type POST
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(405);
  $res->addMessage("Request method now allowed");
  $res->send();
  exit;
}

if($_SERVER['CONTENT_TYPE'] !== "application/json"){  // cheacking if the requist content type is json so we can handel it
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(400);
  $res->addMessage("Content type header not set to JSON");
  $res->send();
  exit;
}

try{ // cheacking if connectin to data base has nno errors
  $writeDB = DB::connectWriteDB();

}catch(PDOException $e){
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(500);
  $res->addMessage("Database connection error");
  $res->send();
  exit;
}


$rawPostData = file_get_contents('php://input'); // raw input from user (JSON)


if(!$input = json_decode($rawPostData)){ // cheacking if we can decode the json body
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(400);
  $res->addMessage("Request body is not valid json");
  $res->send();
  exit;
}

if(!isset($input->name) || !isset($input->email) || !isset($input->password) || !isset($input->fdotw) || !isset($input->country_id) ){ // cheacking  if all signup parametars are supplied
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(400);
  (!isset($input->name) ?  $res->addMessage("Name not supplied") : fasle);
  (!isset($input->email) ?  $res->addMessage("email not supplied") : fasle);
  (!isset($input->password) ?  $res->addMessage("password not supplied") : fasle);
  (!isset($input->fdotw) ?  $res->addMessage("First day of the week not supplied") : fasle);
  (!isset($input->country_id) ?  $res->addMessage("Country id not supplied") : fasle);
  $res->send();
  exit;
}

if(strlen($input->name) < 1 || strlen($input->name) > 255 || strlen($input->email) < 1 || strlen($input->email) > 255 || strlen($input->password) < 1 || strlen($input->password) > 255 ||
   strlen($input->fdotw) < 1 || strlen($input->fdotw) > 1 || strlen($input->country_id) < 1 || strlen($input->country_id) > 11 ){

  $res->setSuccess(false);
  $res->setHttpStatusCode(400);
  (strlen($input->name) < 1 ?  $res->addMessage("Name can not be blank") : fasle);
  (strlen($input->name) > 255 ?  $res->addMessage("Name can not be larger than 255 characters") : fasle);
  (strlen($input->email) < 1 ?  $res->addMessage("Email can not be blank") : fasle);
  (strlen($input->email) > 255 ?  $res->addMessage("Email can not be larger than 255 characters") : fasle);
  (strlen($input->password) < 1 ?  $res->addMessage("Password can not be blank") : fasle);
  (strlen($input->password) > 255 ?  $res->addMessage("Password can not be larger than 255 characters") : fasle);
  (strlen($input->fdotw) < 1 ?  $res->addMessage("First day of the week  can not be blank") : fasle);
  (strlen($input->fdotw) > 1 ?  $res->addMessage("First day of the week can not be larger than 1 characters") : fasle);
  (strlen($input->country_id) < 1 ?  $res->addMessage("country id  can not be blank") : fasle);
  (strlen($input->country_id) > 11 ?  $res->addMessage("cuontry id can not be larger than 11 characters") : fasle);
  $res->send();
  exit;

}


//name logic
$name = trim($input->name); // removing white spaces from left and right



// email logic
$email = trim($input->email); // removing white spaces from left and right

try{

  // checking if email alredy exist
  $query = $writeDB->prepare('select id from users where email = :email');
  $query->bindParam(':email',$email,PDO::PARAM_STR);
  $query->execute();


 $rowCount = $query->rowCount();


 if($rowCount !== 0) {
   $res = new Response();
   $res->setSuccess(false);
   $res->setHttpStatusCode(409);
   $res->addMessage("Incorrect email");
   $res->send();
   exit;
 }

}catch(PDOException $e){
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(500);
  $res->addMessage("Connection error please try again later");
  $res->send();
  exit;
}



// password logic

$hashed_password = password_hash($input->password,PASSWORD_DEFAULT);


// first day of the week logic

$fdotw = $input->fdotw;

if($fdotw !== "1" && $fdotw !== "2" && $fdotw !== "3" && $fdotw !== "4" && $fdotw !== "5" && $fdotw !== "6"  && $fdotw !== "7" ){
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(400);
  $res->addMessage("Invalid first day of the week value it must be between 1-7  (1-sun 2-mon ...) ");
  $res->setData($fdotw);
  $res->send();
  exit;
}


//countryid logic


$country_id = trim($input->country_id);

  if(!is_int((int)$country_id)){
    $res = new Response();
    $res->setSuccess(false);
    $res->setHttpStatusCode(400);
    $res->addMessage("country id must be a number");
    $res->send();
    exit;
  }


   $country_id = (int)$country_id;

  if($country_id > 239 || $country_id < 1 ){
    $res = new Response();
    $res->setSuccess(false);
    $res->setHttpStatusCode(400);
    $res->addMessage("Invalid country id");
    $res->send();
    exit;
  }





// all params passed the logic now insert to database
try{

$query = $writeDB->prepare('insert into users (name, email, password, fdotw, fk_cid) values (:name, :email, :password, :fdotw, :fk_cid)');
$query->bindParam(':name',$name,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$hashed_password,PDO::PARAM_STR);
$query->bindParam(':fdotw',$fdotw,PDO::PARAM_STR);
$query->bindParam(':fk_cid',$country_id,PDO::PARAM_INT);
$query->execute();

}catch(PDOException $e){
  $res = new Response();
  $res->setSuccess(false);
  $res->setHttpStatusCode(500);
  $res->addMessage("There was an issue signing up a new user please try again later");
  $res->send();
  exit;
}

// successfully inserted to databas now send back response

$uid = $writeDB->lastInsertId();


$returnData = array();
$returnData['id'] = $uid;
$returnData['name'] = $name;
$returnData['email'] = $email;
$returnData['created_at'] = date('r');


$res = new Response();
$res->setSuccess(true);
$res->setHttpStatusCode(201);
$res->addMessage("Successfully created a new user");
$res->setData($returnData);
$res->send();
exit;



 ?>
