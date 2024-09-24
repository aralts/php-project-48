<?php

namespace Differ\Differ\Formatters\JSON;

function format(array $diff): string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
