<?php
namespace RaymondByczko\PhpCodfishWebsite;

class DirUtilities
{

/**
  * The max capacity of the location for generated files.
  */
public static $max_capacity = 5;
public static $max_age = 15*60*60; // in secs, corresponding to 15 minutes

public static $relativeDirectory;

static public function getRelative($ps)
{
	// $ps = $_SERVER['PHP_SELF'];
	$chunksPs = explode('/', $ps);
	$ctChunksPs = count($chunksPs);

	if ($ctChunksPs > 0)
	{
		if ($chunksPs[0] == '')
		{
			// ok - ignore it.
		}
		else
		{
			// not ok - something unexpected.
			throw new Exception('expected empty string for first element');
		}
	}

	for ($i=1; $i<$ctChunksPs; $i++)
	{
		if ($chunksPs[$i] != '')
		{
			// ok
		}	
		else
		{
			// not ok - something unexpected.
			throw new Exception('expected non-empty strings in subsequent elements:'.$chunksPs[$i]);
		}

	}

	$relPath = '';
	$numEmptyComponent = 1;
	$numFileNameComponent = 1;

	$numIgnoredComponent = $numEmptyComponent + $numFileNameComponent;
	for ($j=1; $j <= ($ctChunksPs - $numIgnoredComponent); $j++)
	{
		$relPath .= '../';
	}
	return $relPath;
}

/**
  * Store the relative directory once determined with getRelative.
  */
static public function storeRelative($relativeDirectory)
{
	self::$relativeDirectory = $relativeDirectory;
}


/**
  * @todo The static methods from here onward should
  * probably be refactored to their own website utility
  * library.
  *
  * The purpose of that will be to control resources granted
  * in a limited way based on session.  Only so many resources
  * (that is files) will be allowed to exist, and they
  * will be declared out of date, and deleted, within
  * a very short period of time.
  */

/**
  * A file of any type will be stored under
  * a dedicated directory, called baseLocationGenerated.
  * Under that directory, will be 0 or more subdirectories,
  * up to a certain limit.  That limit is expressed
  * max_capacity.
  */

/**
  * Returns the base location of generated files.
  * Under this sub-directory, there will be a number of
  * other directories.  Each of these other directories will
  * mapped directory to a session.
  */
static public function baseLocationGenerated()
{
	return 'generated';
}

/**
  * Deterimines if the location allocated for generated
  * files is full.
  *
  * At this point, the policy on 'full' is determined directly
  * here.  In the future it could be refactored to another
  * file.
  *
  */
static public function isLocationGeneratedFull()
{

	$numDirs = 0;
	$lg = self::baseLocationGenerated();
	if (!is_dir($lg))
	{
		return -1; 	// Location is not a directory.  It may
					// not have been created.
	}
	$hd = opendir($lg);
	if (!$hd)
	{
		return -2;	// Unable to opendir.  Maybe permissions?
	}
	while (($file = readdir($hd)) !== false)
	{
    	$ft = filetype($lg.$file);
    	if ($ft == 'dir')
    	{
    		$numDirs++;
    	}
    	// echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
	}
	closedir($hd);
	$isFull = ($numDirs<self::$max_capacity)?FALSE:TRUE;
	return $isFull;
}

/**
  * Removed the older files in the base location of generated files.
  *
  */
static public function removeOlderFiles()
{
	// Get the current time.
	// Get contents of base location of generated file.
	// Find entries older than current time.
	// Delete those entries.

	$currentTime = time();
	$relDir = self::getRelative($_SERVER['PHP_SELF']);
	$lg = self::baseLocationGenerated();
	if (!is_dir($relDir.$lg))
	{
		return -1; 	// Location is not a directory.  It may
					// not have been created.
	}
	$hd = opendir($relDir.$lg);
	if (!$hd)
	{
		return -2;	// Unable to opendir.  Maybe permissions?
	}
	while (($entry = readdir($hd)) !== false)
	{

		$ft = filetype($relDir.$lg.'/'.$entry);
    	if ($ft == 'dir')
    	{
    	    $statDetails = stat($relDir.$lg.'/'.$entry);	
    	    $lastModifyTime = $statDetails['mtime'];
    	    $age = $lastModifyTime - $currentTime;
    	    if ($age > self::$max_age)
	    	{
	    		unlink($relDir.$lg.'/'.$entry);
	    	}
    	}
	}
	closedir($hd);
	return 1; // Success
}

/**
  * In the generated area, did the user associated with session id given
  * as $sid, previously create anything?
  *
  * In valid operation, it returns TRUE or FALSE.
  *
  * If an error is detected, possibly mis-configuration, then an exception
  * is thrown.
  */
static public function anythingCreated($sid)
{
	$relDir = self::getRelative($_SERVER['PHP_SELF']);
	$lg = self::baseLocationGenerated();
	if (!is_dir($relDir.$lg))
	{
		throw new Exception('Base location is not a directory.');
		// return -1; 	// Location is not a directory.  It may
						// not have been created.
	}
	$hd = opendir($relDir.$lg);
	if (!$hd)
	{
		throw new Exception('Unable to opendir.');
		// return -2;	// Unable to opendir.  Maybe permissions?
	}
	while (($entry = readdir($hd)) !== false)
	{
		$ft = filetype($relDir.$lg.'/'.$entry);
    	if ($ft == 'dir')
    	{

    		if ($entry == $sid)
    		{
    			closedir($hd);
    			return TRUE;
    		}
    	}
	}
	closedir($hd);
	return FALSE;
}

static public function createSessionDir($sid)
{

}
}
?>