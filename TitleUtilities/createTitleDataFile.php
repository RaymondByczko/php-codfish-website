<?php
	header('location: /index.php');
	require '../vendor/autoload.php';

	use RaymondByczko\PhpCodfish\TitleDataFileCreateAttributes;
	use RaymondByczko\PhpCodfish\TitleUtilities;

	session_start();

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
	$createAttributes = TitleDataFileCreateAttributes::makeN($numberLines, $fileName, $originalExceptions);
	$retCreate = TitleUtilities::createTitleDataFile($createAttributes);
	$_SESSION['makeN.index'] = 'Created file';
	echo 'makeN.php end'."\n";

?>
