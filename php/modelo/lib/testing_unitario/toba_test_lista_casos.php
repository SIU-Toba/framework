<?php

class toba_test_lista_casos
{
	static $proyecto;
	static $instancia;
	
	static function get_path()
	{
		if (isset(self::$proyecto) && isset(self::$instancia)) {
			$p = toba_modelo_catalogo::instanciacion()->get_proyecto(self::$instancia, self::$proyecto);
			return $p->get_dir()."/php/testing";

		} else {
			$proyecto = toba_contexto_info::get_proyecto();
			return toba::instancia()->get_path_proyecto($proyecto)."/php/testing";
		}
	}
	
	static function comparar($x, $y)
	{
		if ( $x["nombre"] == $y["nombre"] )
			return 0;
		elseif ( $x["nombre"] < $y["nombre"] )
			return -1;
		else
			return 1;
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
		usort($categorias, array("toba_test_lista_casos", "comparar"));			
		return $categorias;
	}
	
	static function get_casos($categoria = 'nopar')
	{
		//Agrega el proyecto al include path
		if (isset(self::$proyecto)) {
			$proyecto = self::$proyecto;
		} else {
			$proyecto = toba_contexto_info::get_proyecto();
		}
		$path = toba::instancia()->get_path_proyecto($proyecto)."/php";
		agregar_dir_include_path($path);		
		
		$casos = array();
		$path = self::get_path();
		if (file_exists($path.'/test_toba.php')) {
			require_once($path.'/test_toba.php');			
		}
		if( $handle = @opendir( $path ) ) {
			while (false !== ($file = readdir($handle))) { 
				$path_completo = $path . "/" . $file;
				if( is_dir( $path_completo ) && substr($file, 0, 5) == "test_" ) {
					if ( $handle_interno = opendir( $path_completo ) ) {
						while (false !== ($file_interno = readdir($handle_interno))) { 
							if (substr($file_interno, 0, 5) == "test_" ) {
								$pos_punto = strripos($file_interno, "."); 
								$nombre_clase = substr($file_interno, 0, $pos_punto);
								require_once("$path_completo/$file_interno");
								$nombre = call_user_func(array($nombre_clase, "get_descripcion"));
								if ($nombre == '') {
									$nombre = $nombre_clase;
								}
								$id_categoria = substr($file, 5);
								$casos[] = array('id' => $nombre_clase, 'nombre' => $nombre, 'categoria' => $id_categoria, 'archivo' => "$path_completo/$file_interno");
							}
						}
						closedir($handle_interno);  
					}
				}
			}
			closedir($handle); 
		}
		usort($casos, array("toba_test_lista_casos", "comparar"));			

		if ($categoria == 'todas' || $categoria == 'nopar')
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