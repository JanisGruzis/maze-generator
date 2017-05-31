<?php

require_once __DIR__.'/../vendor/autoload.php';

$maze = new \JanisGruzis\Maze();
var_dump($maze->generate());
