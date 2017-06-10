<?php

require_once __DIR__.'/../vendor/autoload.php';

function outputResult($result)
{
    foreach ($result as $row) {
        foreach ($row as $value) {
            echo $value.' ';
        }

        echo PHP_EOL;
    }
}

$maze = new \JanisGruzis\Maze();
$result = $maze->generate();
outputResult($result);

echo PHP_EOL;
$maze->setSeed(20);
$result = $maze->generate();
outputResult($result);


