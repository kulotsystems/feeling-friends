<?php
   session_start();
   if(isset($pathed)==false)
   {
      echo "<script>window.history.back();</script>";
      echo "<noscript>Sorry, an error has occured. Please go <a href='index.php'>here</a>.</noscript>";
      exit();
   }
   
   $path_level="../../";
   require $path_level ."parts/file_initializations.php";
   require $path_level . "external.php";
   require $path_level . "parts/login_checker.php";

   $frmAction=htmlentities($_SERVER['PHP_SELF']);
   if($username==$_SESSION['username'])
   {
      $firstName=$_SESSION['firstName'];
      $lastName=$_SESSION['lastName'];
      $gender=$_SESSION['gender'];
      $birthMonth=$_SESSION['birthMonth'];
      $birthDate=$_SESSION['birthDate'];
      $birthYear=$_SESSION['birthYear'];
      $fbUsername=$_SESSION['fbUsername'];
      $address=$_SESSION['address'];
   }
   else
   {
      // get user info!
      $fr=fopen($users, "r");
      while(!feof($fr))
      {
         $l=fgets($fr);
         if(strlen($l)>3)
         {
            $row=explode("|/@|", $l);
            if($username==$row[0])
            {
               $username=$row[0];
               $firstName=$row[2];
               $lastName=$row[3];
               $gender=$row[4];
               $birthMonth=$row[5];
               $birthDate=$row[6];
               $birthYear=$row[7];
               $fbUsername=$row[8];
               $address=trim($row[9]);
            }
         }
      }
   }
   
   if(isset($_POST['btnPostStatus']))
      require $path_level . "parts/post_status.php";
   else if(isset($_POST['btnPostComment']))
      require $path_level . "parts/post_comment.php";
?>   
<html>
<head>
   <title><?php echo $firstName . " " . $lastName;?> - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr>
         <td align="center">
            <?php
               $atProfile=true;
               require $path_level . "parts/header.php";
            ?>
         </td>
      </tr> 
      <tr><td style='height:6px; font-size:5px'></td></tr>
	  
      <tr><td>
       <!-- PROFILE INFOs---------------------------------------------------------------------->
      <?php
         echo "<table style='width:100%; background-color:#ffff9a; border-radius:4px; border-bottom-left-radius:0px; border-bottom:1px solid yellow; border-left:1px solid yellow' cellspacing='0'>";
         echo "<tr><td style='background-color:#ffff9a; padding-left:6px'>";
         
         // display profile picture..
         $goodFileExts=array("jpg", "jpeg", "gif", "png");
         $pic=$pic_dir . "/def.png";
         $href = $userPath . $username . "/index.php";
         foreach($goodFileExts as $e)
         {
            $n=$pic_dir . $username . ".$e";
            if(file_exists($n))
            {
               $pic=$n;
               $href=$pic;
               break;
            }
         }
         
         echo "<a href='$href'><img src='$pic' style='width:128px; height:128px; float:right'><a>";
         
         $target = $userPath . $username . "/index.php";
         echo "<big><b><a href='$target' style='text-decoration:none; color:#303030'>$firstName $lastName</a></b></big><br>";
         
         $g=($gender=="")?"(other)":$gender;
         echo "<small>Gender: <b style='color:#404040'>$g</b></small><br>";
         if($address!="")
         {
            echo "<small>Lives in <b <b style='color:#404040'>$address</b></small><br>";
         }
         if($birthMonth!="--")
         {
            echo "<small>Born on <b <b style='color:#404040'>$birthMonth $birthDate, $birthYear</b></small><br>";
         }
         if($fbUsername!="")
         {
            if($_SESSION['username']!=$username)
            {
               $a="this person";
               if($gender=="Male")$a="him";
               if($gender=="Female")$a="her";
               echo "<small>Visit $a on <a href='http://www.facebook.com/$fbUsername'><b>Facebook</b></a></small>";
            }
            else
            {
               echo "<small>Facebook: <a href='http://www.facebook.com/$fbUsername' class='link'>www.facebook.com/<b>$fbUsername</b></a></small>"; 
            }
         }
         echo "<br><br>";
         
         if($_SESSION['username']==$username)
         {
            $target = $path_level . "edit.php";
            echo "<a href='$target' class='link'><i><b><small>Edit info</small></b></i></a>";
         }
		 echo "</td></tr>";
		 echo "</table>";
      ?>
      <!-------------------------------------------------------------------------------------->
	  </td></tr>
	  </td></tr>
      
      <tr><td style='height:6px; font-size:5px'></td></tr>
      
      <tr><td align="center">
       <?php require $path_level . "parts/status_box.php";?>
      </td></tr>
	  
      <tr><td>&nbsp;</td></tr>
      
      <!--Post all status here-->
      <?php
         $statAuthors=array();
         $statCtr=array();
         $wallOwner=array();
         $statTime=array();
         $editedOrNot=array();
         
         // check/create the reference file for all status
         if(file_exists($statusRef)==false)
         {
            $fc=fopen($statusRef, "w");
            fclose($fc);
         }
         
		 $arrAllStats=file($statusRef);
//________PRINT Status one-by-one____________________________________________________________
         $sender="profile";
         require $path_level . "parts/display_status.php";
         require $path_level . "parts/scroll_focus.php";
      ?>
   </table>
   </td></tr></table>
   </td></tr></table>
</body>
</html>