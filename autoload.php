<?php
spl_autoload_register('Autoloader::autoload');

class Autoloader {
    static $dirs = [
        '',
        'database/',
        'response/',
        'secure/'
        ];
    
    static function autoload($class) {
        foreach (self::$dirs as $dir) {
            $classFile = ___DIR___ . '/' . $dir . $class . '.php';
            self::loadFileIfExists($classFile);
        }
    }
    
    static function loadFileIfExists($classFile) {
        if (file_exists($classFile)) {
            require_once $classFile;
        }
    }
}

