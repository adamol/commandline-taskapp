<?php

use Symfony\Component\Console\Application;

require 'vendor\autoload.php';

$app = new Application('Laracasts Demo', '1.0');

$app->add(new Acme\RenderCommand);

$app->run();