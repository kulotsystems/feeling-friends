<?php
   if(!isset($_SESSION['username']) || !isset($_SESSION['password']))
   {
      $_SESSION['visitProfile']="http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
	  
      echo "<script>";
      echo "window.open('$index', '_self');";
      echo "</script>";
      
      echo "<noscript>Connection lost please <a href='$index'>login</a> first..</noscript>";
      exit();
   }
?>