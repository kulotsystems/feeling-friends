<?php
   session_start();
   require "external.php";
   
   $goodFileExts=array("jpg", "jpeg", "gif", "png");
   
   $path_level="";
   require "parts/file_initializations.php";
   require "parts/login_checker.php";
   
   $dir="uploads";
   $updMsg="";
   $color="green";
   if(isset($_POST['btnUpdate']))
   {
      $newPassword=$_POST['txtPassword'];
      $newFirstName=trim($_POST['txtFirstName']);
      $newLastName=trim($_POST['txtLastName']);
      $newGender=$_POST['optGender'];
      $newBMonth=$_POST['cboMonth'];
      $newBDate=trim($_POST['txtDate']);
      $newBYear=trim($_POST['txtYear']);
      $newFbUsername=trim($_POST['txtFbUsername']);
      $newAddress=trim($_POST['txtAddress']);
      
      if($newPassword=="" || $newFirstName=="" || $newLastName=="")
      {
         $updMsg="UPDATE FAILED: Account info was incomplete!";
         $color="red";
      }
      else if(contains($newPassword, "'") || contains($newFirstName, "'") || contains($newLastName, "'") || contains($newFbUsername, "'") || contains($newAddress, "'"))
      {
         $updMsg="Sorry, an error occured while saving your info";
         $color="red";
      }
      else if($newPassword==$_SESSION['password'] && $newFirstName==$_SESSION['firstName'] && $newLastName==$_SESSION['lastName'] && $newGender==$_SESSION['gender'] && $newBMonth==$_SESSION['birthMonth'] && $newBDate==$_SESSION['birthDate'] && $newBYear==$_SESSION['birthYear'] && $newFbUsername==$_SESSION['fbUsername'] && $newAddress==$_SESSION['address'])
      {
         $updMsg="No changes committed!";
         $color="blue";
      }
      else
      {
         $users="uesrs.txt";
         if(file_exists($users)==false)
         {
            $createUsers=fopen($users, "w");
            fclose($createUsers);
         }
         
         $fr=fopen($users, "r");
         $newContent="";
         while(!feof($fr))
         {
            $l=fgets($fr);
            if(strlen($l)>3)
            {
               $row=explode("|/@|", $l);
               if($row[0]==$_SESSION['username'])
               {
                  $_SESSION['password']=$newPassword;
                  $_SESSION['firstName']=htmlspecialchars($newFirstName);
                  $_SESSION['lastName']=htmlspecialchars($newLastName);
                  $_SESSION['gender']=$newGender;
                  $_SESSION['birthMonth']=$newBMonth;
                  $_SESSION['birthDate']=htmlspecialchars($newBDate);
                  $_SESSION['birthYear']=htmlspecialchars($newBYear);
                  $_SESSION['fbUsername']=htmlspecialchars($newFbUsername);
                  $_SESSION['address']=htmlspecialchars($newAddress);
                  $l=$_SESSION['username']. "|/@|" . $newPassword. "|/@|" . $newFirstName. "|/@|" . $newLastName. "|/@|" . $newGender. "|/@|" . $newBMonth. "|/@|" . $newBDate. "|/@|" . $newBYear. "|/@|" . $newFbUsername. "|/@|" . $newAddress . "\n";
               }
               $newContent=$newContent.$l;
            }
         }
         fclose($fr);
         
         $fupd=fopen($users, "w");
         fwrite($fupd, $newContent);
         fclose($fupd);
         
         $updMsg="Account info successfully updated!";
         $color="green";
      }
      // FILE UPLOAD
      if(!($_FILES["pic"]["error"]>0))
      {
         $fname=$_FILES["pic"]["name"];
         
         // split file name into name and extension
         $name="";
         $ext="";
         $length=strlen($fname);
         for($i=$length-1; $i>=0; $i--)
         {
            if(substr($fname, $i, 1)==".")
            {
               $name=substr($fname, 0, $i);
               $ext=substr($fname, $i+1, $length-($i+1));
               break;
            }
         }
         
         // verify file format and file extension
         $ftype=$_FILES["pic"]["type"];
         
         if(($ftype=="application/octet-stream" || $ftype=="image/jpg" || $ftype=="image/jpeg" || $ftype=="image/gif" || $ftype=="image/png" || $ftype=="image/pjpeg" || $ftype=="image/x-png") && in_array($ext, $goodFileExts))
         {
            // check create the directory for all uploaded  files
            
            if(file_exists($dir)==false)
            {
               mkdir($dir);
            }
            
            $picName=$dir . "/" . $_SESSION["username"] . ".$ext";
            // if file already exists, delete it first!
            foreach($goodFileExts as $e)
            {
               $n=$dir . "/" . $_SESSION["username"] . ".$e";
               if(file_exists($n))
               {
                  unlink($n);
               }
            }
            
            // move the uploaded file from 'tmp' to $dir..
            move_uploaded_file($_FILES["pic"]["tmp_name"], $picName);
            $updMsg="Profile pic changed!";
            $color="green";  
         }
         else
         {
            $updMsg=" Invalid profile pic! ";
            $color="red";
         }
      }      
   }
