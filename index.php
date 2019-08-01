<?php
require 'vendor/autoload.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div id="form_maken">
<form action="/TitleUtilities/createTitleDataFile.php" method="post">
Number Lines:<br>
<input type="text" name="numberLines" value="0">
<br><br>
File Name:<br>
<input type="text" name="fileName" value="">
<br><br>
<input type="submit" value="Submit">
</form>
</div>
<div id="maken_status">
<?php
if (isset($_SESSION['makeN.index']))
{
	echo $_SESSION['makeN.index'];
	session_unset();
}	
?>
</div>
</body>
</html>
