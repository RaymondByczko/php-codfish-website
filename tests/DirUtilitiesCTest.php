<?php
/**
  * @file DirUtilitiesCTest
  * @location test
  * @company self
  * @author Raymond Byczko
  * @start_date 2019-08-25
  * @history 2019-08-25; RByczko; This is another addition to the
  * set of files testing DirUtilities.  Since it needs a certain
  * directory structure, which will be take care of by setUp/tearDown,
  * it is relegated to its own test class.
  */
?>
<?php
use PHPUnit\Framework\TestCase;
use RaymondByczko\PhpCodfishWebsite\DirUtilities;

class DirUtilitiesCTest extends TestCase
{
	private $genDir;
	private $createdGenDir = FALSE;
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
		$extraFiles = 3;
		for ($i = 0; $i < DirUtilities::$max_capacity + $extraFiles; $i++)
		{
			mkdir('Session'.$i);
			chdir('Session'.$i);
			touch('shortlivedresource.txt');
			chdir('..');
			sleep(1);
		}
		chdir('..');
		sleep(10);
		sleep(DirUtilities::$max_age - DirUtilities::$max_capacity);

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
		
		$extraFiles = 3;
		for ($i = 0; $i < DirUtilities::$max_capacity + $extraFiles; $i++)
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

	public function testIsLocationGeneratedFull()
    {
        $isFull = DirUtilities::isLocationGeneratedFull();
        $this->assertEquals(TRUE, $isFull);
    }
}
?>