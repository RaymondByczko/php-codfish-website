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

/**
  * Returns the location where logs are stored for this application.
  * @todo This may be refactored into another class.
  */
static public function logLocation()
{
	return 'log';
}

/**
  * Given a pathname given by $ps, this method gets
  * the relative directory back to the root, or beginning,
  * of the path given by $ps.
  *
  * $ps should begin with '/', otherwise an Exception is thrown.
  * To allow $ps not not start with a '/', set $startWithFS to
  * FALSE.
  *
  * Generally, this method is given $_SERVER['PHP_SELF'].
  * So the call if it looks like:
  *
  * DirUtilities::getRelative($_SERVER['PHP_SELF']
  *
  * Beginning with forward slash can be confusing.  PHP_SELF
  * begins with a '/', so it looks like the beginning of the
  * file system.  Its actually from the document root.
  */
static public function getRelative($ps, $startWithFS=TRUE)
{
	// $ps = $_SERVER['PHP_SELF'];
	$chunksPs = explode('/', $ps);
	$ctChunksPs = count($chunksPs);
	echo "\n".'... ps='.$ps."; startWithFS==".var_export($startWithFS, TRUE)."\n";
	echo "\n".'... ctChunksPs='.$ctChunksPs."\n";


	if ($ctChunksPs > 0)
	{
		if ($chunksPs[0] == '')
		{
			// ok - ignore it.
		}
		else
		{
			if ($startWithFS == TRUE)
			{
				// not ok - something unexpected.
				throw new \Exception('expected empty string for first element');
			}
		}
	}

	if ($startWithFS)
		$iStart = 1;
	else
		$iStart = 0;

	for ($i=$iStart; $i<$ctChunksPs; $i++)
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
	$numEmptyComponent = NULL;
	$jStart = NULL;
	if ($startWithFS)
	{
		$numEmptyComponent = 1;
		$jStart = 1;
	}
	else
	{
		$numEmptyComponent = 0;
		$jStart = 1;
	}

	$numFileNameComponent = 1;

	$numIgnoredComponent = $numEmptyComponent + $numFileNameComponent;
	for ($j=$jStart; $j <= ($ctChunksPs - $numIgnoredComponent); $j++)
	{
		$relPath .= '../';
	}
	return $relPath;
}


/**
  * This function determines if the current directory ends in the
  * path piece given by $dir.
  *
  * True would be returned for the following.
  *		~/vendor/bin
  *		~/websites/goodsite_install/vendor/bin
  *		/vendor/bin
  */
static public function currentDirEndsWith($dir = 'vendor/bin')
{
	$retEndsWith = FALSE;
	// For each piece in $dir, make sure there
	// is that entry as we explore back.
	$chunksDir = explode('/', $dir);
	echo '...chunksDir='.var_export($chunksDir, TRUE)."\n";
	$ctChunksDir = count($chunksDir);
	echo '...ctChunksDir='.$ctChunksDir."\n";
	if ($ctChunksDir < 1)
	{
		// dir is specified as empty
		echo '...ctChunksDir<1'."\n";
		return FALSE; // @todo check this
	}
	$inspectPath = '../';
	$currentChunkFound = FALSE;
	for ($i = ($ctChunksDir-1); $i >= 0; $i--)
	{
		echo '...i='.$i."\n";
		$currentChunk = $chunksDir[$i];
		echo '...currentChunk='.$currentChunk."\n";
		$currentChunkFound = FALSE;
		if ($handle = opendir($inspectPath))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry == $currentChunk)
				{
					$currentChunkFound = TRUE;
					echo '...currentChunkFound...'.$entry."\n";
				}
			}
		}
		if (!$currentChunkFound)
		{
			echo '...NOT currentChunkFound'."\n";
			break;
		}
		$inspectPath .= $inspectPath;
	}
	if ($currentChunkFound)
	{
		$retEndsWith = TRUE;	
	}
	return $retEndsWith;
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
    	$ft = filetype($lg.'/'.$file);
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
  * Returns, to some extent, the contents of the located used for
  * generated files.  That location has subdirectories, and each
  * subdirectory reflects a session, and it contains a single
  * resource created (and to be downloaded) by the user
  * associated with that session.
  *
  * Basically the names of the directories in the generated
  * directory are returned via an array.
  */
static public function contentsGenerated()
{
	$sessionDirs = array();
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
		echo "\n".'... file='.$file."\n";
    	$ft = filetype($lg.'/'.$file);
    	if (  ($ft == 'dir') && (($file != '..')&&($file != '.'))  )
    	{
    		echo "\n... adding file=".$file."\n";
    		$sessionDirs[] = $file;
    	}
	}
	closedir($hd);
	return $sessionDirs;
}
//ENZ

/**
  * Removed the older files in the base location of generated files.
  *
  * The relative directory, as it is called, is an optional parameter.
  * If set to null, then $_SERVER['PHP_SELF'] of this file itself
  * will be used.  If not null, that value will be used.
  *
  */
static public function removeOlderFiles($relDir = NULL)
{
	// Get the current time.
	// Get contents of base location of generated file.
	// Find entries older than current time.
	// Delete those entries.

	$currentTime = time();
	$relDir = is_null($relDir)?self::getRelative($_SERVER['PHP_SELF']):$relDir;
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
  * Prunes the generation area of files, first based on exceeding
  * a certain max age that is allowed, and then based on the max
  * capacity of that generation area.
  *
  * Here is how its done.
  *
  * This method first identifies those files in the generated area
  * that are older than $max_age, and removes them.  Files should
  * only exist in that generation area for so long, and then they should
  * be removed.  This will help keep the generation area under a certain
  * size.
  *
  * Then, remaining files will be ordered by their age.  If that set
  * exceeds max_capacity, it will have to be reduced in size to max_capacity.
  * The files that are deleted will be the oldest.
  */
static public function pruneFiles($relDirPrune = NULL)
{
	$thoseNotOutOfDate = array();
	$currentTime = time();

	$relDir = '';
	$sapiType = php_sapi_name();
	if (substr($sapiType, 0,3) == 'cli')
	{
		if (is_null($relDirPrune))
		{
			$relDir = '';// @todo
		}
	}
	else
	{
		$relDir = is_null($relDirPrune)?self::getRelative($_SERVER['PHP_SELF']):$relDirPrune;
	}
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
	    	else
	    	{
	    		$thoseNotOutOfDate[$relDir.$lg.'/'.$entry] = $age;
	    	}
    	}
	}
	closedir($hd);

	asort($thoseNotOutOfDate);
	$ct = 0;
	foreach ($thoseNotOutOfDate as $keyEntry=>$valueAge)
	{
		$ct++;
		if ($ct > DirUtilities::$max_capacity)
		{
			unlink($keyEntry);
		}
	}
/**
	usort($thoseNotOutOfDate, function($a, $b)
		{
			if ($a == $b) {
        		return 0;
    		}
    		return ($a < $b) ? -1 : 1;
		});
**/
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