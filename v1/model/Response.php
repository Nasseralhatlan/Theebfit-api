<?php

class Response {

  private $success ;
  private $httpStatusCode;
  private $messages = array();
  private $data ;
  private $toCache = false ;
  private $responseData = array();


  public function setSuccess($inputSuccess){
    $this->success = $inputSuccess;
  }

 public function setHttpStatusCode($statusCode){
        $this->httpStatusCode = $statusCode;
 }


 public function clearAllMessages(){
   unset($this->messages);
   $this->messages= array();
}

 public function addMessage($inputMessage){
       $this->messages[] = $inputMessage ;
 }


 public function setData($inputData){
   $this->data = $inputData ;
 }


 public function setToCahce($inputToCahce){
   $this->toCache = $inputToCahce ;
 }


 public function send(){

   header("Content-type: application/json;charset=utf-8");

   if($this->toCache){

       header("Cahce-control: max-age=60");

   }else{

       header("Cahce-control: no-cahce, no-store");

   }


 if( ($this->success !== false && $this->success !== true)  || is_numeric($this->httpStatusCode) !== true ){

    http_response_code(500);
    $this->clearAllMessages();
    $this->addMessage("Response creation error");
    $this->responseData["statusCode"] = 500 ;
    $this->responseData["success"] = false ;
    $this->responseData["messages"] = $this->messages ;

  }else{

    http_response_code($this->httpStatusCode);
    $this->responseData["statusCode"] = $this->httpStatusCode ;
    $this->responseData["success"] = $this->success ;
    $this->responseData["messages"] = $this->messages ;
    $this->responseData["data"] = $this->data;
  }


  echo json_encode($this->responseData);

 }






}
