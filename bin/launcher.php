<?php
if (! isset($_SERVER['TOBA_DIR'])) {
    $toba_dir = realpath(__DIR__.'/../');
    $path_autoload = '/vendor/autoload.php';
    
    if (! file_exists($toba_dir.$path_autoload)) {
        //Estoy dentro del vendor/bin de composer
        $path_autoload = '/../../..'. $path_autoload;
        
        if (! file_exists($toba_dir.$path_autoload)) {
            die("No se encuentra la carpeta de Toba, ni la variable de entorno TOBA_DIR");
        }
    }
    $_SERVER['TOBA_DIR'] = $toba_dir;
    
    //Hago todas las rutas relativas a TOBA_DIR que es lo unico constante
    include(realpath($toba_dir . $path_autoload));                      //Necesito cargar el autoload de composer antes, sino no funca nada
    $path_env =  (stripos($toba_dir,  DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false)  ?  realpath($toba_dir . '/../../../')  : $toba_dir;      
    if (file_exists($path_env. '/entorno_toba.env')) {  
        $dotenv = new Dotenv\Dotenv($path_env, 'entorno_toba.env');         
        $dotenv->load();
        //Chequeo que existan las variables correspondientes... deberia pero bue nunca esta de mas.
        $dotenv->required('TOBA_INSTANCIA');
        $dotenv->required('TOBA_INSTALACION_DIR');
    }       
}

require_once($_SERVER['TOBA_DIR'].'/php/consola/run.php');
