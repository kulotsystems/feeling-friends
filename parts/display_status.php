<?php
   echo "<tr><td align='center'>";
   echo "<table id='tblPink' style='width:100%; background-color:pink; padding-right:2px; padding-top:2px; padding-bottom:2px; border-bottom-left-radius:0px; border-top-right-radius:0px' cellspacing='0'>";
   $limit=29;
   $limit_ctr=0;
   for($i=sizeof($arrAllStats)-1; $i>=0; $i--)
   {
      if(strlen($arrAllStats[$i])>3)
	  {
	    $row=explode("|/@|", $arrAllStats[$i]);
		  
		// store the author
		$statAuthor = $row[0];
		  
		// store the ctr
		$statCtr=$row[1];
		  
		// store time
		$statTime=$row[2];
		  
		// store status recipient
		$wallOwner=$row[3];
		  
		// edited or not?
		$editedOrNot=trim($row[4]);
		  
		$statRefs=$statAuthor . "_" . $statCtr . ".txt";
		$statPHP=$statAuthor . "_" . $statCtr . ".php";
		$file="status/".$statRefs;
		$phpFile="status/".$statPHP;
		if($sender=="profile")
		{
			$file=$status_dir.$statRefs;
			$phpFile=$status_dir.$statPHP;
		}
		else if($sender=="view_status")
		{
			$file="../status/$statRefs";
			$phpFile="../status/$statPHP";
		}
		$go=false;
		if(file_exists($file))
		{
			if($sender=="home" || $sender=="view_status")
			{
				$go=true;
			}
			else if($sender=="profile" && ($statAuthor==$username || $wallOwner==$username))
			{
				$go=true;
			}
		}
		if($go)
		{
			$bgcolor=($i%2==0)?"#f5f5dc":"#eee8cd";
		 
			echo "<tr>";
			echo "<td id='tblCommentList$i' style='font-size:16px; padding-left:5px; padding-right:5px; padding-top:6px; border-bottom-left-radius:0px; border-top-right-radius:0px";
			if(isset($_POST['btnPostStatus']) && ($i==sizeof($arrAllStats)-1 && $statAuthor==$_SESSION['username']))
			{
				if($i%2==0)
					echo "' class='fade_1'>";
				else
					echo "' class='fade_2'>";
			}
			else
			{
				echo "; background-color:$bgcolor'>";
			}
		 
         
			echo "<b>";
			if($sender=="home" || $sender=="view_status")
			{
				echo "<a href='$userPath$statAuthor/index.php' class='link'>" . getFullName($statAuthor) . "</a>";
				if($wallOwner != "(*.*)")
				{
					echo "<font color='blue'>&nbsp&#187;&nbsp;</font>";
					echo "<a href='$userPath$wallOwner/index.php' class='link'>" . getFullName($wallOwner) . "</a>";
				}            
			}
         
			else if($sender=="profile")
			{
				$target = $userPath . $statAuthor . "/index.php";
				echo "<a href='$target' class='link'>" . getFullName($statAuthor) . "</a>";
				$addWallOwner=false;
				if($username==$_SESSION['username'] && ($statAuthor==$_SESSION['username'] && $wallOwner!="(*.*)"))
					$addWallOwner=true;
				else if($statAuthor==$username && $wallOwner!="(*.*)")
					$addWallOwner=true;
            
				if($addWallOwner)
				{
					echo "<font color='blue'>&nbsp&#187;&nbsp</font>";
					$target = $userPath . $wallOwner . "/index.php";
					echo "<a href='$target'>" . getFullName($wallOwner) . "</a>";
				}
			}
			echo "</b>";
         
			echo "<span style='float:right; font-size:14px; color:#686868'><small>" . $editedOrNot . " " . getTimeDiff($statTime) . "</small></span>";
         
			echo "<br>";
			$fread=fopen($file, "r");
			while(!feof($fread))
			{
				echo htmlspecialchars(linefeed(fgets($fread)))."<br>";
			}
			fclose($fread);
         
			echo "<span style='float:right; font-size:12px'>";
			echo "<small><a href='$phpFile' style='font-size:12px' id='link$i'  class='link'>Comment</a></small>";
			if($statAuthor==$_SESSION['username'] || $wallOwner==$_SESSION['username'])
			{
				$label = ($statAuthor==$_SESSION['username']) ? "Edit" : "Delete";
				$href=$editDir.$statAuthor . "_" . $statCtr . ".php";
				echo "<small>&nbsp;&nbsp;<a href='$href' style='font-size:12px' class='link'>$label</a></small>";
			}
			echo "</span>";
         
			// display only up to last 3 comments if it was from Home or Profile
			// and all comments if it was from 'Comments' page..
         
			// save file content to array..
			$allComments = file($comment_dir . $statRefs);
         
			$lastauthors=array();
			$lastrefs=array();
			$lastcomments=array();
			$lasttimes=array();
         
			$k = ($sender=="view_status") ? 0 : sizeof($allComments)-1;
			$j=0;
			while( ($sender=="view_status" && $k<sizeof($allComments)) || ($sender!="view_status" && $k>=0) )
			{
				$line=trim($allComments[$k]);
				if(strlen($line)>6)
				{
					$comrec=explode("||~/@||", $line);
					if( ($sender!="view_status" && $j<3) || ($sender=="view_status"))
					{
						array_push($lastauthors, $comrec[0]);
						array_push($lastcomments, $comrec[1]);
						array_push($lasttimes, $comrec[2]);
					}
					$j++;
				}
				if($sender=="view_status")
					$k++;
				else
					$k--;
			}
         
			//okay, display comments now!
			echo "<br><br>";
			echo "<table style='width:100%; border-radius:0px' cellspacing='0'>";
			echo "<tr><td style='width:3%'>&nbsp;</td>";
			echo "<td style='width:97%'>";
			
			echo "<table style='width:100%; border-radius:0px; background-color:#f5f5ce' cellspacing='0'>";
         
			$k = ($sender=="view_status") ? 0 : sizeof($lastauthors)-1;
			
			if($sender!="view_status" && $j>3 )
			{
				echo "<tr><td style='background-color:$bgcolor'><small><a href='$phpFile' style='font-size:12px' class='link'>See all comments..</a></small></td></tr>";
			}
         
			while(($sender=="view_status" && $k<sizeof($lastauthors)) || ($sender!="view_status" && $k>=0) )
			{
				echo "<tr>";
				echo "<form name='frmDeleteComment' method='post' action='$frmAction'>";
				echo "<td style='border-top:1px solid #d8d8d8; border-radius:0px; font-size:14px'";
			
				$fade=false;
				if(isset($_POST['btnPostComment']) && $lastauthors[$k]==$_SESSION['username'] && $_SESSION['new_comment']==($statRefs . $lasttimes[$k]))
				{
					echo " class='fade_comment'";
					$fade=true;
				}
				echo ">";
				echo "<a href='$userPath$lastauthors[$k]/index.php' style='font-weight:bold' class='link'>" . getFullName($lastauthors[$k]) . "</a>&nbsp;" . htmlspecialchars(trim(linefeed($lastcomments[$k])));
				echo "<br>";
            
				if($lastauthors[$k]==$_SESSION['username'] || ($statAuthor==$_SESSION['username'] && $wallOwner=="(*.*)") || $wallOwner==$_SESSION['username'])
				{
					$cmtRef=$lastauthors[$k].$lasttimes[$k];
					echo "<input type='hidden' name='txtCmtRec' value='$cmtRef'>";
					echo "<input type='hidden' name='txtCmtFile' value='$statRefs'>";
					echo "<button type='submit' name='btnDelComment' id='btnDelComment$i$k' style='background-color:#f5f5ce'";
					if($fade)
						echo " class='delCmntBTN_fade'";
					else
					{
						echo " class='delCmntBTN'";
			   
					}
					echo "><small><i><b>x</b></i></small></button>";
				}
            
				//save window scolling coordinates @ delete comment button....................................................
				accumulate_heights("tblCommentList$i");
				echo "<script>";
				echo "var b=document.getElementById('btnDelComment" . $i . $k . "');";
				echo "var txtOffsetHeight = document.getElementById('txtOffsetHeight');";
				
				//echo "b.innerHTML='0,' + txtOffsetHeight.value;";
				echo "b.value='0,' + txtOffsetHeight.value;";
				echo "</script>";
				//...........................................................................................................
            
				echo "<span style='color:#808080; text-align:right; font-size:12px'><small>" . getTimeDiff($lasttimes[$k]) . "</small></span>";
				echo "</td>";
				echo "</form>";                     
				echo "</tr>";
            
				if($sender=="view_status")
					$k++;
				else
					$k--;
			}
			if($j>0)
			{
				$str = ($j>1) ? "comments" : "comment";
				echo "<script>";
				echo "document.getElementById('link$i').innerHTML='<b>$j</b> $str';";
				echo "</script>";
			}
			echo "</table>";
			echo "<form name='frmPostComment' method='post' action='$frmAction'>";
			echo "<input type='hidden' name='txtStatusRef' value=$statRefs>";
			
			$value = $statAuthor . "[*^*~|" . $statCtr . "[*^*~|" . $wallOwner;
			echo "<input type='hidden' name='txt_Author_Ctr_Owner' value='$value'>";
          
			echo "<input type='hidden' name='txtCmntBox_Id' value='txt$i'>";            
          
			echo "<table style='width:100%' cellspacing='0' id='tblComment$i'><tr>";
			echo "<td align='right'>";
			echo "<input type='text' name='txtCommentBox' id='txt$i' style='width:90%; height:21px; font-size:15px; border:1px solid orange; background-color:#f0f0f0; border-radius:0px; border-top-left-radius:3px; border-bottom-left-radius:3px;margin:0px'>";
			echo "<button type='submit' name='btnPostComment' id='btnPostComment$i' class='cmntBTN'><small>Comment</small></button>";
          
			if($sender=="view_status")
			{
				echo "<script>document.getElementById('txt$i').select();</script>";
			}
          
			//save window scolling coordinates @ comment button....................................................
			accumulate_heights("tblComment$i");
			echo "<script>";
			echo "var b=document.getElementById('btnPostComment" . $i . "');";
			echo "var txtOffsetHeight = document.getElementById('txtOffsetHeight');";
			
			//echo "b.innerHTML='0,' + txtOffsetHeight.value;";
			echo "b.value='0,' + txtOffsetHeight.value;";
			echo "</script>";
			//.....................................................................................................
          
			echo "</td>";
			echo "</form>";
			echo "</tr></table>";
			echo "</td></tr></table>";
          
			echo "</td>";
			echo "</tr>";
			if($i>0)
			{
				echo "<tr><td id='tdStatBr_$i'style='height:6px; font-size:5px'></td></tr>";
				//accumulate_heights("tdStatBr_$i");
			}
		}
	  }
	  $limit_ctr++;
	  if($limit_ctr==$limit)break;
	}
	echo "</table>";
	echo "</td></tr>";
?>