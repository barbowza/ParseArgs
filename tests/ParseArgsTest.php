<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Barbowza\ParseArgs;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @author Jan Břečka
 */
class ParseArgsTest extends TestCase
{

    const MOCK_SCRIPT_NAME = 'test.php';

    /** @test */
    public function parseEmptyArray()
    {
        $result = ParseArgs::parseArgs(array());
        $this->assertEquals(0, count($result));
    }

    /** @test */
    public function noArgument()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME));
        $this->assertEquals(0, count($result));
    }

    /** @test */
    public function singleArgument()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, 'a'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('a', $result[0]);
    }

    /** @test */
    public function multiArguments()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, 'a', 'b'));
        $this->assertEquals(2, count($result));
        $this->assertEquals('a', $result[0]);
        $this->assertEquals('b', $result[1]);
    }

    /** @test */
    public function singleSwitch()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a'));
        $this->assertEquals(1, count($result));
        $this->assertTrue($result['a']);
    }

    /** @test */
    public function singleSwitchWithValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a=b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function multiSwitch()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a', '-b'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertTrue($result['b']);
    }

    /** @test */
    public function multiSwitchAsOne()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertTrue($result['b']);
    }

    /** @test */
    public function singleFlagWithoutValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a'));
        $this->assertEquals(1, count($result));
        $this->assertTrue($result['a']);
    }

    /** @test */
    public function singleFlagWithValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function singleFlagOverwriteValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=original', '--a=overwrite'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('overwrite', $result['a']);
    }

    /** @test */
    public function singleFlagOverwriteWithoutValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=original', '--a'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('original', $result['a']);
    }

    /** @test */
    public function singleFlagWithDashInName()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--include-path=value'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('value', $result['include-path']);
    }

    /** @test */
    public function singleFlagWithDashInNameAndInValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--include-path=my-value'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('my-value', $result['include-path']);
    }

    /** @test */
    public function singleFlagWithEqualsSignInValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--funny=spam=eggs'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('spam=eggs', $result['funny']);
    }

    /** @test */
    public function singleFlagWithDashInNameAndEqualsSignInValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--also-funny=spam=eggs'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('spam=eggs', $result['also-funny']);
    }

    /** @test */
    public function singleFlagWithValueWithoutEquation ()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a', 'b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function multiSwitchAsOneWithValue()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab', 'value'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertEquals('value', $result['b']);
    }

    /** @test */
    public function combination()
    {
        $result = ParseArgs::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab', 'value', 'argument', '-c', '--s=r', '--x'));
        $this->assertEquals(6, count($result));
        $this->assertTrue($result['a']);
        $this->assertEquals('value', $result['b']);
        $this->assertEquals('argument', $result[0]);
        $this->assertTrue($result['c']);
        $this->assertEquals('r', $result['s']);
        $this->assertTrue($result['x']);
    }

    /** @test */
    public function parseGlobalServerVariable()
    {
        $_SERVER['argv'] = array(self::MOCK_SCRIPT_NAME, 'a');
        $result = ParseArgs::parseArgs();
        $this->assertEquals(1, count($result));
    }
}
