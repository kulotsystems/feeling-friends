<?php
   session_start();
   $path_level="";
   $view_thread=false;
   if(isset($participant1))
   {
	  $path_level="../";
	  $view_thread=true;
   }
   require $path_level . "parts/file_initializations.php";
   require $path_level . "external.php";
   require $path_level . "parts/login_checker.php";
   
   if(file_exists($msgs)==false)
   {
		$fc=fopen($msgs, "w");
		fclose($fc);
   }
   
   $timestamp=time();
   
   $recipient="";
   $looking_for="";
   if(isset($_POST["btnLook4User"]))
   {
		$looking_for=trim($_POST['txtRecipient']);
   }
   
   else if(isset($_POST['btnMsgFromProfile']))
   {
		$recipient=$_POST['btnMsgFromProfile'];
   }
   
   else if(isset($_POST['btnSend']))
   {
	   $receiver= $_POST['btnSend'];
	   $msg_content=trim($_POST['txtMsg']);
	   
	   
	   if($msg_content != "")
	   {
			//check create messages file
			if(file_exists($msgs)==false)
			{
				$fc=fopen($msgs, "w");
				fclose($fc);
			}
			
			// write or append the message record..
			$reorder_rec = "";
			$new_msg_recs="";
			$fmsgs_rec = fopen($msgs, "r");
			while(!feof($fmsgs_rec))
			{
				$msg_rec=fgets($fmsgs_rec);
				if(strlen($msg_rec)>4)
				{
					$rec_row=explode("|<<-|", $msg_rec);
					if($rec_row[1]==$_SESSION['username'] && $rec_row[2]==$receiver)
						$reorder_rec = "|<<-|" . $rec_row[1] . "|<<-|" . $rec_row[2] . "|<<-|" . "*" . "\n"; 
					else if($rec_row[1]==$receiver && $rec_row[2]==$_SESSION['username'])
						$reorder_rec = "*" . "|<<-|" . $rec_row[1] . "|<<-|" . $rec_row[2] . "|<<-|" . "\n"; 
					else
						$new_msg_recs = $new_msg_recs . $msg_rec;
				}
			}
			fclose($fmsgs_rec);
			if($reorder_rec != "")
			{
				// update message records
				$fw=fopen($msgs, "w");
				fwrite($fw, $new_msg_recs);
				fclose($fw);
				
				// move active thread to last..
				$fa=fopen($msgs, "a");
				fwrite($fa, $reorder_rec);
				fclose($fa);
				
			}
			else
			{
				// append message record
				$fa=fopen($msgs, "a");
				fwrite($fa, "|<<-|" . $_SESSION['username'] . "|<<-|" . $receiver . "|<<-|" . "*" . "\n");
				fclose($fa);
			}
	   
			//check create directory for messages
			if(file_exists($msgDir)==false)
			{
				mkdir($msgDir);
			}
			
			$conv_file = $_SESSION['username'] . "_" . $receiver . ".txt";
			if(file_exists($msgDir . $receiver . "_" . $_SESSION['username'] . ".txt"))
			{
				$conv_file=$receiver . "_" . $_SESSION['username'] . ".txt";
			}
			
			$conv_file = $msgDir . $conv_file;
			
			// check create conversation file
			if(file_exists($conv_file)==false)
			{
				$fc=fopen($conv_file, "w");
				fclose($fc);
			}
			
			// append coversation now!!
			$fa=fopen($conv_file, "a");
			fwrite($fa, $_SESSION['username'] . "{<}>" . compress_lines($msg_content) . "{<}>" . $timestamp . "\n");
			fclose($fa);
			
			
			// Also, create the message reference
			if(file_exists($msgRefs)==false)
			{
				mkdir($msgRefs);
			}
			$ref_file = $_SESSION['username'] . "_" . $receiver . ".php";
			if(file_exists($msgRefs . $receiver . "_" . $_SESSION['username'] . ".php"))
			{
				$ref_file=$receiver . "_" . $_SESSION['username'] . ".php";
			}
			$ref_file = $msgRefs . $ref_file;
			if(file_exists($ref_file)==false)
			{
				$fref=fopen($ref_file, "w");
				fwrite($fref, "<?php $" . "participant1 = '" . $_SESSION['username'] . "'; $" . "participant2 = '" . $receiver . "'; require '../messages.php';" . "?>");
				fclose($fref);
			}
			
	   
			//AYUS NA!! displaying na lang..
		}
		else
		{
			$recipient = $receiver;
		}   
   }
   
   // a function that converts a multiline content to single line..
   function compress_lines($content)
   {
		$lines=explode("\n", $content);
		
		$new_line=trim($lines[0]);
		for($i=1; $i<sizeof($lines); $i++)
		{
			$new_line=$new_line . "__/*" . trim($lines[$i]);
		}
		return $new_line;
   }
