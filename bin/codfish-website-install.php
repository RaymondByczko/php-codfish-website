<?php
// #!/usr/bin/env php
?>
<?php
/**
  * @change_history 2019-08-17; RByczko; Added css/styles.css .
  */
?>
<?php
	echo '<pre>';
	echo 'codfish-website-install:start'."\n";
	echo '__FILE__='.__FILE__."\n";
	$currentDir = getcwd();
	echo 'currentDir='.$currentDir."\n";

	if ($handle = opendir($currentDir)) {
		echo "Directory handle: $handle\n";
		echo "Entries:\n";

		while (false !== ($entry = readdir($handle))) {
			echo "$entry\n";
			if ($entry == basename(__FILE__))
			{
				echo '...'.basename(__FILE__).' is at current directory'."\n";
			}
		}

		closedir($handle);
	}
	else
	{
		exit();
	}

	// Assume we are in vendor/bin
	// @todo call code to insure that.
	require '../../vendor/autoload.php';
	use RaymondByczko\PhpCodfishWebsite\DirUtilities;
	
	$genDir = DirUtilities::baseLocationGenerated();

	if (!is_dir($currentDir.'/../../'.$genDir))
	{
		mkdir($currentDir.'/../../'.$genDir);
	}

	$logDir = DirUtilities::logLocation();

	if (!is_dir($currentDir.'/../../'.$logDir))
	{
		mkdir($currentDir.'/../../'.$logDir);
	}

	// require 'vendor/raymond-byczko/php-codfish/LongestTitle.php';
	if (!is_dir($currentDir.'/../../TitleUtilities'))
	{
		mkdir($currentDir.'/../../TitleUtilities');
	}
	if (!is_dir($currentDir.'/../../css'))
	{

		mkdir($currentDir.'/../../css');

	}

	if (!file_exists($currentDir.'/../../TitleUtilities/createTitleDataFile.php'))
	{
		copy($currentDir.'/../../vendor/raymond-byczko/php-codfish-website/TitleUtilities/createTitleDataFile.php', $currentDir.'/../../TitleUtilities/createTitleDataFile.php');
	}
	if (!file_exists($currentDir.'/../../css/styles.css'))
	{
		copy($currentDir.'/../../vendor/raymond-byczko/php-codfish-website/css/styles.css', $currentDir.'/../../css/styles.css');
	}

	if (!file_exists($currentDir.'/../../index.php'))
	{
		copy($currentDir.'/../../vendor/raymond-byczko/php-codfish-website/index.php', $currentDir.'/../../index.php');
	}
	echo 'codfish-website-install:end'."\n";
	echo '</pre>';
?>
