<?php

/**
 * Parser de links en una ayuda
 */
class parser_ayuda
{
	protected static $tags = array('wiki', 'api');
	
	function es_texto_plano($texto)
	{
		return true;		
	}
	
	static function parsear($texto)
	{
		$parseado = "";
		$tags = implode('|', self::$tags);
		$exp_reg = '/([^\[]*)\[('.$tags.'):([^\ ]+)[\ ]([^\[]+)\]([^\[]*)/';
		var_dump($exp_reg);
		$resultado = array();
		if (preg_match_all($exp_reg, $texto, $resultado)) {
			for ($i=0; $i< count($resultado[0]); $i++) {
				$tipo = $resultado[2][$i];
				$parseado .= $resultado[1][$i];
				$metodo = "parsear_".$tipo;
				$parseado .= self::$metodo($resultado[3][$i], $resultado[4][$i]);
				$parseado .= $resultado[5][$i];
			}
		}
		ei_arbol($resultado);
		echo $parseado;
		return $parseado;
	}
	
	function parsear_wiki($id, $nombre)
	{
		return "<a href='WIKI$id'>$nombre</a>";
	}
	
	function parsear_api($id, $nombre)
	{
		return "<a href='API$id'>$nombre</a>";		
	}
	
	function parsear_test($id, $nombre)
	{
		return "<test id='$id'>$nombre</test>";
	}
	
}


?>