?>   
<html>
<head>
   <title>Messages - feelingfriends</title>
   <link rel="stylesheet" type="text/css" href="<?php echo $design;?>">
</head>

<body>
   <table style="width:100%; height:100%" id="mainTable"><tr><td align="center">
   <table style="width:768px; height:100%" class="body"><tr><td align="center" valign="top">
   <table style="width:88%" cellspacing="0">
      <tr>
         <td>
         <?php
            $atMessages = true;
            require $path_level . "parts/header.php";
         ?>
         </td>
      </tr>   
      <tr><td style='height:6px; font-size:5px'></td></tr>
      <tr>
		 <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>">
         <td style='background-color:#eee8cd; border:2px solid gold; border-radius:0px; border-top-left-radius:3px; border-top-right-radius:3px'>
            <small><b>Messages
			<?php
				if(isset($participant1))
				{
					$name = ($_SESSION['username']!=$participant1) ? $participant1 : $participant2;
					$target = $userPath . $name . "/index.php";
					echo " - " . "<a href='$target' style='text-decoration:none; color:black'>" . getFullName($name) . "</a>";
				}
			?>
			</b></small>
			
			<?php if($view_thread==false) : ?>
			<!--COMPOSE new-->
			<span style='float:right'>
			<small>Compose a new message to:</small>
            <input list="names" name="txtRecipient" id="txtRecipient" value="<?php echo $looking_for; ?>" style="margin-right:0px; border:0px; border-top-left-radius:3px; border-bottom-left-radius:3px; font:14px Arial, sans-serif; font-weight:bold">
            <script>document.getElementById("txtRecipient").select();</script>
			<input type="submit" name="btnLook4User" value="Ok" style="margin:0px; border:0px; border-top-left-radius:0px; border-bottom-left-radius:0px; font:14px Arial, sans-serif" class="mainButtons">
            <datalist id="names">
            <?php
               $fnames=fopen($users, "r");
               while(!feof($fnames))
               {
                  $l=fgets($fnames);
                  if(strlen($l)>3)
                  {
                     if($r[0] != $_SESSION['username'])
                     {
                        $r=explode("|/@|", $l);
                        echo "<option value='$r[2] $r[3]'>";
                     }
                  }
               }
               fclose($fnames);
            ?>
            </datalist>
			</span>
			<?php endif; ?>
         </td>
      </tr>

      <?php
		 echo "<tr>";
         echo "<td style='background-color:#eee8cd; border-radius:0px' align='right'>";
         if(isset($_POST["btnLook4User"]))
         {
            // verify the recipient..
            if($looking_for != "")
            {
               $usernames=array();
               $firstName=explode(" ", $looking_for);
			   $firstName=strtolower($firstName[0]);
               
               
               $fusers=fopen($users, "r");
               while(!feof($fusers))
               {
                  $l=fgets($fusers);
                  if(strlen($l)>3)
                  {
                     $r=explode("|/@|", $l);
                     if($r[0] != $_SESSION['username'] && (strtolower($r[0])==$firstName || strtolower($r[2])==$firstName || strtolower($r[3])==$firstName))
                     {
                        array_push($usernames, $r[0]);
                     }
                  }
               }
               fclose($fusers);
               
               $n_found=sizeof($usernames);
               if($n_found>0)
               {
                  if($n_found==1)
                     $recipient=$usernames[0];
                  else
                  {
                     //display search results
                     echo "<table style='width:97%; background-color:#f5f5ce' cellspacing='2'>";
                     for($i=0; $i<$n_found; $i++)
                     {
                        echo "<tr>";
                        echo "<td style='width:64px'>";
                        // display profile picture..
                        $goodFileExts=array("jpg", "jpeg", "gif", "png");
                        $pic=$pic_dir . "def.png";
                        foreach($goodFileExts as $e)
                        {
                           $n=$pic_dir . $usernames[$i] . ".$e";
                           if(file_exists($n))
                           {
                              $pic=$n;
                              break;
                           }
                        }
                        $target=$userPath . $usernames[$i] . "/index.php";
                        echo "<a href='$target'><img src='$pic' style='width:64px; height:64px'></a>";
                        echo "</td>";
                        
                        echo "<td>";
                        //echo "<input type='hidden' name='txtUsername' value='$usernames[$i]'>";
                        echo "<a href='$target' class='link'>" . getFullName($usernames[$i]) . "</a>";
                        echo "<br><button type='submit' name='btnAddRecipient' value='$usernames[$i]' class='mainButtons'>Message</button>";
                        echo "</td>";
                        
                        echo "</tr>";
                     }
                     echo "</table>";
                  }
               }
               else
               {
				  echo "<script>document.getElementById('txtRecipient').style.color='red';</script>";
                  echo "<noscript>";
				  echo "<small style='color:red'>* Unknown Recipient!</small>";
				  echo "</noscript>";
               }
               
               echo "</td>";
               echo "</tr>";
            }
         }
         
         else if(isset($_POST['btnAddRecipient']))
         {
            $recipient=$_POST['btnAddRecipient'];
         }
         
         // RECIPIENT NOW IDENTIFIED
		 if($recipient != "")
		 {
			echo "<tr>";
			echo "<td style='background-color:white; border-radius:0px; padding-top:5px; padding-bottom:3px'>";
			//echo "recipient is $recipient";
			echo "To: " . "<a href='$userPath$recipient/index.php' class='link'>" . getFullName($recipient) . "</a>";
			echo "<br>";
			echo "<textarea name='txtMsg' id='txtMsg' style='width:97%; float:right; margin-bottom:3px; padding:2px; border:0px; border-left:1px solid #e8e8e8; border-bottom:1px solid #e8e8e8' rows='4%'></textarea>";
			echo "<script>document.getElementById('txtMsg').select();</script>";
			echo "<br>";
			echo "<button type='submit' name='btnSend' value='$recipient' class='mainButtons' style='float:right'>Send</button>";
			echo "</td>";
			echo "</tr>";
		 
		 }
      ?>
      <!--END OF COMPOSING-->
	  <tr>
	  <td style="background-color:#f5f5dc; border-radius:0px">
		<?php
			if($view_thread==false)
			{
				if(isset($_POST["btnLook4User"])==false && isset($_POST['btnMsgFromProfile'])==false)
				{
					// go search and display for messages where current user is a participant
					$arr_msgs=file($msgs);
					echo "<table style='width:100%; border-radius:0px' cellspacing='0'>";
					for($i=sizeof($arr_msgs)-1; $i>=0; $i--)
					{
						if(strlen($arr_msgs[$i])>4)
						{
							$r=explode("|<<-|", $arr_msgs[$i]);
							if($r[1]==$_SESSION['username'] || $r[2]==$_SESSION['username'])
							{
								echo "<tr><td style='border-radius:0px; border-bottom:1px solid orange'>";
								$name_to_display = ($r[1] != $_SESSION['username']) ? $r[1] : $r[2];
								$asterisk = ($r[1] != $_SESSION['username']) ? $r[3] : $r[0];
								
								if(trim($asterisk)=="*")
								{
									echo "<b>";
								}
								
								$target = $msgRefs . $r[1] . "_" . $r[2] . ".php";
								if(file_exists($target)==false)
								{
									$target = $msgRefs . $r[2] . "_" . $r[1] . ".php";
								}
								echo "<a href='$target' class='link'>";
								echo getFullName($name_to_display);
								echo "</a>";
								
							    echo "<a href='$target' class='link' style='text-decoration:none; color:black'>";
								
								// get the last message on thread..
								$conv_file = $msgDir . $r[1] . "_" . $r[2] . ".txt";
								if(file_exists($conv_file)==false)
								{
									$conv_file = $msgDir . $r[2] . "_" . $r[1] . ".txt";
								}
								$arr_convs=file($conv_file);
								for($x=sizeof($arr_convs)-1; $x>=0; $x--)
								{
									if(strlen($arr_convs[$x])>3)
									{
										$r_conv=explode("{<}>", $arr_convs[$x]);
										if($r_conv[0] != $_SESSION['username'])
										{
											echo "<font color='blue'>:</font>";
										}
										echo " ";
										$thread=explode("__/*", $r_conv[1]);
										for($y=0; $y<sizeof($thread); $y++)
										{
											echo $thread[$y] . " ";
										}
										echo "</a>";
										echo "<br><span style='font-size:13px; color:#606060'><small>" .  getTimeDiff(trim($r_conv[2]))  . "</small></span>";
										break;
									}
								}
								if(trim($asterisk)=="*")
								{
									echo "</b>";
								}
								echo "<br><br>";
								echo "</td></tr>";
							}
						}
					}
					echo "</table>";
				}
			}
			else
			{
				// display the conversation here..
				$conv_file = $msgDir . $participant1 . "_" . $participant2 . ".txt";
				if(file_exists($conv_file)==false)
				{
					$conv_file = $msgDir . $participant2 . "_" . $participant1 . ".txt";
				}
				
				// save conversation to an array..
				$arr_conv = file($conv_file);

				// limit conversation!
				$limit=30;
				$arr_conversation = array();
				for($i=sizeof($arr_conv)-1; $i>=0; $i--)
				{
					if(strlen($arr_conv[$i])>3)
					{
						array_push($arr_conversation, $arr_conv[$i]);
						$limit--;
						if($limit==0)
						{
							break;
						}
					}
				}
				
				// display conversation now!
				for($i=sizeof($arr_conversation)-1; $i>=0; $i--)
				{
					$conv_rec = explode("{<}>", $arr_conversation[$i]);
					$sender = $conv_rec[0];
					$reply = $conv_rec[1];
					$time = trim($conv_rec[2]);
					
					echo "<span style='float:right; font-size:13px; color:#606060'><small>" .  getTimeDiff($time)  . "</small></span>";
					$target = $userPath . $sender . "/index.php";
					echo "<a href='$target' target='_blank' class='link' style='text-decoration:none'>" . getFullName($sender) ."</a>";
					
					echo "<br>";
					echo "<table style='width:100%; border-radius:0px; border-bottom:1px solid orange' cellspacing='0'>";
					echo "<tr>";
					echo "<td style='width:3%'>&nbsp;</td>";
					echo "<td>";
					$thread = explode("__/*", $reply);
					for($j=0; $j<sizeof($thread); $j++)
					{
						echo linefeed($thread[$j]) . "<br>";
					}
					echo "<br>";
					
					echo "</td>";
					echo "</tr>";
					echo "</table>";	
				}
				$recipient = ($participant1==$_SESSION['username']) ? $participant2 : $participant1;
				echo "<script>window.document.title='Messages - " . getFullName($recipient) . "';</script>";
				echo "<table style='width:100%; border-radius:0px; border-bottom:2px solid orange' cellspacing='0'>";
				echo "<tr>";
				echo "<td style='width:3%'>&nbsp;</td>";
				echo "<td>";
				
				echo "<textarea name='txtMsg' id='txtMsg' style='width:100%; margin-bottom:3px; padding:2px; border:0px; border-left:1px solid #e8e8e8; border-bottom:1px solid #e8e8e8' rows='2%'></textarea>";
				echo "<script>document.getElementById('txtMsg').select();</script>";
				echo "<br>";
				echo "<button type='submit' name='btnSend' id='btnSend' value='$recipient' class='mainButtons' style='float:right'>Reply</button>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";
			}
		?> 
	  </td>
	  </tr>
      </table>
	  </form>
      </td></tr></table>
      </td></tr></table>
</body>
<?php
	if(isset($participant1))
	{
		echo "<script>";
		echo "var tbl=document.getElementById('mainTable');";
		echo "window.scrollBy(0, (tbl.offsetHeight)-1);";
		echo "</script>";
		
		// clear asterisks on message reference
		
		$new_refs="";
		$need4update = false;
		$fr=fopen($msgs, "r");
		while(!feof($fr))
		{
			$l=fgets($fr);
			if(strlen($l)>4)
			{
				$r=explode("|<<-|", $l);
				if($r[0]=="*" && $r[1]==$_SESSION['username'])
				{
					$need4update=true;
					$l = "|<<-|" . $r[1] . "|<<-|" . $r[2] . "|<<-|" . $r[3];
				}
				else if(trim($r[3])=="*" && $r[2]==$_SESSION['username'])
				{
					$need4update=true;
					$l = $r[0] . "|<<-|" . $r[1] . "|<<-|" . $r[2] . "|<<-|" . "\n";
				}
				$new_refs=$new_refs.$l;
			}
		}
		fclose($fr);
		
		if($need4update)
		{
			$fupd=fopen($msgs, "w");
			fwrite($fupd, $new_refs);
			fclose($fupd);
		}
	}
?>
</html>