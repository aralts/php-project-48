<?php

namespace Differ\Differ\Formatters\Stylish;

function format(array $diff, int $indentSize = 4, int $currentDepth = 1): string
{
    return "{\n" . formatItems($diff, $indentSize, $currentDepth) . "\n}";
}

function formatItems(array $diff, int $indentSize, int $currentDepth): string
{
    $indent = str_repeat(' ', $indentSize * $currentDepth - 2);

    $formatted = array_map(function (array $item) use ($indent, $currentDepth, $indentSize): string {
        return formatItem($item, $indent, $currentDepth, $indentSize);
    }, $diff);

    return implode("\n", $formatted);
}

function formatItem(array $item, string $indent, int $currentDepth, int $indentSize): string
{
    $key = $item['key'];
    $type = $item['type'];
    $value = '';

    switch ($type) {
        case 'added':
        case 'removed':
        case 'unchanged':
            $value = formatValue($item['value'], $indentSize, $currentDepth + 1);
            return formatString($indent, $key, $value, $type);
        case 'changed':
            $oldValue = formatValue($item['oldValue'], $indentSize, $currentDepth + 1);
            $newValue = formatValue($item['newValue'], $indentSize, $currentDepth + 1);
            return formatString($indent, $key, $oldValue, 'removed') . "\n"
                . formatString($indent, $key, $newValue, 'added');
        case 'nested':
            $children = formatItems($item['children'], $indentSize, $currentDepth + 1);
            return formatNestedString($indent, $key, $children);
        default:
            throw new \Exception("Unknown type: {$type}");
    }
}

function formatString(string $indent, string $key, string $value, string $type): string
{
    $prefix = match ($type) {
        'added' => '+ ',
        'removed' => '- ',
        'unchanged' => '  ',
        default => '',
    };

    return "{$indent}{$prefix}{$key}: {$value}";
}

function formatNestedString(string $indent, string $key, string $children): string
{
    return "{$indent}  {$key}: {\n{$children}\n{$indent}  }";
}

function formatValue(mixed $value, int $indentSize, int $currentDepth): string
{
    if (is_array($value)) {
        $keys = array_keys($value);
        $formatted = formatArray($value, $keys, $indentSize, $currentDepth);
        return "{\n" . implode("\n", $formatted) . "\n" . str_repeat(' ', $indentSize * ($currentDepth - 1)) . "}";
    }

    return is_string($value) ? "$value" : strtolower(var_export($value, true));
}

function formatArray(array $array, array $keys, int $indentSize, int $currentDepth): array
{
    if (count($keys) === 0) {
        return [];
    }

    $key = $keys[0];
    $formattedLine = formatLine($key, $array[$key], $indentSize, $currentDepth);
    return array_merge([$formattedLine], formatArray($array, array_slice($keys, 1), $indentSize, $currentDepth));
}

function formatLine(string|int $key, mixed $val, int $indentSize, int $currentDepth): string
{
    $innerIndent = str_repeat(' ', $indentSize * $currentDepth);
    return "{$innerIndent}{$key}: " . formatValue($val, $indentSize, $currentDepth + 1);
}