?>
<html>
<head>
   <title>Account - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr>
         <td>
            <?php require "parts/header.php";?>
         </td>
      </tr>   
      <tr><td style='height:6px; font-size:5px'></td></tr>
      <tr>
      <td align="center" style="background-color:gold">
     
   <table style="background-color:#ffff9a; padding-left:9px; padding-right:9px" cellspacing='0'>
      <tr>
         <?php
         if($updMsg != "")
         {
            echo "<td colspan='2' style='width:100%; border-radius:0px; color:$color; background-color:yellow; text-align:center'>$updMsg</td>";
         }
         else
            echo "<td colspan='2'>&nbsp;</td>";
         ?>
      </tr>
      <form name="frmUpdateInfo" enctype="multipart/form-data" method="post" action="<?php htmlentities($_SERVER['PHP_SELF']);?>">
      <tr>
         <td colspan="2" align="center">Username: <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></td>
      </tr>
      
      <tr>
         <td colspan="2" align="center">
         <?php
            // display profile picture..
            $pic=$dir . "/def.png";
            $href='edit.php';
            foreach($goodFileExts as $e)
            {
               $n=$dir . "/" . $_SESSION['username'] . ".$e";
               if(file_exists($n))
               {
                  $pic=$n;
                  $href=$pic;
                  break;
               }
            }
            echo "<a href='$href'><img src='$pic' style='width:128px; height:128px'></a><br>";
         ?>
         <input type="file" name="pic" id="pic">
         </td>
      </tr>
      
      <tr>
         <td><br>Password</td>
         <td><br><input type="password" name="txtPassword" value="<?php echo htmlspecialchars($_SESSION['password']); ?>" style="font-weight:bold; width:100%" class="inpText"></td>
      </tr>
      
      <tr>
         <td>First name:</td>
         <td><input type="text" name="txtFirstName" value="<?php echo $_SESSION['firstName'];?>" style="font-weight:bold; width:100%" class="inpText"></td>
      </tr>
      
      <tr>
         <td>Last name:</td>
         <td><input type="text" name="txtLastName" value="<?php echo $_SESSION['lastName'];?>" style="font-weight:bold; width:100%" class="inpText"></td>
      </tr>
      
      <tr>
         <td>Address:</td>
         <td><input type="text" name="txtAddress" value="<?php echo $_SESSION['address'];?>" style="font-weight:bold; width:100%" class="inpText"></td>
      </tr>
      
      <tr>
         <td>Gender:</td>
         <td align="center">
            <input type="radio" name="optGender" class="inpText" value="Male" <?php if($_SESSION['gender']=="" || $_SESSION['gender']=="Male") echo "checked";?>>Male
            <input type="radio" name="optGender" class="inpText" value="Female" <?php if($_SESSION['gender']=="Female") echo "checked";?>>Female 
         </td>
      </tr>
      
      <tr>
         <td>Birthday:</td>
         <td align="center">
            <select name="cboMonth" class="inpText">
               <?php
                  $months=array("--", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
                  for($i=0; $i<sizeof($months); $i++)
                  {
                     $optState=($_SESSION['birthMonth']==$months[$i])?"selected":"";
                     echo "<option $optState>$months[$i]</option>";
                  }
               ?>
            </select>
            <input type="number" name="txtDate" min="1" max="31" class="inpText" style="width:55px; text-align:center" value="<?php echo $_SESSION['birthDate']; ?>">
            <input type="number" name="txtYear" min="1970" max="2013" class="inpText" style="width:90px; text-align:center" value="<?php echo $_SESSION['birthYear']; ?>">
         </td>
      </tr>
      
      <tr>
         <td>Facebook username:</td>
         <td><input type="text" name="txtFbUsername" class="inpText" value="<?php echo $_SESSION['fbUsername'];?>" style="font-weight:bold; width:100%"></td>
      </tr>
      
      <tr>
         <td colspan="2" align="center"><br><input type="submit" name="btnUpdate" value="Update Account" class="mainButtons"></td>
      </tr>
      </form>
   </table>
   </td></tr></table>
   </td></tr></table>
</body>
</html>