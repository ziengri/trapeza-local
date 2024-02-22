<?php

function telegrammBotsAutoload($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = strtolower(substr($className, $lastNsPos + 1));
        $fileName  = str_replace('\\', '/', $namespace) . '/';
    }
    $fileName .= str_replace('_', '/', $className) . '.php';
	$fileName = __DIR__."/".$fileName;
	
    if (file_exists($fileName)) include $fileName;
}

spl_autoload_register('telegrammBotsAutoload');
