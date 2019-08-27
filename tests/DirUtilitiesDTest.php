<?php
/**
  * @file DirUtilitiesDTest.php
  * @location tests/
  * @company self
  * @author Raymond Byczko
  * @start_date 2019-08-26
  * @history 2019-08-26; RByczko; Wrote phpunit test code for the static
  * method pruneFiles.
  */
?>
<?php
use PHPUnit\Framework\TestCase;
use RaymondByczko\PhpCodfishWebsite\DirUtilities;

class DirUtilitiesDTest extends TestCase
{
	private $genDir;
	private $createdGenDir = FALSE;
	private $extraFiles = 7;
	/**
	  * Set up a max capacity plus three files in generated
	  * directory.
	  */
	public function setUp(): void
	{
		$this->createdGenDir = FALSE;
		$this->genDir = DirUtilities::baseLocationGenerated();
		if (!is_dir($this->genDir))
		{
			$this->createdGenDir = TRUE;
			mkdir($this->genDir);
		}
		chdir($this->genDir);
		DirUtilities::$max_capacity = 5;

		DirUtilities::$max_age = 60; // seconds
		for ($i = 0; $i < (DirUtilities::$max_capacity + $this->extraFiles); $i++)
		{
			mkdir('Session'.$i);
			chdir('Session'.$i);
			touch('shortlivedresource.txt');
			chdir('..');
			sleep(1);
		}
		chdir('..');
		// sleep(10);
	}

	public function tearDown(): void
	{
		if (!is_dir($this->genDir))
		{
			throw new Exception('genertion directory should exist but does not');
		}
		chdir($this->genDir);
		DirUtilities::$max_capacity = 5;
		DirUtilities::$max_age = 60; // seconds
		
		for ($i = 0; $i < DirUtilities::$max_capacity + $this->extraFiles; $i++)
		{
			chdir('Session'.$i);
			unlink('shortlivedresource.txt');
			chdir('..');
			rmdir('Session'.$i);
		}
		chdir('..');
		if ($this->createdGenDir)
		{
			rmdir($this->genDir);
		}
	}

    public function testPruneFiles()
    {
    	$wd = getcwd();
    	$contentsBefore = DirUtilities::contentsGenerated();
    	$sizeContentsBefore = count($contentsBefore);
    	$this->assertEquals(12, $sizeContentsBefore);
    	$retPrune = DirUtilities::pruneFiles();
    	// $this->assertEquals(1, $remOlder);
    	$contentsAfter = DirUtilities::contentsGenerated();
    	$sizeContentsAfter = count($contentsBefore);
    	$this->assertEquals(11, $sizeContentsAfter);
    }
}
?>