<?php
   echo "<noscript>";
   $scrolling = "0,10";
   echo "</noscript>";
   $scrolling = $_POST['btnPostComment'];
   
   $coord=explode(",", $scrolling);
   $_SESSION['winX']=intval($coord[0]);
   $_SESSION['winY']=intval($coord[1]);
   
   $a=$_SESSION['winX'];
   $b=$_SESSION['winY'];
   
   
   $status_details=$_POST['txt_Author_Ctr_Owner'];
   $details=explode("[*^*~|", $status_details);
   
   $status_author=$details[0];
   $status_ctr=$details[1];
   $status_wallOwner=$details[2];
   
   $fname=$status_author . "_" . $status_ctr . ".txt";
   $file=$comment_dir . $fname;
   if(file_exists($file))
   {
      $timestamp=time();
      $comment=trim($_POST['txtCommentBox']);
      if(strlen($comment)>0)
      {
         $goodComment=true;
         if(isset($_SESSION['lastComment']))
         {
            if($_SESSION['lastComment']==$comment.$file)
            $goodComment=false;
         }
         if($goodComment)
         {
			
            $fcom=fopen($file, "a");
            fwrite($fcom, $_SESSION['username'] . "||~/@||" . $comment . "||~/@||" . $timestamp . "\n");
            fclose($fcom);
			
			$_SESSION['new_comment']=$fname . $timestamp;
			
            $_SESSION['lastComment']=$comment.$file;
            $_SESSION['lastTxt']=$_POST['txtCmntBox_Id'];
            
            //get the participants on this status
            $participants = array();
            if($status_author != $_SESSION['username'])
            {
               array_push($participants, $status_author);
            }
            if($status_wallOwner != "(*.*)" && $status_wallOwner != $_SESSION['username'])
            {
               array_push($participants, $status_wallOwner);
            }
            $comments=$comment_dir . $status_author . "_" . $status_ctr . ".txt";
            $fcom=fopen($comments, "r");
            while(!feof($fcom))
            {
               $l=fgets($fcom);
               if(strlen($l)>6)
               {  
                  $r=explode("||~/@||", $l);
                  if($r[0] != $_SESSION['username'])
                  {
                     //add username to array if it is unique..
                     $distinct=true;
                     for($n=0; $n<sizeof($participants); $n++)
                     {
                        if($r[0]==$participants[$n])
                        {
                           $distinct=false;
                           break;
                        }
                     }
                     if($distinct)
                     {
                        array_push($participants, $r[0]);
                     }
                  }
               }
            }
            fclose($fcom);
            
            
            // write the notification to every participant
            for($m=0; $m<sizeof($participants); $m++)
            {
               $file = $notifDir . $participants[$m] . ".txt";
               $fw=fopen($file, "a");
               fwrite($fw, "|(o))|" . "c" . "|(o))|" . $status_author . "|(o))|" . $status_ctr . "|(o))|" . $_SESSION['username'] . "|(o))|" . $status_wallOwner . "|(o))|" . $timestamp . "\n");
               fclose($fw);
            }
         }
      }
   }
?>
