<?php

use Acme\NewCommand;
use Symfony\Component\Console\Application;

require('vendor\autoload.php');

$app = new Application('Laracasts Demo version 1.0');

$app->add(new NewCommand(new GuzzleHttp\Client));

$app->run();