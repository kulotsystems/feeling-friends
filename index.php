<?php
   session_start();
   
   require "external.php"; 
   $regUsername="";
   $regPassword="";
   $regRePassword="";
   $regFirstName="";
   $regLastName="";
   
   $loginUsername="";
   $regSuccessfull=false;
   $errMsg="";
   $users="uesrs.txt";
   
   if(isset($_POST['btnRegister']))
   {
	  session_destroy();
	  
      
      // check / create user info storage
      if(file_exists($users)==false)
      {
         $createUsers=fopen($users, "w");
         fclose($createUsers);
      }
      
      $regUsername=strtolower(trim($_POST['txtUsername']));
      if($regUsername=="")
         $errMsg="Please enter a username.";
      else if(contains($regUsername, "'") || contains($regUsername, "/") || contains($regUsername, "\\") || contains($regUsername, "*") || contains($regUsername, "%") || contains($regUsername, ".") || contains($regUsername, '"'))
         $errMsg="An error occured while saving the username.";
      else
      {
         // check if username is available!
         $fr=fopen($users, "r");
         while(!feof($fr))
         {
            $l=fgets($fr);
            if(strlen($l)>3)
            {
               $row=explode("|/@|", $l);
               if($regUsername==$row[0])
               {
                  $errMsg="Username is not available.";
                  break;
               }
            }
         }
         fclose($fr);
      }
      if($errMsg=="") //this means username is available
      {
         $regPassword=$_POST['txtPassword'];
         $regRePassword=$_POST['txtRePassword'];
         $regFirstName=ucwords(trim($_POST['txtFirstName']));
         $regLastName=ucwords(trim($_POST['txtLastName']));
         
         if($regPassword=="" || $regRePassword=="" || $regFirstName=="" || $regLastName=="" || $regUsername=="(*.*)" || $regUsername=="def")
            $errMsg="All fields cannot be empty";
         else if(contains($regPassword, "'"))
            $errMsg="An error occured while saving the password.";
         else if(contains($regFirstName, "'"))
            $errMsg="An error occured while saving your first name.";
         else if(contains($regLastName, "'"))
            $errMsg="An error occured while saving your last name.";
         else 
         {
            if(strlen($regPassword)<6)
               $errMsg="Password cannot be less than 6-chars long.";
            else if($regPassword != $regRePassword)
               $errMsg="Passwords don't match.";
            else
            {
               // no more errors to catch, save user now!
               $fa=fopen($users, "a");
               fwrite($fa, $regUsername . "|/@|" . $regPassword . "|/@|" . $regFirstName . "|/@|" . $regLastName . "|/@|" . "" . "|/@|" . "--" . "|/@|" . "1" . "|/@|" . "1990" . "|/@|" . "" . "|/@|" . "" . "\n");
               fclose($fa);
               
               // write user's index file
               $usersDir="users";
               if(file_exists($usersDir)==false)
               {
                  mkdir($usersDir);
               }
               
               chdir($usersDir);
               
               if(file_exists($regUsername)==false)
               {
                  mkdir($regUsername);
               }
               
               $fw=fopen($regUsername . "/index.php", "w");
               fwrite($fw, "<?php $" . "username='$regUsername'; $" . "pathed=true; require '../../profile.php';?>");
               fclose($fw);
               
               $notifDir="notifications";
               chdir("../");
               
               // write user's notification file
               if(file_exists($notifDir)==false)
               {
                  mkdir($notifDir);
               }
               
               chdir($notifDir);

               $fw=fopen($regUsername . ".txt", "w");
               fclose($fw);
 
               $regSuccessfull=true;
               $loginUsername=$regUsername;
            }
         }
      }
   }

   else if(isset($_POST['btnLogout']))
   {
      session_destroy();
   }
   else
   {
      if(isset($_SESSION['username']) && isset($_SESSION['password']))
      {
         echo "<script>";
         echo "window.open('home.php', '_self');";
         echo "</script>";
      
         echo "<noscript>";
         echo "<font color='green'>Already logged in! Check out <a href='home.php'>here</a><br>Please enable Javascript in your browser!</font>";
         exit();
         echo "</noscript>";
      }
   }
?>

