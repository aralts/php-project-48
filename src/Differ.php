<?php

namespace Differ\Differ;

use function Differ\Differ\Parsers\parse;
use function Differ\Differ\Formatters\format;

function genDiff(string $file1, string $file2, string $format = 'stylish'): string
{
    try {
        $data1 = parse($file1);
        $data2 = parse($file2);
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    $diff = buildDiff($data1, $data2);

    return format($diff, $format);
}

function buildDiff(array $data1, array $data2): array
{
    $allKeys = array_values(array_unique(array_merge(array_keys($data1), array_keys($data2))));
    $sortedKeys = array_merge([], $allKeys);
    usort($sortedKeys, fn($a, $b) => $a <=> $b);

    return array_map(function ($key) use ($data1, $data2) {
        $existsInFile1 = array_key_exists($key, $data1);
        $existsInFile2 = array_key_exists($key, $data2);

        if ($existsInFile1 && !$existsInFile2) {
            return ['key' => $key, 'type' => 'removed', 'value' => $data1[$key]];
        }

        if (!$existsInFile1 && $existsInFile2) {
            return ['key' => $key, 'type' => 'added', 'value' => $data2[$key]];
        }

        if (is_array($data1[$key]) && is_array($data2[$key])) {
            return ['key' => $key, 'type' => 'nested', 'children' => buildDiff($data1[$key], $data2[$key])];
        }

        if ($data1[$key] !== $data2[$key]) {
            return ['key' => $key, 'type' => 'changed', 'oldValue' => $data1[$key], 'newValue' => $data2[$key]];
        }

        return ['key' => $key, 'type' => 'unchanged', 'value' => $data1[$key]];
    }, $allKeys);
}
