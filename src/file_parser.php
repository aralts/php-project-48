<?php

function parseFile(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new Exception("Error: File '$filePath' does not exist.");
    }

    $fileContent = file_get_contents($filePath);
    $data = json_decode($fileContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error: Failed to parse JSON from '$filePath'");
    }

    return $data;
}