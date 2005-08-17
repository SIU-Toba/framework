<?php
include_once('nucleo/browser/interface/ef.php');
require_once("test_toba.php");

class lista_casos
{
	static function get_path()
	{
		$proyecto = toba::get_hilo()->obtener_proyecto();
		if($proyecto == "toba")
			$path = $_SESSION["path_php"] . "/acciones/pruebas/testing_automatico";
		else
			$path = $_SESSION["path"] . "/proyectos/$proyecto/php/testing_automatico";
		return $path;
	}
	
	static function get_categorias()
	{
		$categorias = array();
		$path = self::get_path();
		if( $handle = @opendir( $path ) ) {
			$categorias[] = array('id' => 'todas', 'nombre' => '-- Todas --');
			while (false !== ($file = readdir($handle))) { 
				$path_completo = $path . "/" . $file;
				if( is_dir( $path_completo ) && substr($file, 0, 5) == "test_" ) {
					$nombre = ucfirst(substr($file, 5));
					$archivo = $path_completo . "/" . "info.txt";
					if ( file_exists($archivo) ) 
						$nombre = file_get_contents($archivo);
					$id = substr($file, 5);
					$categorias[] = array('id' => $id, 'nombre' => $nombre);
				}
			}
			closedir($handle); 
		}
			
		return $categorias;
	}
	
	static function get_casos($categoria = apex_ef_no_seteado)
	{
		$casos = array();
		$path = self::get_path();
		if( $handle = @opendir( $path ) ) {
			while (false !== ($file = readdir($handle))) { 
				$path_completo = $path . "/" . $file;
				if( is_dir( $path_completo ) && substr($file, 0, 5) == "test_" )
					if ( $handle_interno = opendir( $path_completo ) ) {
						while (false !== ($file_interno = readdir($handle_interno))) { 
							if (substr($file_interno, 0, 5) == "test_" ) {
								$pos_punto = strripos($file_interno, "."); 
								$nombre_clase = substr($file_interno, 0, $pos_punto);
								require_once("$path_completo/$file_interno");
								$clase = new $nombre_clase;
								
								$nombre = ($clase->get_descripcion() == "")? $nombre_clase : $clase->get_descripcion();
								$id_categoria = substr($file, 5);
								
								$casos[] = array('id' => $nombre_clase, 'nombre' => $nombre, 'categoria' => $id_categoria, 'archivo' => "$path_completo/$file_interno");
						    }
						}
						closedir($handle_interno);  
					}
			}
			closedir($handle); 
		}
		
//		ei_arbol($casos);
		
		if ($categoria == 'todas' || $categoria == apex_ef_no_seteado)
			return $casos;
		else {
			$casos_selecc = array();
			foreach ($casos as $caso) {
				if ($caso['categoria'] == $categoria) {
					$casos_selecc[] = $caso;
				}
			}
			return $casos_selecc;
		}
	}
}


?>