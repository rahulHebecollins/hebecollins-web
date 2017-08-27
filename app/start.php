<?php

use Noodlehaus\Config;
use Slim\Slim;
use Slim\Views\TwigExtension;
use Toolbox\Helpers\Hash;
use Toolbox\User\User;
use Slim\Views\Twig;
use Toolbox\Validation\Validator;

session_cache_limiter(false);
session_start();

//display error on
ini_set('display_errors','On');

define('INC_ROOT',dirname(__DIR__));

require  INC_ROOT.'/vendor/autoload.php';

$app = new Slim([
    'mode'=>file_get_contents(INC_ROOT.'/mode.php'),
    'view'=>new Twig(),
    'templates.path'=> INC_ROOT.'/app/views'
]);

$app->configureMode($app->config('mode'), function () use($app){
    $app->config= Config::load(INC_ROOT."/src/config/{$app->mode}.php");
});

require 'database.php';
require 'routes.php';


$app->container->set('user',function(){
    return new User;
});

//Hash doesn't have to change throughout the application
$app->container->singleton('hash',function() use ($app){
    //passing configuration from development.php to Hash.php
    return new Hash($app->config);
});

//adding validator to the app container
$app->container->singleton('validation',function(){
   return new Validator;
});


//$app->get('/some',function() use($app){
//    $app->render('home.php');
//});

$view = $app->view();

$view -> parserOptions = [
    'debug'=> $app->config->get('twig.debug')
];

$view-> parserExtensions = [
    new TwigExtension
];

//echo $app->hash->password('iluvpussy');
//$pass = 'iluvpussy';
//$hash = '$2y$10$eQ23gDmgYNzbk1f89bvFJ.3/KK7MChLtsaA1yR9Oqd9SY91iZgxcq';
//
//var_dump($app->hash->passwordCheck($pass,$hash));