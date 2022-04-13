<?php
   session_start();
   
   $path_level="../";
   require $path_level . "parts/file_initializations.php";
   
   require "../external.php";
   require "../parts/login_checker.php";
   
   $askYesNo=false;
   
   if($wallOwner==$_SESSION['username'])
   {
      $askYesNo=true;
   }
   
   $file_error=false;
   
   $fContents="";
   
   $file = $author . "_" . $ctr . ".txt";
   $s = $status_dir . $author . "_" . $ctr . ".php";
   
   if($author==$_SESSION['username'] || $wallOwner==$_SESSION['username'])
   {
      // get file contents
      $fr=fopen($status_dir.$file, "r");
      while(!feof($fr))
      {
         $fContents=$fContents . fgets($fr);
      }
      fclose($fr);
   }
   else
   {
      $file_error=true;
   }
   
   if(isset($_POST["btnUpdateStat"]) || isset($_POST['btnYesDelete']))
   {
      if(isset($_POST["btnUpdateStat"]))
      {
         $newStatus=trim($_POST["txtStatus"]);
         if(strlen($newStatus)>0)
         {
            // write the status
            $fupd=fopen($status_dir . $file, "w");
            fwrite($fupd, $newStatus);
            fclose($fupd);
         }
      }
      else
      {
         $s=$home;
         // delete the status text file
         unlink($status_dir . $file);
         
         // delete the status PHP file
         $length=strlen($file);
         unlink($status_dir . $author . "_" . $ctr . ".php");
         
         // delete the edit-status PHP file
         unlink($editDir . $author . "_" . $ctr . ".php");
         
         // delete status comments file
         unlink($comment_dir . $author . "_" . $ctr . ".txt");
      }
      
      // update reference
      $newContent="";
      $appendContent="";
      
      // delete the last status reference
      $fr=fopen($statusRef, "r");
      while(!feof($fr))
      {
         $l=fgets($fr);
         if(strlen($l)>3)
         {
            $row=explode("|/@|", $l);
            if($row[0]==$author && $row[1]==$_POST['txtCtr'])
            {
               $l="";
               $appendContent=$row[0] . "|/@|" . $row[1] . "|/@|" . time() . "|/@|" . trim($row[3]) . "|/@|" . "Edited\n";
            }
            $newContent=$newContent . $l;
          }
       }
       fclose($fr);
       $fwrUpd=fopen($statusRef, "w");
       fwrite($fwrUpd, $newContent);
       fclose($fwrUpd);
       
       if(isset($_POST["btnUpdateStat"]))
       {
          // append the new status reference
          $fapp=fopen($statusRef, "a");
          fwrite($fapp, $appendContent);
          fclose($fapp);
       }
       
       // redirect page..
       echo "<script>window.open('$s', '_self')</script>";
    }
   
   else if(isset($_POST['btnDeleteStat']))
   {
      $askYesNo=true;
   }
   else if(isset($_POST['btnNoDelete']))
   {
      echo "<script>window.open('$s', '_self')</script>";
   }
?> 
<html>
<head>
   <title>Status - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr>
         <td align="center">
            <?php require "parts/header.php";?>
         </td>
      </tr>
      <tr><td style='height:6px; font-size:5px'></td></tr>
      <?php
         if($file_error)
         {
            echo "<tr><td align='center' style='background-color:#ffffcc'>Sorry, you cannot edit or delete this status..</td></tr>";
            exit();
         }
      ?>  
      <tr>
         <td>
            <?php $title=($askYesNo)?"Deleting":"Editing";?>
			<small>
            <?php echo "<font color='#00bc00'>" . $title . " status of </font>"; ?>
            <?php
               echo "<span color:gold; text-shadow: 1px 1px 2px white'>";
               $target=$userPath . $author . "/index.php";
               echo "<u><a href='$target' style='text-decoration:none; color:gold; text-shadow: 1px 1px 2px #ff4500'>" . getFullName($author) . "</a></u>";
               if($wallOwner != "(*.*)")
               {
                  $target=$userPath . $wallOwner . "/index.php";
                  echo "<font color='gold'> &#187; </font><u><a href='$target' style='text-decoration:none; color:gold; text-shadow: 1px 1px 2px #ff4500'>" . getFullName($wallOwner) . "</a></u>";
               }
               echo "</span>";
            ?>
			<span id="spnback"><u onclick="window.history.back()">Back</u></span>
			</small>
      </tr>
      
      <form name="frmEditStatus" method="post" action="<?php htmlentities($_SERVER['PHP_SELF']);?>">
      <tr>
         <td align="center">
            <textarea name="txtStatus" id="txtStatus" rows="8" <?php if($askYesNo) echo 'readonly'; ?>><?php echo $fContents;?></textarea>
         </td>
      </tr>
      <tr>
         <input type="hidden" name="txtCtr" value="<?php echo $ctr; ?>">
         <td align="right" style="border-bottom:1px solid gold; border-bottom-right-radius:0px; padding-bottom:2px">
         <?php
            if($askYesNo==false)
            {
               echo "<input type='submit' name='btnDeleteStat' value='Delete' class='mainButtons' style='background-color:red; border-top-right-radius:0px; border-bottom-right-radius:0px; width:60px; font-size:14px'>";
               echo "<input type='submit' name='btnUpdateStat' value='Update' class='mainButtons' style='border-top-left-radius:0px; border-bottom-left-radius:0px; width:60px; font-size:14px'>";
            }
            else
            {
               echo "<div style='background-color:#ffff9a; border-radius:3px; padding:1px'>";
               echo "<i style='color:crimson'><small>Are you sure you want to DELETE this status?&nbsp;</small></i>";
               echo "<input type='submit' name='btnYesDelete' value='Yes' class='mainButtons'>";
               echo "<input type='submit' name='btnNoDelete' value='No' class='mainButtons' style='background-color:#ffff9a; color:#404040; font-weight:bold'>";               
               echo "</div>";
            }
         ?>
         </td>
      </tr>
      </form>
   </td></tr></table>
   </td></tr></table>
   <script>
      document.getElementById("txtStatus").focus();
   </script>
</body>
</html>