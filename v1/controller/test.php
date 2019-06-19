<?php




require_once '../model/Response.php' ;
require_once 'Database/DB.php';


         if(filter_var("@gmail.com",FILTER_VALIDATE_EMAIL)){
           $res = new Response();
           $res->setSuccess(true);
           $res->setHttpStatusCode(200);
           $res->addMessage("email is correct");
           $res->send();
         }else{
           $res = new Response();
           $res->setSuccess(true);
           $res->setHttpStatusCode(200);
           $res->addMessage("email is not valid");
           $res->send();
         }
