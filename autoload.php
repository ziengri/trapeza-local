<?php

spl_autoload_register(function($className){
    global $pathInc2;
    
	$className = str_replace('\\', '/', $className);
	$className = ltrim($className, '/');
    $className = preg_replace('/^App\//', 'bc/', $className);
    $className = preg_replace('/^Custom\//', ltrim($pathInc2, '/').'/', $className);
	$fileclass = __DIR__.'/'.$className.'.php';
    

    // $fileclass = str_replace("/" ,"\\",$fileclass);
    // var_dump($fileclass);

    if (file_exists($fileclass)) include $fileclass;
});
