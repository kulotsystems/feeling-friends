<?php
   // scroll window..
   if(isset($_SESSION['winX']) && isset($_SESSION['winX']) && (isset($_POST['btnPostComment']) || isset($_POST['btnDelComment'])))
   {
      echo "<script>";
      $x=$_SESSION['winX'];
      $y=$_SESSION['winY']-896;
      echo "window.scrollBy($x, $y);";
	  //echo "alert('scrolling to ($x, $y)')";
      echo "</script>";
   }
   
   // focus text box.
   if(isset($_SESSION['lastTxt']) && isset($_POST['btnPostComment']))
   {
      $focusTxt=$_SESSION['lastTxt'];
      echo "<script>";
      echo "document.getElementById('$focusTxt').select();";
      echo "</script>";
   }
?>