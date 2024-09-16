<?php
namespace Differ;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $expected = "{\n    - follow: false\n    host: 'hexlet.io'\n    - proxy: '123.234.53.22'\n    - timeout: 50\n    + timeout: 20\n    + verbose: true\n}";
        $actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $this->assertEquals($expected, $actual);
    }
}
