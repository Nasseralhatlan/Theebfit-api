<?php




require_once '../model/Response.php' ;
require_once 'Database/DB.php';

         $a = date('r');
         $res = new Response();
         $res->setSuccess(true);
         $res->setHttpStatusCode(200);
         $res->addMessage($a);
         $res->addMessage(strlen($a));
         $res->send();
