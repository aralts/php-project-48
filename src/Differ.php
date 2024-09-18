<?php

namespace Differ;

use function Differ\Parsers\parse;

function genDiff(string $file1, string $file2, string $format = 'stylish'): string
{
    try {
        $data1 = parse($file1);
        $data2 = parse($file2);
    } catch (\Exception $e) {
        return $e->getMessage();
    }

    switch ($format) {
        case 'stylish':
            return formatStylish($data1, $data2);
        default:
            throw new \Exception("Error: Unknown format '$format'. Supported formats: stylish.");
    }
}

function formatStylish(array $data1, array $data2, int $indentSize = 2, int $baseIndentLevel = 1): string
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($allKeys);

    $diff = array_map(function ($key) use ($data1, $data2, $indentSize, $baseIndentLevel) {
        $existsInFile1 = array_key_exists($key, $data1);
        $existsInFile2 = array_key_exists($key, $data2);

        if ($existsInFile1 && !$existsInFile2) {
            return formatLine($key, $data1[$key], '-', $baseIndentLevel, $indentSize);
        }

        if (!$existsInFile1 && $existsInFile2) {
            return formatLine($key, $data2[$key], '+', $baseIndentLevel, $indentSize);
        }

        if ($data1[$key] !== $data2[$key]) {
            return formatLine($key, $data1[$key], '-', $baseIndentLevel, $indentSize) . "\n" .
                   formatLine($key, $data2[$key], '+', $baseIndentLevel, $indentSize);
        }

        return formatLine($key, $data1[$key], ' ', $baseIndentLevel, $indentSize);
    }, $allKeys);

    return "{\n" . implode("\n", $diff) . "\n}";
}

function formatLine(string $key, $value, string $sign, int $indentLevel, int $indentSize): string
{
    $indent = addIndent($indentLevel, $indentSize);
    return "{$indent}{$sign} $key: " . var_export($value, true);
}

function addIndent(int $depth, int $indentSize = 2): string
{
    return str_repeat(' ', $depth * $indentSize);
}
