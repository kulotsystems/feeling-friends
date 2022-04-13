<?php
   date_default_timezone_set("Asia/Manila");

   // function that retrieves the full name [or other details] associated with a specified username
   function getFullName($username, $otherDetails="")
   {
      global $users;
      $return="";
      $fr=fopen($users, "r");
      while(!feof($fr))
      {
         $line=fgets($fr);
         if(strlen($line)>3)
         {
            $user=explode("|/@|", $line);
            if($user[0]==$username)
            {
               if($otherDetails=="")
                  $return = htmlspecialchars($user[2])." ".htmlspecialchars($user[3]);
               else if($otherDetails=="gender")
               {
                  $gender=$user[4];
                  $return="a";
                  if($gender=="Male")
                     $return="his";
                  else if($gender=="Female")
                     $return="her";
               }
            }
         }
      }
      fclose($fr);
      return $return;
   }
   
   // function that returns the "ago" time
   function getTimeDiff($timeRec)
   {  
      $diff = intval(time() - intval($timeRec));
      
      $msg="";
      
      if($diff >= 0)
      {
         $sec=1;
         $min=60;
         $hr=$min*60;
         $dy=$hr*24;
         $wk=$dy*7;
         $mnth=$dy*30;
         $yr=$mnth*12;
      
         // within this day
         if($diff<86400)
         {
            if($diff==0)
               $msg="Just now";
            else
            {
               $nHours=intval($diff/$hr);
               if($nHours>0)
               {
                  $diff=$diff % $hr;
                  $msg=$msg . $nHours . " hr";
                  ($nHours>1)?$msg=$msg."s":$msg=$msg."";
               }
               
               $msg=($nHours>0) ? $msg.", " : $msg;
               
               $nMinutes=intval($diff/$min);
               if($nMinutes>0)
               {
                  $diff=$diff % $min;
                  $msg=$msg . $nMinutes . " min";
                  ($nMinutes>1)?$msg=$msg."s":$msg=$msg;
               }
               if($nHours<=0)
               {
                  $msg=($nMinutes>0) ? $msg.", " : $msg;
                  $nSeconds=intval($diff/$sec);
                  $msg=$msg . $nSeconds . " sec";
                  ($nSeconds>1)?$msg=$msg."s":$msg=$msg;
               }
               
               $msg=$msg." ago";
            }
         }
         else
         {
            $timeRec=$timeRec . dateDetails(getdate(intval($timeRec)));
            $timeDetails = explode("./)", $timeRec);
            $dayDetail = $timeDetails[1];
            $monthDetail = $timeDetails[2];
            $dateDetail = $timeDetails[3];
            $yearDetail = $timeDetails[4];
            $hourDetail = $timeDetails[5];
            $minuteDetail = $timeDetails[6];
            
            // yesterday
            if($diff<172800)
               $msg="Yesterday @ $hourDetail:$minuteDetail";
            
            // within this week
            else if($diff<604800)
               $msg="$dayDetail @ $hourDetail:$minuteDetail";
               
            // within this month
            else if($diff<2592000)
               $msg="$monthDetail $dateDetail @ $hourDetail:$minuteDetail";
            
            // within this year
            else if($diff<31536000)
               $msg="$monthDetail $dateDetail";
            else
            {
               $msg="$monthDetail $dateDetail, $yearDetail";
            }
         }
      }
      return $msg;
   }         
   
   // function that returns the current date and time of a given timestamp..
   function dateDetails($d)
   {
      $wday=$d['wday'];
      $weekday="Sun";
      switch($wday)
      {
         case 1: $weekday="Mon"; break;
         case 2: $weekday="Tue"; break;
         case 3: $weekday="Wed"; break;
         case 4: $weekday="Thu"; break;
         case 5: $weekday="Fri"; break;
         default: $weekday="Sat";
      }
      $mon=$d['mon'];
      $month="Jan";
      switch($mon)
      {
         case 2: $month="Feb"; break;
         case 3: $month="Mar"; break;
         case 4: $month="Apr"; break;
         case 5: $month="May"; break;
         case 6: $month="Jun"; break;
         case 7: $month="Jul"; break;
         case 8: $month="Aug"; break;
         case 9: $month="Sept"; break;
         case 10: $month="Oct"; break;
		 case 11: $month="Nov"; break;
         default: $month="Dec";
      }
      $mday=$d['mday'];
      $year=$d['year'];
      $hours=$d['hours'];
      $AMorPM=($hours>=0 && $hours<12)?"am":"pm";
      if($hours>12) $hours=$hours-12;
      if($hours==0) $hours=12;
      $minutes=$d['minutes'];
      if($minutes<10) $minutes="0".$minutes;
      return "./)" . $weekday . "./)" . $month . "./)" . $mday . "./)" . $year . "./)" . $hours . "./)" . $minutes.$AMorPM;
   }
   
   // function that checks wether a string contains a certain character
   function contains($str, $chr)
   {
      $bool=false;
      for($i=0; $i<strlen($str); $i++)
      {
         if(substr($str, $i, 1)==$chr)
         {
            $bool=true;
            break;
         }
      }
      return $bool;
   }
   
   // delete a comment
   if(isset($_POST['btnDelComment']))
   {
      $comref=$_POST['txtCmtRec'];
      // find and delete that comment record!
      $cmntsFile=$comment_dir . $_POST['txtCmtFile'];
      $newFile="";
      $fr=fopen($cmntsFile, "r");
      while(!feof($fr))
      {
         $line=fgets($fr);
         if(strlen($line)>6)
         {
            $rec=explode("||~/@||", $line);
            $comline=$rec[0].trim($rec[2]);
            if(trim($comref)==trim($comline))
            {
               $line="";
            }
            $newFile=$newFile.$line;
         }
      }
      fclose($fr);
      
      // write the new 'comments' file
      $fupd=fopen($cmntsFile, "w");
      fwrite($fupd, $newFile);
      fclose($fupd);
      
      // save scrolling pixels
	  echo "<noscript>";
	  $scrolling = "0,10";
	  echo "</noscript>"; 
	  
      $scrolling = $_POST['btnDelComment'];
      $coord=explode(",", $scrolling);
      
      $_SESSION['winX']=intval($coord[0]);
      $_SESSION['winY']=intval($coord[1]);
	  
	  $a=$_SESSION['winX'];
	  $b=$_SESSION['winY'];
   }
   
   // a function that accumulates element offsetHeights for scrolling purposes..
   function accumulate_heights($id)
   {
		echo "<script>";
		echo "var txtOffsetHeight = document.getElementById('txtOffsetHeight');";
		echo "var e = document.getElementById('$id');";
		echo "txtOffsetHeight.value = parseInt(txtOffsetHeight.value) + parseInt(e.offsetHeight);";
		echo "</script>";
   }
   
    // function to put endline on long words..
    function linefeed($line)
    {
       $return="";
       if(strlen($line)>60)
       {
          $words=explode(" ", $line);
          for($j=0; $j<sizeof($words); $j++)
          {
             if(strlen($words[$j])>60)
             {
                $endline="";
				for($xx=0; $xx<strlen($words[$j]); $xx++)
				{
					$endline = $endline . substr($words[$j], $xx, 1);
					if($xx % 60 == 0)
						$endline = $endline . "\n";
				}
				$words[$j]=$endline;
             }
          }
		  $return = implode(" ", $words);
       }
       else
       {
          $return=$line;
       }
       return $return;
    }
?>