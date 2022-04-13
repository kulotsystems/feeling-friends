<?php
   echo "<table style='width:100%; padding:0px; background-color:white; border-top-left-radius:0px; border-bottom-right-radius:0px' cellspacing='0'>";
   echo "<tr>";
   echo "<form method='post' action='$messages'>";
   echo "<td style='background-color:white'><i><small>";
   
   if(isset($atProfile))
   {
      if($username!=$_SESSION['username'])
	  {
         echo "Write to $firstName ";
		 $pronoun=getFullName($username, "gender");
		 
		 if($pronoun=="his")
			$pronoun="him";
		 else if($pronoun=="a")
		 {
			$pronoun="";
		 }
		 
		 echo "or <button type='submit' name='btnMsgFromProfile' id='btnMsgFromProfile' value='$username'>send $pronoun a private message</button>";
	  }
		 
      else
         echo "Say something..";
   }
   else
   {
      echo "Say something..";  
   }
   
   echo "</small></i></td>";
   echo "</form>";
   echo "<td>&nbsp;</td>";
   echo "</tr>";
   $postAction=htmlentities($_SERVER['PHP_SELF']);
   echo "<form name='frmPostStatus' method='post' action='$postAction'>";
   echo "<tr>";
   echo "<td colspan='2'>";
   echo "<textarea name='txtStatus' id='txtStatus'></textarea>";
   if(isset($_POST['btnPostStatus'])==false && isset($_POST['btnPostComment'])==false && isset($_POST['btnDelComment'])==false)
   {
		echo "<script>document.getElementById('txtStatus').select();</script>";
   }
   echo "</td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align='right' colspan='2'><input type='submit' name='btnPostStatus' value=' Post '  class='mainButtons'></td>";
   echo "</tr>";
   echo "</form>";
   echo "</table>";
?>