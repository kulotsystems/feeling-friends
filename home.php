<?php
   session_start();
   $has_new_notifs=false;
   
   $path_level="";
   require "parts/file_initializations.php";
   require "external.php";
   
   if(isset($_POST['btnLogin']))
   {
	  $userFound=false;
      $username=strtolower(trim($_POST['txtUsername']));
      $password=$_POST['txtPassword'];
      
      // verify session user
      $fr=fopen($users, "r");
      while(!feof($fr))
      {
         $l=fgets($fr);
         if(strlen($l)>3)
         {
            $row=explode("|/@|", $l);
            if($username==$row[0] && $password==$row[1])
            {
               $_SESSION['username']=$row[0];
               $_SESSION['password']=$row[1];
               $_SESSION['firstName']=htmlspecialchars($row[2]);
               $_SESSION['lastName']=htmlspecialchars($row[3]);
               $_SESSION['gender']=$row[4];
               $_SESSION['birthMonth']=$row[5];
               $_SESSION['birthDate']=htmlspecialchars($row[6]);
               $_SESSION['birthYear']=htmlspecialchars($row[7]);
               $_SESSION['fbUsername']=htmlspecialchars($row[8]);
               $_SESSION['address']=htmlspecialchars(trim($row[9]));
               $userFound=true;
               if(isset($_SESSION['visitProfile']))
               {
                  $page=$_SESSION['visitProfile'];
                  echo "<script>window.open('$page', '_self');</script>";
				  unset($_SESSION['visitProfile']);
               }
               break;
            }
         }
      }
      fclose($fr);
      if(!$userFound)
      {
         echo "<script>";
         //echo "alert('Invalid account!..');";
		 $_SESSION['invalid_account']=$username;
         echo "window.open('index.php', '_self');";
         echo "</script>";
         
         echo "<noscript>Invalid account! Please <a href='index.php'>try again</a>..</noscript>";
         exit();
      }
      $_SESSION['lastTxt']="txtStatus";
   }
   $scriptPush="index.php";
   $noscriptPush="index.php";
   require "parts/login_checker.php";
   
   if(isset($_POST['btnPostStatus']))
   {
      $username=$_SESSION['username'];
      require $path_level . "parts/post_status.php";
   }
   
   else if(isset($_POST['btnPostComment']))
      require $path_level . "parts/post_comment.php";
?>   
<html>
<head>
   <title>feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr>
         <td align="center" id="tdHeader">
            <?php
               $atHome=true;
               require "parts/header.php";
            ?>
         </td>
      </tr>
	  <?php //accumulate_heights("tdHeader");?>
      
      <tr><td style='height:6px; font-size:5px' id="tdBr_1"></td></tr>
	  <?php //accumulate_heights("tdBr_1");?>
      
      <tr><td align="center" id="tdStatus">
         <?php
            if($has_new_notifs)
            {
               $ignore_session_start=true;
               $notif_at_home=true;
               require $notif;
               echo "<br>";
            }
            require $path_level . "parts/status_box.php";
         ?>
      </td></tr>
	  <?php accumulate_heights("tdStatus");?>
      
      <tr><td id="tdBr_2">&nbsp;</td></tr>
	  <?php //accumulate_heights("tdBr_2");?>
      
      <!--Post all status here-->
      <?php
         $statAuthors=array();
         $statCtr=array();
         $wallOwner=array();
         $statTime=array();
         $editedOrNot=array();
         $frmAction=htmlentities($_SERVER['PHP_SELF']);
         
         // check/create the reference file for all status
         if(file_exists($statusRef)==false)
         {
            $fc=fopen($statusRef, "w");
            fclose($fc);
         }
		 
		 $arrAllStats=file($statusRef);
//________PRINT Status one-by-one____________________________________________________________
         
         $sender="home";
         require "parts/display_status.php";
         echo "</table>";
         require "parts/scroll_focus.php";
      ?>
   </table>
   </td></tr></table>
   </td></tr></table>
</body>
</html>