<?php
   session_start();
   
   $path_level="";
   require "parts/file_initializations.php";
   require "external.php";
   require "parts/login_checker.php";
?>

<html>
<head>
   <title>Search - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
   <table style="width:100%; height:100%"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr><td align="center">
      <?php
         require "parts/header.php";
      ?>
      </td></tr>   
      <tr><td style='height:6px; font-size:5px'></td></tr>
   </table>
   
   <?php
      if(isset($_POST['btnSrch']))
      {
         $qry=trim($_POST['txtSrchBox']);
         if(strlen($qry)>0)
         {
            $_SESSION['srchQuery']=$qry;
         }
      }
      
      echo "<table style='width:88%; background-color:#ffffcc' cellspacing='0'>";
      if(isset($_SESSION['srchQuery']))
      {
         $users="uesrs.txt";
         $fsrch=fopen($users, "r");
         $userFound=false;
         
         echo "<tr><td style='color:#404040'>Search results for <b>'" . $_SESSION['srchQuery'] . "'</b></td></tr>";
         $nSrch=0;
         $s_username="";
         while(!feof($fsrch))
         {
            $l=fgets($fsrch);
            if(strlen($l)>3)
            {
               $row=explode("|/@|", $l);
               if(strtolower($_SESSION['srchQuery'])==strtolower($row[0]) || strtolower($_SESSION['srchQuery'])==strtolower($row[2] . " " . $row[3]) || strtolower($_SESSION['srchQuery'])==strtolower($row[2]) || strtolower($_SESSION['srchQuery'])==strtolower($row[3]))
               {
                  $nSrch+=1;
                  
                  $s_username=$row[0];
                  $s_firstName=$row[2];
                  $s_lastName=$row[3];
                  $s_gender=$row[4];
                  $s_address=trim($row[9]);
                  
                  echo "<tr><td style='border-top:1px solid gray'>";
                  
                  // display profile picture..
                  $goodFileExts=array("jpg", "jpeg", "gif", "png");
                  $pic=$pic_dir . "def.png";
                  foreach($goodFileExts as $e)
                  {
                     $n=$pic_dir . $s_username . ".$e";
                     if(file_exists($n))
                     {
                        $pic=$n;
                        break;
                     }
                  }
                  $target="users/$row[0]/index.php";
                  echo "<a href='$target'><img src='$pic' style='width:128px; height:128px; float:right'></a>";
                  
                  echo "<br><big><b><a href='$target'>$s_firstName $s_lastName</a></b></big>";
                  
                  echo "<br>";
                     
                  $g=($s_gender=="")?"(other)":$s_gender;
                  echo "<small>Gender: <b style='color:#404040'>$g</b></small><br>";
                  
                  if($s_address!="")
                  {
                     echo "<small>Lives in <b style='color:#404040'>$s_address</b></small>";
                  }   
                  echo "<br></br>"; 
                  echo "</td></tr>";
               }
            }
         }
         fclose($fsrch);
         
         // instantly go to profile if search result is only one
         if($nSrch==1)
         {
            $openProfile="users/$s_username/index.php";
            echo "<script>window.open('$openProfile', '_self')</script>";
         }
      }
      echo "</table>";
   ?>
   </table>
   </td></tr></table>
   </td></tr></table>
   <script>
      document.getElementById("txtSrchBox").select();
   </script>
</body>
</html>