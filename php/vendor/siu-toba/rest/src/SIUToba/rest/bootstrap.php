<?php

namespace SIUToba\rest;

class bootstrap
{
    /**
     * PSR-0 autoloader.
     */
    public static function autoload($className)
    {
        $baseDir = __DIR__;
        $baseDir = substr($baseDir, 0, -strlen(__NAMESPACE__)); //le saco el /rest
        $className = ltrim($className, '\\');
        $fileName = $baseDir;
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
        }
        $fileName .= $className.'.php';
        //$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($fileName)) {
            require $fileName;
        }
    }

    /**
     * Register PSR-0 autoloader.
     */
    public static function registerAutoloader()
    {
        spl_autoload_register(__NAMESPACE__."\\bootstrap::autoload");
    }
}
