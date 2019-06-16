<?php




require_once '../model/Response.php' ;
require_once 'Database/DB.php';



     try{

          $writeDB = DB::connectWriteDB();

          $query = $writeDB->prepare("select * from `countries` ");
          $query->execute();


          $c = array();
          $num = 0 ;

          while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $c['country_'.$num] = $row['nicename'];
                $num = $num+1;
         }


         $res = new Response();
         $res->setSuccess(true);
         $res->setHttpStatusCode(200);
         $res->addMessage("Query success");
         $res->setData($c);
         $res->send();


     }catch(PDOException $e){
       $res = new Response();
       $res->setSuccess(false);
       $res->setHttpStatusCode(500);
       $res->addMessage("Database connection error");
       $res->send();
     }
