<?php
http_response_code(500);
ob_start();

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
    $responseCode = http_response_code();
    $output = ob_get_contents(); 
    
    /* @var $error array */
    $error = error_get_last();
    
    if ($error != null || ($responseCode === 500 && empty($output))) { 
        $errorFile = 'Unknown file';
        $errorLine = 0;
        $errorMsg = 'Unknown error.';

        if ($error != null) {
            $errorFile = $error['file'];
            $errorLine = $error['line'];
            $errorMsg = $error['message'];      
        }

        $errorMessage = "Error@$errorFile($errorLine) - $errorMsg";
        Response::errorResponse(500, $errorMessage);        
     }    
}

