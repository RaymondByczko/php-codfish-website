<?php
namespace RaymondByczko\PhpCodfishWebsite;

class DirUtilities
{

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
}
?>