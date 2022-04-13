<?php
   session_start();
   
   $path_level="../";
   require $path_level . "parts/file_initializations.php";
   require $path_level . "external.php";
   require $path_level . "parts/login_checker.php";
   
   $file=$username . "_". $ctr . ".txt";
   $time="";
   $wallowner="";
   $edited="";
   // fetch other status info
   $fr=fopen($statusRef, "r");
   while(!feof($fr))
   {
      $l=fgets($fr);
      if(strlen($l)>3)
      {
         $row=explode("|/@|", $l);
         if($row[0]==$username && $row[1]==$ctr)
         {
            $time=$row[2];
            $wallowner=$row[3];
            $edited=trim($row[4]);
            break;
         }
      }
   }
   fclose($fr);
   
   // clear this comment in notifications
   $newContent="";
   $notif_found=false;
   $file=$notifDir . $_SESSION['username'] . ".txt";
   $fr=fopen($file, "r");
   while(!feof($fr))
   {
      $l=trim(fgets($fr));
      if(strlen($l)>5)
      {
         $r=explode("|(o))|", $l);
         if($r[2]==$username && $r[3]==$ctr && $r[0]=="")
         {
            $l = "*" . $l;
            $notif_found=true;
         }
         $newContent = $newContent . $l . "\n";
      }
   }
   fclose($fr);
   
   if($notif_found)
   {
      $fupd=fopen($file, "w");
      fwrite($fupd, $newContent);
      fclose($fupd);
   }
   
   
   
?>

<html>
<head>
   <title>Comments - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
   <?php
      
      if(isset($_POST['btnPostComment']))
      {
         require $path_level . "parts/post_comment.php";
      }
      echo "<tr><td>";
      require $path_level . "parts/header.php";
      echo "</td></tr>";
      
      echo "<tr><td style='height:6px; font-size:5px'></td></tr>";
      
	  $arrAllStats=array($username . "|/@|" . $ctr . "|/@|" . $time . "|/@|" . $wallowner . "|/@|" . $edited);
      $sender="view_status";
      $frmAction=$_SERVER['PHP_SELF'];
      require $path_level . "parts/display_status.php";
      require $path_level . "parts/scroll_focus.php";
   ?>
   </table>
   </td></tr></table>
   </td></tr></table>
</body>
</html>