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

    switch ($extension) {
        case 'json':
            $data = json_decode($fileContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error: Failed to parse JSON from '$realPath'.");
            }
            break;
        case 'yml':
        case 'yaml':
            $data = Yaml::parse($fileContent, Yaml::PARSE_OBJECT_FOR_MAP);
            $data = objectToArray($data);
            break;
        default:
            throw new \Exception("Error: Unsupported file format '$extension'.");
    }

    return $data;
}


function objectToArray(object $object): array
{
    $array = (array) $object;
    $result = [];

    foreach ($array as $key => $value) {
        $result[$key] = is_object($value) ? objectToArray($value) : $value;
    }

    return $result;
}
