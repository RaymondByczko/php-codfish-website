<?php
require 'vendor/autoload.php';
session_name('sn-php-codfish-website');
session_start();

 
// Tell log4php to use our configuration file.
Logger::configure('config/config.xml');
 
// Fetch a logger, it will inherit settings from the root logger
$log = Logger::getLogger('index');
 
// Start logging
// $log->trace("trace message.");
$log->debug("index: start");
$sid = session_id();
$log->debug("session id=".$sid);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta name="author" content="Raymond Byczko">
	<link rel="stylesheet" href="./css/styles.css">
	<title>PHP Codfish Website | Welcome</title>
</head>
<body>
	<header>
		<div class="container">
	        <div id="branding">
	        	<h1><span class="highlight">PHP</span> Codfish</h1>
	        </div>
	        <nav>
	          <ul>
	            <li class="current"><a href="index.php">HOME</a></li>
	            <li><a href="help.php">HELP</a></li>
	            <li><a href="github.php">GITHUB</a></li>
	          </ul>
	        </nav>
	    </div>
	</header>
<p>
<section>
<div class="container2">
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
</div>
</section>
</body>
</html>
