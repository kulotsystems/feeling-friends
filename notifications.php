<?php
   $path_level="";
   if(isset($clear_notifications))
   {
      $path_level="../";
   }
   
   if(isset($ignore_session_start)==false)
   {
      session_start();
      require $path_level . "parts/file_initializations.php";
      require $path_level . "external.php";
      require $path_level . "parts/login_checker.php";
   }
   
   // clear notifications
   if(isset($clear_notifications))
   {
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
            if($r[0]=="")
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
      echo "<script>window.open('$home', '_self')</script>";
   }
?>   
<html>
<head>
   <title>Notifications - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <?php
   if(isset($ignore_session_start)==false)
   {
      echo "<table style='width:100%; height:100%'><tr><td align='center'>";     
      echo "<table style='width:768px; height:100%' class='body'><tr><td align='center' valign='top'>";
	  echo "<table style='width:88%' cellspacing='0'>";
   }
   else
      echo "<table style='width:100%' cellspacing='0'>";
   ?>
      <tr>
         <td align="center">
         <?php
            if(isset($ignore_session_start)==false)
            {
               $atNotif=true;
               require $path_level . "parts/header.php";
            }
         ?>
         </td>
      </tr>  
      <tr><td style='height:6px; font-size:5px'></td></tr>
         <?php
            $notif_file = $notifDir . $_SESSION['username'] . ".txt";
            
            $allNotifs=array();
            $notifs_to_delete=array();
            if(file_exists($notif_file))
            {
               // save all file contents to array..
               if(isset($notif_at_home)==false)
               {
                  $allNotifs=file($notif_file);
               }
               else
               {
                  $fr=fopen($notif_file, "r");
				  $limit=0;
                  while(!feof($fr))
                  {
                     $l=fgets($fr);
                     if(strlen($l)>5)
                     {
                        $r=explode("|(o))|", $l);
                        if($r[0]=="")
                        {
                           array_push($allNotifs, $l);
						   $limit++;
						   if($limit>=4)
						        break;
                        }
                     }
                  }
                  fclose($fr);
               }
			   
			   echo "<tr><td align='center'>";
               
               $has_notifs=false;
               echo "<table style='width:100%; background-color:gold; border-radius:3px' cellspacing='0'>";
               echo "<tr><td style='background-color:#eee8cd; border:2px solid gold; border-radius:3px'>";
               echo "<small>";
			   echo "<b>Notifications&nbsp;</b>";
               if(sizeof($allNotifs)>0)
               {  
                     if($path_level=="")
                        echo "<a id='linkClearNotif' href='" . $path_level . "parts/clear_notifs.php' class='link'>(Nevermind)</a>";
                     else
                        echo "cleared!";
               }
			   echo "</small>";
               echo "</td></tr>";
               
               $hasNewNotif=false;   
               for($i=sizeof($allNotifs)-1; $i>=0; $i--)
               {
                  if(strlen($allNotifs[$i])>6)
                  {                       
                     // wallpost(w): *| type | author | ctr | time
                     // comment(c):  *| type | author | ctr | commentor | wallOwner | time
                     
                     $rec=explode("|(o))|", $allNotifs[$i]);
                     
                     $bgcolor = "#f5f5dc";
                     if($rec[0]=="")
                     {
                        $bgcolor="#ffdebb";
                        $hasNewNotif=true;
                     }
                     $type=$rec[1];
                     $author=$rec[2];
                     $ctr=$rec[3];
                     $time=$rec[4];
                     
                     $fname=$status_dir . $author . "_" . $ctr;
                     $status_file = $fname . ".txt";
                     
                     if(file_exists($status_file)==false)
                     {
                        array_push($notifs_to_delete, $allNotifs[$i]);
                        $allNotifs[$i]="";
                        continue;
                     }
                     else
                     {
                        //display the notification
                        $has_notifs=true;
                        $status_link = $fname . ".php";
                        
                        $wall_link = $userPath . $_SESSION['username'] . "/index.php";
                        
                        if($type=="w")// a wall post------------------------------------------
                        {
                           echo "<tr><td style='background-color:$bgcolor; border-radius:3px; font-size:15px; padding-top:5px'>";
                           if(isset($ignore_session_start)) echo "<a href='$status_link' class='notif_link'>";
						   
						   if(isset($ignore_session_start)==false) echo "<a href='$userPath$author/index.php' class='notif_link'>";
						   echo getFullName($author);
						   if(isset($ignore_session_start)==false) echo "</a>";
						   echo "&nbsp;";
						   
                           if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='link'>";
						   echo "<b>wrote</b>";
						   if(isset($ignore_session_start)==false) echo "</a>";
						   
						   echo " on ";
						   if(isset($ignore_session_start)==false) echo "<a href='$wall_link' class='notif_link'>";
						   echo "your wall";
						   if(!isset($ignore_session_start)) echo "</a>";
						   
                           // status pre-read..
                           echo ": <i><b>";
						   if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='link'>";
						   echo "\"";
                           $status=file_get_contents($status_file);
                           echo substr($status, 0, 48);
                           if(strlen($status)>48) echo "..";
                           echo "\"";
						   if(isset($ignore_session_start)==false) echo "</a>";
						   echo "</b></i>";
                           
						   
						   if(isset($ignore_session_start)) echo "</a>";
						   echo "<br><span style='font-size:13px; color:#404040'><small>" . getTimeDiff(trim($time)) . "</small></span>";
                           echo "</td></tr>";
                           echo "<tr><td style='height:1px'></td></tr>";
                        }
                        
                        else if($type=="c") // a comment--------------------------------------
                        {
                           //$rec[4]; - commentor
                           //$rec[5]; - wallOwner
                           
                           $time=$rec[6];
                           
                           // check if the comment still exists..
                           $comment_exists=false;
                           $fcheck=fopen($comment_dir . $author . "_" . $ctr . ".txt", "r");
                           while(!feof($fcheck))
                           {
                              $l_chk=fgets($fcheck);
                              if(strlen($l_chk)>6)
                              {
                                 $r_chk=explode("||~/@||", $l_chk);
                                 if($r_chk[0]==$rec[4] && trim($r_chk[2])==trim($time))
                                 {
                                    $comment_exists=true;
                                    break;
                                 }
                              }
                           }
                           fclose($fcheck);
                           
                           // if comment doesn't exist anymore, delete the notification.. 
                           // delete that notification record!
                           if($comment_exists==false)
                           {
                              array_push($notifs_to_delete, $allNotifs[$i]);
                              $allNotifs[$i]="";
                              continue;
                           }
                           
                           // don't display current notification if a link to its status
                           // has already been displayed!
                           
                           $already_linked=false;
                           for($h=$i+1; $h<sizeof($allNotifs); $h++)
                           {
                              if(strlen($allNotifs[$h])>5)
                              {
                                 $r=explode("|(o))|", $allNotifs[$h]);
                                 if($r[1]=="c")
                                 {
                                    if($r[2]==$author && $r[3]==$ctr && $r[4]==$rec[4])
                                    {
                                       $already_linked=true;
                                       break;
                                    }
                                 }
                              }
                           }
                           
                           if($already_linked==false)
                           {
                              echo "<tr><td style='background-color:$bgcolor; border-radius:3px; font-size:15px; padding-top:5px'>";
                              $str="commented";
                              $poster="";
                              $owner="";

                              // determine poster name/pronoun based from status author
                              if($author==$_SESSION['username'])
                                 $poster="your";
                              else if($author==$rec[4])
                              {
                                 $poster=getFullName($author, "gender");
                                 $str="also commented";
                              }
                              else
                              {
                                 $poster=getFullName($author);
                              }
                           
                              // determine owner name/pronoun based from wallOwner
                              if($rec[5]=="(*.*)" || $rec[5]==$_SESSION['username'])
                              {
                                 $owner="your";
                              }
                              else if($rec[4]==$rec[5])
                              {
                                 $owner=getFullName($rec[4], "gender");
                                 $str="also commented";
                              }
                              else
                              {
                                 $owner=getFullName($rec[5]) . "'s";
                              }
                           
                              // print notification for comments
							  if(isset($ignore_session_start)) echo "<a href='$status_link' class='notif_link'>";
							  
							  if(isset($ignore_session_start)==false) echo "<a href='$userPath$author/index.php' class='notif_link'>";
							  echo getFullName($rec[4]);
							  if(isset($ignore_session_start)==false) echo "</a>";
							  echo "&nbsp;";
							  
                              if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='link'>";
							  echo "<b>$str</b>";
							  if(isset($ignore_session_start)==false) echo "</a>";
							  
							  echo " on ";
							  
                              if($poster=="your" && $owner=="your")
							  {
								echo "your ";
								if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='notif_link'>";
								echo "status";
								if(isset($ignore_session_start)==false) echo "</a>";
							}
							  
                              else if(($author==$rec[4] && $rec[4]==$rec[5]) || $rec[5]=="(*.*)")
                              {
								
								
                                 if($poster!="his" && $poster!="her" && $poster!="a")
                                 {
                                    if(isset($ignore_session_start)==false) echo "<a href='" . $userPath . $author . "/index.php" . "' class='notif_link'>";
									echo $poster;
									if(isset($ignore_session_start)==false) echo "</a>";
									
									echo "'s ";
									if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='notif_link'>";
									echo "status";
									if(isset($ignore_session_start)==false) echo "</a>";
                                 }
                                 else
								 {
								 
                                    echo $poster;
									if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='notif_link'>";
									echo "&nbsp;status";
									if(isset($ignore_session_start)==false) echo "</a>"; 
								}									
                              }
                              else
                              {
								
                                 if($author != $rec[4] && $author != $_SESSION['username'])
								 {
                                    if(isset($ignore_session_start)==false) echo "<a href='". $userPath . $author . "/index.php" . "' class='notif_link'>";
									echo $poster;
									if(isset($ignore_session_start)==false) echo "</a>";
									echo "'s ";
									
									if(isset($ignore_session_start)==false) echo "<a href='$status_link' class='notif_link'>";
									echo "post";
									if(isset($ignore_session_start)==false) echo "</a>";
								}
                                else
								{
                                    echo $poster;
									if(isset($ignore_session_start)==false) echo " <a href='$status_link' class='notif_link'>";
									echo " post";
									if(isset($ignore_session_start)==false) echo "</a>";
								}
                                 if($owner != "a")
                                 {
                                    if($rec[5]=="(*.*)")
                                       $wall_link=$userPath . $author . "/index.php";
                                    else
                                       $wall_link=$userPath . $rec[5] . "/index.php";
                                    echo " on ";
									if(isset($ignore_session_start)==false) echo "<a href='$wall_link' class='notif_link'>";
									echo $owner;
									echo " wall";
									if(isset($ignore_session_start)==false) echo "</a>";
                                 }
                              }
							  if(isset($ignore_session_start)) echo "</a>";
                              echo "<br><span style='font-size:13px; color:#404040'><small>" . getTimeDiff(trim($time)) . "</small></span>";
							  
							  
                              echo "</td></tr>";
                              echo "<tr><td style='height:1px'></td></tr>";
                           }
                        }
                     }
                  }
               }
               
               //delete void notification record
               if(sizeof($notifs_to_delete)>0)
               {
                  $newContent="";
                  $fr=fopen($notif_file, "r");
                  while(!feof($fr))
                  {
                     $l=fgets($fr);
                     for($j=0; $j<sizeof($notifs_to_delete); $j++)
                     {
                        if(trim($l)==trim($notifs_to_delete[$j]))
                        {
                           $l="";
                           break;
                        }
                     }
                     $newContent=$newContent.$l;
                  }
                  fclose($fr);
                  
                  $fupd=fopen($notif_file, "w");
                  fwrite($fupd, $newContent);
                  fclose($fupd);
               }
               
               if($has_notifs==false)
               {
                  echo "<tr><td align='center' style='background-color:#eee8cd; border:1px solid gold'>(No notifications)</td></tr>";
               }
               if($hasNewNotif==false)
               {
                  echo "<script>document.getElementById('linkClearNotif').style.visibility='hidden'</script>";
               }
               echo "</table>";
			   echo "</td></tr>";
			   
            }
         ?>
      </table>
   <?php
   if(isset($ignore_session_start)==false)
   {
      echo "</td></tr></table>";
      echo "</td></tr></table>";
   }
   ?>
</body>
</html>