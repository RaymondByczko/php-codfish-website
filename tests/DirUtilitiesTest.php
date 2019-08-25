<?php
/**
  * @file DirUtilitiesTest.php
  * @location php-codfish-website/tests
  * @company self
  * @author Raymond Byczko
  * @start_date 2019-08-24
  * @history 2019-mm-dd; RByczko; did...
  */
?>

<?php
use PHPUnit\Framework\TestCase;
use RaymondByczko\PhpCodfishWebsite\DirUtilities;

class DirUtilitiesTest extends TestCase
{
    public function testGetRelativeExpectException()
    {
        // @note This works! You can expect more than one thing in writing your
        // test methods.
        $this->expectException('Exception');
        $this->expectExceptionMessage('expected empty string for first element');
        // Missing initial forward slash.
        $relSomeFile = DirUtilities::getRelative('vendor/bin/somefile.php');
    }

    public function testGetRelativeSameInputNoException()
    {
        // Missing initial forward slash.
        $relSomeFile = DirUtilities::getRelative('vendor/bin/somefile.php', FALSE);
        $this->assertEquals('../../', $relSomeFile);
    }

    public function testGetRelativeShouldWork()
    {

        $relSomeFile = DirUtilities::getRelative('/vendor/bin/somefile.php');

        $this->assertEquals('../../', $relSomeFile);
    }



    public function testGetRelativeVeryLongShouldWork()
    {
        $relSomeFile = DirUtilities::getRelative('/dir1/dir2/dir3/somefile.php');
        $this->assertEquals('../../../', $relSomeFile);

    }

}
?>