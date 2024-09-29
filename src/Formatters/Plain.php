<?php

namespace Differ\Differ\Formatters\Plain;

function format(array $diff): string
{
    $lines = array_map(fn($item) => formatItem($item, ''), $diff);
    return implode("\n", array_filter($lines));
}

function formatItem(array $item, string $path): string
{
    $key = $item['key'];
    $propertyPath = $path === '' ? $key : "{$path}.{$key}";
    $type = $item['type'];

    switch ($type) {
        case 'added':
            $value = formatValue($item['value']);
            return "Property '{$propertyPath}' was added with value: {$value}";
        case 'removed':
            return "Property '{$propertyPath}' was removed";
        case 'unchanged':
            return '';
        case 'changed':
            $oldValue = formatValue($item['oldValue']);
            $newValue = formatValue($item['newValue']);
            return "Property '{$propertyPath}' was updated. From {$oldValue} to {$newValue}";
        case 'nested':
            $children = array_map(fn($child) => formatItem($child, $propertyPath), $item['children']);
            return implode("\n", array_filter($children));
        default:
            throw new \Exception("Unknown type: {$type}");
    }
}

function formatValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    return is_string($value) ? "'{$value}'" : strtolower(var_export($value, true));
}
