<?php
spl_autoload_register('Autoloader::autoload');
register_shutdown_function('handleFatalError');

class Autoloader {
    static $dirs = [
        '',
        'database/',
        'response/',
        'secure/'
        ];
    
    static function autoload($class) {
        foreach (self::$dirs as $dir) {
            $classFile = __DIR__ . "/$dir" . "$class.php";
            self::loadFileIfExists($classFile);
        }
    }
    
    static function loadFileIfExists($classFile) {
        if (file_exists($classFile)) {
            require_once $classFile;
        }
    }
}

function handleFatalError() {
    $errorFile = 'Unknown file';
    $errorLine = 0;
    $errorMsg = 'Unknown error.';
    
    /* @var $error array */
    $error = error_get_last();
    if ($error != null) {
        $errorFile = $error['file'];
        $errorLine = $error['line'];
        $errorMsg = $error['message'];
        
        $errorMessage = "FatalError@$errorFile $errorLine - $errorMsg";
        
        Response::errorResponse(500, $errorMessage);
    }
}

