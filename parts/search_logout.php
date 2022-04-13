<?php
   echo "<table style='width:100%' cellspacing='0'>";
   echo "<tr>";
   echo "<form name='frmSrch' method='post' action='$searchPage'>";
   echo "<td style='width:60%'>"; 
   echo "Search:";
   echo "<input type='text' name='txtSrchBox' id='txtSrchBox'>";
   echo "<input type='submit' name='btnSrch' id='btnSrch' value=' Go ' style='border:2px solid blue'>";
   echo "</td>";
   echo "</form>";
   echo "<form method='post' action='$index'>";
   echo "<td align='right'>";
   echo "<input type='submit' name='btnLogout' value='(Logout)' class='logoutBTN'>";
   echo "</td>";
   echo "</form>";
   echo "</tr>";
   echo "</table>";
?>