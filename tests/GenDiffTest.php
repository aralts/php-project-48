<?php

namespace Differ;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class GenDiffTest extends TestCase
{
	/**
	 * @covers \Differ\genDiff
	 * @covers \Differ\Parsers\parse
	 * @covers \Differ\formatStylish
	 * @covers \Differ\addIndent
	 * @covers \Differ\formatLine
	 */
	public function testJSONDiff(): void
	{
		$expected = <<<EOL
        {
          - follow: false
            host: 'hexlet.io'
          - proxy: '123.234.53.22'
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        EOL;

		$actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
		$this->assertEquals($expected, $actual);
	}

  /**
	 * @covers \Differ\genDiff
	 * @covers \Differ\Parsers\parse
	 * @covers \Differ\Parsers\objectToArray
	 * @covers \Differ\formatStylish
	 * @covers \Differ\addIndent
	 * @covers \Differ\formatLine
	 */
	public function testYamlDiff()
	{
		$expected = <<<EOL
        {
          - follow: false
            host: 'hexlet.io'
          - proxy: '123.234.53.22'
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        EOL;

		$this->assertEquals($expected, genDiff('tests/fixtures/file1.yaml', 'tests/fixtures/file2.yaml'));
	}
}
