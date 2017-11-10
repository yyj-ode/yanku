<?php
    $host=mysql_connect("localhost","root","1234");
    if($host){
        echo "ok";
    }else{
        echo "fail";
    }
?>
