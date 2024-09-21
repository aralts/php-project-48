<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class GenDiffTest extends TestCase
{
    private string $expectedOutput;

    protected function setUp(): void
    {
        $this->expectedOutput = '{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}';
    }

	/**
	 * @coversNothing
	 */
    public function testNestedJsonFiles(): void
    {
        $file1 = 'tests/fixtures/nestedFile1.json';
        $file2 = 'tests/fixtures/nestedFile2.json';

        $this->assertSame($this->expectedOutput, genDiff($file1, $file2));
    }

	/**
	 * @coversNothing
	 */
    public function testNestedYamlFiles(): void
    {
        $file1 = 'tests/fixtures/nestedFile1.yaml';
        $file2 = 'tests/fixtures/nestedFile2.yaml';

        $this->assertSame($this->expectedOutput, genDiff($file1, $file2));
    }
}
