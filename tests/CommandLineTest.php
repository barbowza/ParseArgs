<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @author Jan Břečka
 */
class CommandLineTest extends TestCase
{

    const MOCK_SCRIPT_NAME = 'test.php';

    /** @test */
    public function parseEmptyArray()
    {
        $result = CommandLine::parseArgs(array());
        $this->assertEquals(0, count($result));
    }

    /** @test */
    public function noArgument()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME));
        $this->assertEquals(0, count($result));
    }

    /** @test */
    public function singleArgument()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, 'a'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('a', $result[0]);
    }

    /** @test */
    public function multiArguments()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, 'a', 'b'));
        $this->assertEquals(2, count($result));
        $this->assertEquals('a', $result[0]);
        $this->assertEquals('b', $result[1]);
    }

    /** @test */
    public function singleSwitch()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a'));
        $this->assertEquals(1, count($result));
        $this->assertTrue($result['a']);
    }

    /** @test */
    public function singleSwitchWithValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a=b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function multiSwitch()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-a', '-b'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertTrue($result['b']);
    }

    /** @test */
    public function multiSwitchAsOne()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertTrue($result['b']);
    }

    /** @test */
    public function singleFlagWithoutValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a'));
        $this->assertEquals(1, count($result));
        $this->assertTrue($result['a']);
    }

    /** @test */
    public function singleFlagWithValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function singleFlagOverwriteValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=original', '--a=overwrite'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('overwrite', $result['a']);
    }

    /** @test */
    public function singleFlagOverwriteWithoutValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a=original', '--a'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('original', $result['a']);
    }

    /** @test */
    public function singleFlagWithDashInName()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--include-path=value'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('value', $result['include-path']);
    }

    /** @test */
    public function singleFlagWithDashInNameAndInValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--include-path=my-value'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('my-value', $result['include-path']);
    }

    /** @test */
    public function singleFlagWithEqualsSignInValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--funny=spam=eggs'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('spam=eggs', $result['funny']);
    }

    /** @test */
    public function singleFlagWithDashInNameAndEqualsSignInValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--also-funny=spam=eggs'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('spam=eggs', $result['also-funny']);
    }

    /** @test */
    public function singleFlagWithValueWithoutEquation ()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '--a', 'b'));
        $this->assertEquals(1, count($result));
        $this->assertEquals('b', $result['a']);
    }

    /** @test */
    public function multiSwitchAsOneWithValue()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab', 'value'));
        $this->assertEquals(2, count($result));
        $this->assertTrue($result['a']);
        $this->assertEquals('value', $result['b']);
    }

    /** @test */
    public function combination()
    {
        $result = CommandLine::parseArgs(array(self::MOCK_SCRIPT_NAME, '-ab', 'value', 'argument', '-c', '--s=r', '--x'));
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
        $result = CommandLine::parseArgs();
        $this->assertEquals(1, count($result));
    }
}
