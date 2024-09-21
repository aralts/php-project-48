<?php

namespace Differ\Formatters\Stylish;

function format(array $diff, int $indentSize = 4, int $currentDepth = 1): string
{
    return "{\n" . formatItems($diff, $indentSize, $currentDepth) . "\n}";
}

function formatItems(array $diff, int $indentSize, int $currentDepth): string
{
    $indent = str_repeat(' ', $indentSize * $currentDepth - 2);

    $formatted = array_map(function ($item) use ($indent, $currentDepth, $indentSize) {
        return formatItem($item, $indent, $currentDepth, $indentSize);
    }, $diff);

    return implode("\n", $formatted);
}

function formatItem($item, $indent, $currentDepth, $indentSize)
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

function formatString($indent, $key, $value, $type)
{
    $prefix = match ($type) {
        'added' => '+ ',
        'removed' => '- ',
        'unchanged' => '  ',
        default => '',
    };

    return "{$indent}{$prefix}{$key}: {$value}";
}

function formatNestedString($indent, $key, $children)
{
    return "{$indent}  {$key}: {\n{$children}\n{$indent}  }";
}

function formatValue($value, int $indentSize, int $currentDepth): string
{
    if (is_array($value)) {
        $formatted = array_map(function ($key, $val) use ($indentSize, $currentDepth) {
            return formatLine($key, $val, $indentSize, $currentDepth);
        }, array_keys($value), $value);

        return "{\n" . implode("\n", $formatted) . "\n" . str_repeat(' ', $indentSize * ($currentDepth - 1)) . "}";
    }

    return is_string($value) ? "$value" : strtolower(var_export($value, true));
}

function formatLine($key, $val, $indentSize, $currentDepth)
{
    $innerIndent = str_repeat(' ', $indentSize * $currentDepth);
    return "{$innerIndent}{$key}: " . formatValue($val, $indentSize, $currentDepth + 1);
}
