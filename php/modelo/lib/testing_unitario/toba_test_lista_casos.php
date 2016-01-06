<?php

class toba_test_lista_casos
{
	static $proyecto;
	static $instancia;
	static $path_base = '/php/testing';
	static $tipo;
	
	static function set_tipo($tipo_test)
	{
		self::$tipo = $tipo_test;
	}
	
	static function get_path($ultimo_nivel='', $personalizacion = false)
	{
		if (isset(self::$proyecto) && isset(self::$instancia)) {
			$p = toba_modelo_catalogo::instanciacion()->get_proyecto(self::$instancia, self::$proyecto);
			$path = ($personalizacion === TRUE) ? $p->get_dir_pers() . self::$path_base :  $p->get_dir(). self::$path_base;
		} else {
			$proyecto = toba_contexto_info::get_proyecto();
			$path = ($personalizacion === TRUE) ? toba::instancia()->get_path_proyecto_pers($proyecto). self::$path_base :  toba::instancia()->get_path_proyecto($proyecto). self::$path_base;
		}
		
		if (trim($ultimo_nivel) != '') {
			$path .= '/'. $ultimo_nivel;
		}
		
		return $path;
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

	static function get_categorias($tipo=null)
	{
		$cat_test = $cat_sel = array();
		$categorias = array(array('id' => 'todas', 'nombre' => '-- Todas --'));
		
		//Busco las categorias de los 2 tipos de casos, si no se especifica ninguno particular se suman todos.
		if (!isset(self::$tipo) || $tipo == 'U') {
			$path = self::get_path();
			if( $handle = @opendir( $path ) ) {
				$cat_test = self::traer_todo($handle, $path);
				closedir($handle); 
			}
		}
		
		if (!isset(self::$tipo) || $tipo == 'S') {
			$path_sel= self::get_path('selenium');
			if ($handle = @opendir($path_sel)) {
				$cat_sel = self::traer_todo($handle, $path_sel);
				closedir($handle);
			}
		}
		
		if (! empty($cat_test) || ! empty($cat_sel)) {
			$categorias = array_merge($categorias, $cat_test, $cat_sel);
		}		
		usort($categorias, array("toba_test_lista_casos", "comparar"));
		//toba::logger()->var_dump($categorias);
		return $categorias;
	}
	
	static function traer_todo($handle, $path)
	{
		$categorias = array();
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

		$path_pers = toba::instancia()->get_path_proyecto_pers($proyecto)."/php";
		agregar_dir_include_path($path_pers);
		
		$casos = $casos_sel = $casos_pers = $casos_pers_sel = array();
		$path = self::get_path();
		if (file_exists($path.'/test_toba.php')) {
			require_once($path.'/test_toba.php');			
		}
		if( $handle = @opendir( $path ) ) {
			$casos = self::get_archivos($handle, $path);
			closedir($handle); 
		}
		
		$path_sel= self::get_path('selenium');
		if ($handle = @opendir($path_sel)) {
			$casos_sel = self::get_archivos($handle, $path_sel);
			closedir($handle); 			
		}
		
		$path_pers = self::get_path('', true);
		if( $handle = @opendir( $path_pers ) ) {
			$casos_pers = self::get_archivos($handle, $path_pers);
			closedir($handle); 
		}
		
		$path_pers_sel = self::get_path('selenium', true);
		if( $handle = @opendir( $path_pers_sel ) ) {
			$casos_pers_sel = self::get_archivos($handle, $path_pers_sel);
			closedir($handle); 
		}
		
		if (! empty($casos) || ! empty($casos_sel) || ! empty($casos_pers) || ! empty($casos_pers_sel)) {
			$casos = array_merge ($casos, $casos_sel, $casos_pers, $casos_pers_sel);
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
	
	static function get_archivos($handle, $path) 
	{
		$casos = array();
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
		//ei_arbol($casos, $path);
		return $casos;
	}
}


?>