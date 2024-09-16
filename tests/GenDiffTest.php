<?php
namespace Differ;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @covers \Differ\genDiff
     * @covers \Differ\parseFile
     * @covers \Differ\formatStylish
     * @covers \Differ\addIndent
     * @covers \Differ\formatLine
     */
    public function testGenDiff(): void
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
}
