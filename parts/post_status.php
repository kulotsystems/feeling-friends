<?php
   // Check/create directory for all status..--------------
   if(file_exists($status_dir)==false)
   {
      mkdir($path_level . "status");
   }
   //------------------------------------------------------
   
   // get the entered status 
   $status=trim($_POST['txtStatus']);
   if(strlen($status)>0)
   {
      $privacy="(*.*)";
      if($_SESSION['username']!=$username)
      {
         $privacy=$username;
      }
      $goodStatus=true;
      if(isset($_SESSION['lastStatus']))
      {
         if($_SESSION['lastStatus']==$status.$privacy)
         $goodStatus=false;
      }
      if($goodStatus)
      {
         $statCtr=$ctr_file;
         if(file_exists($statCtr)==false)
         {
            $fc=fopen($statCtr, "w");
            $n=0;
            
            //get the last ctr on statusref
            if(file_exists($statusRef))
            {
               $ctrs=array();
               $fscan=fopen($statusRef, "r");
               while(!feof($fscan))
               {
                  $l=fgets($fscan);
                  if(strlen($l)>3)
                  {
                     $srow=explode("|/@|", $l);
                     array_push($ctrs, trim($srow[1]));
                  }
               }
               if(sizeof($ctrs)>0)
               {
                  $n=intval($ctrs[sizeof($ctrs)-1])+1;
               }
            }
            fwrite($fc, $n);
            fclose($fc);
         }
         $ctr=intval(file_get_contents($statCtr));
         
         // reference the status
         $fadd=fopen($statusRef, "a");
         if($_SESSION['username']!=$username)
         {
            // write notifications: type | author | ctr | time
            $notif_file = $notifDir . $username . ".txt";
            if(file_exists($notif_file))
            {
               $fa=fopen($notif_file, "a");
               fwrite($fa, "" . "|(o))|" .  "w" . "|(o))|" .  $_SESSION['username'] . "|(o))|" . $ctr . "|(o))|" . time() . "\n");
               fclose($fa);
            }  
         }
         fwrite($fadd, $_SESSION['username'] . "|/@|" . $ctr . "|/@|" . time() . "|/@|" . $privacy .  "|/@|" . ""  . "\n");
         fclose($fadd);
         
         $fname = $_SESSION['username'] ."_" . $ctr;
         
         // save the status text file!
         $fstat=fopen($status_dir . $fname . ".txt", "w");
         fwrite($fstat, $status);
         fclose($fstat);
         
         // save the status php file!
         $fstat=fopen($status_dir . $fname . ".php", "w");
         fwrite($fstat, "<?php $" . "username='" . $_SESSION['username']  . "'; $" . "ctr=" . $ctr . "; require '../comments.php';?>");
         fclose($fstat);
         
         // save the status edit php file!
         $fstat=fopen($editDir . $fname . ".php", "w");
         fwrite($fstat, "<?php $" . "author='" . $_SESSION['username'] . "'; $" . "wallOwner='" . $privacy . "'; $" . "ctr=" . $ctr . "; require '../status.php';?>");
         fclose($fstat);
         
         // save status comments file
         $fw=fopen($comment_dir . $fname . ".txt", "w");
         fclose($fw);
         
         //increment $ctr for status id
         $fupd=fopen($statCtr, "w");
         $ctr+=1;
         fwrite($fupd, $ctr);
         fclose($fupd);
		 
      }
      $_SESSION['lastTxt']="txtStatus";
      $_SESSION['lastStatus']=$status.$privacy;
   }
?>