<?php
/**
  * @file DirUtilitiesBTest.php
  * @location php-codfish-website/tests
  * @company self
  * @author Raymond Byczko
  * @start_date 2019-08-25
  * @history 2019-08-25; RByczko; Started this file.  This is the second
  * for DirUtilities, because of the need for uniquie setUp and tearDown.
  */
?>
<?php
use PHPUnit\Framework\TestCase;
use RaymondByczko\PhpCodfishWebsite\DirUtilities;

class DirUtilitiesBTest extends TestCase
{
    protected $wd;

    /**
      * Set up a directory structure from the current one,
      * that ends in vendor/bin.
      */
    public function setUp(): void
    {
    	echo "\nDirUtilitiesATest2::SetUp:start\n";
        $this->wd = getcwd();
        echo "\n";
        echo 'wd='.$this->wd."\n";  
        mkdir('tmp1');
        mkdir('tmp1/tmp2');
        mkdir('tmp1/tmp2/vendor');
        mkdir('tmp1/tmp2/vendor/bin');
        chdir('tmp1');
        chdir('tmp2');
        chdir('vendor');
        chdir('bin');
	
    }

    /**
      * Remove the directory structure that was created
      * in setUp.
      */
    public function tearDown(): void
    {
        echo "\nDirUtilitiesBTest::tearDown:start\n";
        $wd = getcwd();
        echo "\n";
        echo 'wd='.$wd."\n";
        chdir('..');
        rmdir('bin');
        chdir('..');
        rmdir('vendor');
        chdir('..');
        rmdir('tmp2');
        chdir('..');
        rmdir('tmp1');
       
    }

    public function testEndsWith()
    {
    	echo "\n".'... testEndsWith'."\n";
        $wd = getcwd();
        // throw new Exception('testEndsWith: does tearDown occur after?');
        $retEndsWith = DirUtilities::currentDirEndsWith();
        $this->assertEquals(TRUE, $retEndsWith);
    }
}
?>