<html>
<head>
   <title>feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:520px" class="body"><tr><td align="center">
   <table style="width:512px; background-color:gold" cellspacing='0'><tr><td align="center">
   <table style="width:98%; background-color:orange" cellspacing='0'>
      <tr><td  colspan="3">
         <table><tr>
            <td><a href="index.php"><img src="parts/f.png"></a></td>
            <td>
               <b style="font-size:24px">
                  <a href="index.php" style="text-decoration:none"><font color="#ff4500"><span style="font: 40px Pristina, sans-serif">feeling</span><span style="font: 36px Comic Sans MS, sans-serif">friends</span></font></a>
               </b>
            </td>
         </tr></table>
      </td></tr>
      
      <tr>
         <td style="width:10%">&nbsp;</td>
         <td rowspan="3" style="background-color:gold; border-radius:0px" align="center">
            <table>
               <form name="frmLogin" method="post" action="home.php">
               <?php
                  if($regSuccessfull)
                     echo "<font color='green'><b>&nbsp;Registered! You may now login&nbsp;</b></font>";
                  else
                     echo "&nbsp;";
					
				if(isset($_SESSION['visitProfile']))
				{
					echo "<font color='red'><b>&nbsp;Please login first..&nbsp;</b></font>";
				}
				else if(isset($_SESSION['invalid_account']))
				{
					echo "<font color='red'><b>&nbsp;Invalid account!&nbsp;</b></font>";
				}
               ?>
               <tr>
                  <td>Username:</td>
                  <td><input type="text" name="txtUsername" id="txtLoginUsername" class="inpText" value="<?php if(isset($_SESSION['invalid_account'])){ echo htmlspecialchars($_SESSION['invalid_account']); unset($_SESSION['invalid_account']);}else echo htmlspecialchars($loginUsername); ?>"></td>
               </tr>
               <tr>
                  <td>Password:</td>
                  <td><input type="password" name="txtPassword" id="txtLoginPassword" class="inpText"></td>
               </tr>
               <tr>
                  <td colspan="2" align="right"><input type="submit" name="btnLogin" value="Login" class="mainButtons"></td>
               </tr>
               </form>
            </table>
            <br>
            <?php
               if($errMsg != "")
                  echo "<font color='red'><b>$errMsg</b></font>";
               else
                  echo "&nbsp;";
            ?>
            <table style="background-color:#ffff9a">
            <form name="frmRegister" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>">
               <tr>
                  <th colspan="2" style="color:green">Register here</th>
               </tr>
               <tr>
                  <td>Username:</td>
                  <td><input type="text" name="txtUsername" id="txtRegUsername" class="inpText" value="<?php if($errMsg != "") echo htmlspecialchars($regUsername); ?>"></td>
               </tr>
               <tr>
                  <td>Password:</td>
                  <td><input type="password" name="txtPassword" id="txtRegPassword" class="inpText" value="<?php if($errMsg != "") echo htmlspecialchars($regPassword); ?>"></td>
               </tr>
               <tr>
                  <td>Retype:</td>
                  <td><input type="password" name="txtRePassword" id="txtRegRePassword" class="inpText" value="<?php if($errMsg != "") echo htmlspecialchars($regRePassword); ?>"></td>
               </tr>
               <tr>
                  <td><br>First name:</td>
                  <td></br><input type="text" name="txtFirstName" id="txtRegFirstName" class="inpText" value="<?php if($errMsg != "") echo htmlspecialchars($regFirstName); ?>"></td>
               </tr>
               <tr>
                  <td>Last name:</td>
                  <td><input type="text" name="txtLastName" id="txtRegLastName" class="inpText" value="<?php if($errMsg != "") echo htmlspecialchars($regLastName); ?>"></td>
               </tr>
               <tr>
                  <td colspan="2" align="right"><input type="submit" name="btnRegister" value="Register" class="mainButtons"></td>
               </tr>
               </form>
            </table>
            <br>
         </td>
         <td style="width:10%">&nbsp;</td>
      </tr>
      
      <tr>
         <td>&nbsp;</td>
         <td>&nbsp;</td>
      </tr>
      
      <tr>
         <td  style="border-radius:0px; background-color:black">&nbsp;</td>
         <td  style="border-radius:0px; background-color:black">&nbsp;</td>
      </tr>
      <tr><td  colspan="3" align="right"><small style="color:gold">kapatalmana@gmail.com</small></td></tr>
   </table>
   </td></tr></table>
   </td></tr></table>
   </td></tr></table>
   <?php
      echo "<script>";
      if($regSuccessfull)
         echo "document.getElementById('txtLoginPassword').select();";
      else if(isset($_POST['btnRegister']))
      {
         echo "var textboxes=new Array('txtRegUsername', 'txtRegPassword', 'txtRegRePassword', 'txtRegFirstName', 'txtRegLastName');";
         echo "for(var i=0; i<textboxes.length; i++){";
            echo "var textbox=document.getElementById(textboxes[i]);";
            echo "if(textbox.value==''){";
               echo "textbox.select();";
               echo "break;}}";
      }
      else
         echo "document.getElementById('txtLoginUsername').select();";
      echo "</script>";

      // UPDATE 2022 :: Create record files if they don't exist
      $files = ['ctr.txt', 'msesages.txt', 'statursefs.txt', 'uesrs.txt'];
      foreach ($files as $file) {
          if(!file_exists($file)) {
              $fw = fopen($file, 'w');
              fclose($fw);
          }
      }
   ?>
</body>
</html>