<?php
   echo "<input type='hidden' id='txtOffsetHeight' value='0'>";
   
   $home=(isset($home))?$home:"home.php";
   $notif=(isset($notif))?$notif:"notifications.php";
   $icon=($home=="home.php")?"parts/f.png":"../../parts/f.png";
   if($home=="../home.php") $icon="../parts/f.png";
   
   echo "<table style='width:100%; background-color:gold'>";
   
   echo "<tr>";
   echo "<form name='frmSrch' method='post' action='$searchPage'>"; 
   echo "<td style='width:60%; background-color:#ffa500'>";
   
   echo "<table cellspacing='0' style='width:100%'>";
   echo "<tr><td rowspan='2'>";
   echo "<a href='$index'><img src='$icon'></a>";
   echo "</td>";
   
   echo "<td>";
   echo "<input list='names' name='txtSrchBox' id='txtSrchBox'>";
   echo "<datalist id='names'>";
   $fnames=fopen($users, "r");
   while(!feof($fnames))
   {
		$l=fgets($fnames);
		if(strlen($l)>3)
		{
			$r=explode("|/@|", $l);
			echo "<option value='$r[2] $r[3]'>";
		}
   }
   fclose($fnames);
   echo "</datalist>";
   echo "<input type='submit' name='btnSrch' id='btnSrch' value='Search'>";
   echo "</td>";
   
   echo "<tr><td id='menu'>";
   echo "<ul>";
   
   echo "<li><a href='$home'";
   if(isset($atHome))
   {
      echo " style='color:crimson; background-color:gold'";
   }
   echo ">Home</a></li>";
   
   echo "<li><a <a href='" . $userPath . $_SESSION['username'] . "/index.php'";
   if(isset($atProfile) && $username==$_SESSION['username'])
   {
      echo " style='color:crimson; background-color:gold'";
   }
   echo ">Profile</a></li>";
   
   echo "<li><a href='$notif'";
   if(isset($atNotif))
   {
      echo " style='color:crimson; background-color:gold'";
   }
   
   // determine how many new notifications are there..
   $n=0;
   $allNotifs=file($notifDir . $_SESSION['username'] . ".txt");
   for($i=sizeof($allNotifs)-1; $i>=0; $i--)
   {
      if(strlen($allNotifs[$i])>5)
      {
         $rec=explode("|(o))|", $allNotifs[$i]);
         if($rec[0]=="")
         {
            if($rec[1]=="w")
               $n++;
            else
            {
               $already_counted=false;
               for($h=$i+1; $h<sizeof($allNotifs); $h++)
               {
                  if(strlen($allNotifs[$i])>5)
                  {
                     $rec_1=explode("|(o))|", $allNotifs[$h]);
                     if($rec_1[1]=="c" && $rec[2]==$rec_1[2] && $rec[3]==$rec_1[3] && $rec[4]==$rec_1[4])
                     {
                        $already_counted=true;
                        break;
                     }
                  }
               }
               if($already_counted==false)
                  $n++;
            }
         }
      }
   }
   echo ">Notif's";
   if($n>0)
   {
      echo "(<b>$n</b>)";
      if(isset($has_new_notifs))
      {
         $has_new_notifs=true;
      }
   }
   echo"</a></li>";
   
   // determine how many unread messages are there
   $mrefs=file($msgs);
   $n_msg=0;
   for($i=sizeof($mrefs)-1; $i>=0; $i--)
   {
		if(strlen($mrefs[$i])>4)
		{
			$r=explode("|<<-|", $mrefs[$i]);
			if( ($r[1]==$_SESSION['username'] && $r[0]=="*") || ($r[2]==$_SESSION['username'] && trim($r[3])=="*") )
			{
				$n_msg++;
			}
		}
		
   }
   echo "<li><a href='$messages'";
   if(isset($atMessages))
   {
      echo " style='color:crimson; background-color:gold'";
   }
   echo ">Msg's";
   if($n_msg>0)
   {
		echo "(<b>$n_msg</b>)";
   }
   
   echo "</a></li>";
   
   
   
   echo "</ul>";
   echo "</td></tr>";
   echo "</table>";
   
   echo "</td>";
   echo "</form>";
   
   echo "<form method='post' action='$index'>";
   echo "<td style='background-color:gold' align='center'>";
   echo "<table><tr>";
   
   // display profile picture..
   echo "<td>";
   $pic_dir=(isset($pic_dir)==false) ? "uploads/" : $pic_dir;
   if($home=="../home.php") $pic_dir="../uploads/";
   $goodFileExts=array("jpg", "jpeg", "gif", "png");
   $pic=$pic_dir . "/def.png";
   foreach($goodFileExts as $e)
   {
      $n=$pic_dir . $_SESSION['username'] . ".$e";
      if(file_exists($n))
      {
         $pic=$n;
         break;
      }
   }
   echo "<a href='" . $userPath . htmlspecialchars($_SESSION['username']) . "/index.php'><img src='$pic' style='width:60px; height:60px; border:2px solid orange'></a>";
   echo "</td>";
   
   // display name and logout button
   echo "<td>";
   echo "<a href='" . $userPath . htmlspecialchars($_SESSION['username']) . "/index.php' style='text-decoration:none; color:#404040'><b>" . $_SESSION['firstName'] . " " .   $_SESSION['lastName'] . "</b></a>";
   echo "<br><button type='submit' name='btnLogout' class='logoutBTN' style='float:right'><small>Out >></small></button>";
   echo "</td>";
   
   echo "</tr></table>";
   echo "</td>";
   echo "</form>";
   echo "</tr>";
   echo "</table>";
?>