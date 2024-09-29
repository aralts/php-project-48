<?php

namespace Differ\Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $filePath): array
{
    $realPath = realpath($filePath);

    if ($realPath === false) {
        throw new \Exception("Error: File '$filePath' does not exist.");
    }

    $fileContent = file_get_contents($realPath);
    if ($fileContent === false) {
        throw new \Exception("Error: Could not read the file '$realPath'.");
    }

    $extension = pathinfo($realPath, PATHINFO_EXTENSION);

    return match ($extension) {
        'json' => parseJson($fileContent, $realPath),
        'yml', 'yaml' => objectToArray(Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP)),
        default => throw new \Exception("Error: Unsupported file format '$extension'.")
    };
}


function parseJson(string $content, string $path): array
{
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Error: Failed to parse JSON from '$path'.");
    }

    return $data;
}

function objectToArray(object $object): array
{
    $array = (array) $object;
    return transformArray($array);
}

function transformArray(array $array): array
{
    if (count($array) === 0) {
        return [];
    }

    $key = array_key_first($array);
    $value = $array[$key];
    $rest = array_slice($array, 1, null, true);

    $transformedValue = is_object($value) ? objectToArray($value) : $value;
    return [$key => $transformedValue] + transformArray($rest);
}
