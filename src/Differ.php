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
    $sortedKeys = keysSort($allKeys);

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
    }, $sortedKeys);
}

function keysSort(array $array): array
{
    if (count($array) <= 1) {
        return $array;
    }

    $middle = intdiv(count($array), 2);
    $left = array_slice($array, 0, $middle);
    $right = array_slice($array, $middle);

    return sortMerge(keysSort($left), keysSort($right));
}

function sortMerge(array $left, array $right): array
{
    if ($left === []) {
        return $right;
    }

    if ($right === []) {
        return $left;
    }

    if ($left[0] <= $right[0]) {
        return array_merge([$left[0]], sortMerge(array_slice($left, 1), $right));
    }

    return array_merge([$right[0]], sortMerge($left, array_slice($right, 1)));
}
