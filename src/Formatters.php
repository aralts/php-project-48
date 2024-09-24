<?php

namespace Differ\Differ\Formatters;

use function Differ\Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Differ\Formatters\Plain\format as formatPlain;
use function Differ\Differ\Formatters\JSON\format as formatJSON;

function format(array $diff, string $formatName): string
{
    return match ($formatName) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        'json' => formatJSON($diff),
        default => throw new \Exception("Unknown format: {$formatName}")
    };
}
