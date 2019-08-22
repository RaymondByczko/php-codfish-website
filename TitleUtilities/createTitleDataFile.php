<?php
/**
  * session interface for this file, and therefore, back to index.php.
  *
  * Current session interface (will probably be changed)
  * ----------------------------------------------------
  *
  * makeN.index - the result of calling a static method makeN.  Its intended
  * 	for the recipient, index.php.
  *
  * isLocationGeneratedFull.index - the result of calling a static method
  *		isLocationGeneratedFull.  Its intended for the recipient, index.php.
  *
  * General discussion on session interface
  * ---------------------------------------
  *
  * There are certain things that this file needs to let another file know.
  * That is, certain things createTitleDataFile.php needs to let index.php
  * know.
  *
  * These things are:
  *		a) Were there older files that needed to be removed.[1]
  *		b) Potential error messages detected here in this file.
  *		c) Did the user previously create something?
  *		d) Is there space available for the user to create something?
  *			(In this case, the user will be informed to either wait until
  *			its removed automatically, or to download the generated file.)
  *
  *		status-user: 	"User previously created something.",
  *						"User needs to wait for short while.",
  *						"Error detected - contact support."
  *		status-dev:		"Error detected - see log.",
  *						"Older files had to be removed."
  */
?>

<?php
	header('location: /index.php');
	require '../vendor/autoload.php';

	use RaymondByczko\PhpCodfish\TitleDataFileCreateAttributes;
	use RaymondByczko\PhpCodfish\TitleUtilities;
	use RaymondByczko\PhpCodfishWebsite\DirUtilities;

	session_name('sn-php-codfish-website');
	session_start();

	$relDir = DirUtilities::getRelative($_SERVER['PHP_SELF']);
	Logger::configure($relDir.'/config/config.xml');
 
	// Fetch a logger, it will inherit settings from the root logger
	$log = Logger::getLogger('other');

	$log->debug("createTitleDataFile: start");

	$sid = session_id();
	$log->debug("session id=".$sid);

	/**
	  * Every page will call the following fragment
	  * to insure generated files are managed and removed
	  * when necessary.
	  */
	$statusRem = DirUtilities::removeOlderFiles();
	$log->debug('statusRem='.$statusRem);
	if ($statusRem != 1)
	{
		// log problem in log file.
		$log->debug('Not able to remove older files.');
	}

	// Did user create anything already?
	$ac = DirUtilities::anythingCreated($sid);

	if ($ac == TRUE)
	{
		// The user identified by $sid has already created something.
		// They are only allowed here to create one thing at a time.
		// Thus they need to download it, or in a short term,
		// it will be removed.
		$_SESSION['makeN.index'] = 'File not created.';
		$_SESSION['isLocationGeneratedFull.index'] = $isLocationGeneratedFull;
	}

	$isLocationGeneratedFull = 'Space available unknown';
	$isFull = DirUtilities::isLocationGeneratedFull();
	if ($isFull)
	{
		// Indicate to user they must wait a while
		// for space to become available.
		$isLocationGeneratedFull = 'No space available';
	}


	if (!isset($_POST['numberLines']))
	{
		echo 'numberLines not set'."\n";
		exit;
	}
	if (!isset($_POST['fileName']))
	{
		echo 'fileName not set'."\n";
		exit;
	}
	$numberLines = $_POST['numberLines'];
	$fileName = $_POST['fileName'];
	echo 'numberLines is:'.$numberLines."\n";
	echo 'fileName is:'.$fileName."\n";
	$originalExceptions = array();
	// $relDir = DirUtilities::getRelative($_SERVER['PHP_SELF']);
	DirUtilities::storeRelative($relDir);
	$locGen = DirUtilities::baseLocationGenerated();
	$locFull = DirUtilities::isLocationGeneratedFull();
	$createAttributes = TitleDataFileCreateAttributes::makeN($numberLines, $relDir.$locGen.'/'.$sid.'/'.$fileName, $originalExceptions);
	$retCreate = TitleUtilities::createTitleDataFile($createAttributes);
	$_SESSION['makeN.index'] = 'Created file';
	$_SESSION['isLocationGeneratedFull.index'] = $isLocationGeneratedFull;
	echo 'makeN.php end'."\n";

?>
