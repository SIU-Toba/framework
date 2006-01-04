<?php

/**
 * Parser de links en una ayuda
 */
class parser_ayuda
{
	protected static $tags = array('wiki', 'api', 'test');
	
	static function es_texto_plano($texto)
	{
		return ! preg_match(self::exp_reg(), $texto);
	}
	
	static function parsear($texto)
	{
		$parseado = "";
		$resultado = array();
		if (preg_match_all(self::exp_reg(), $texto, $resultado)) {
			for ($i=0; $i< count($resultado[0]); $i++) {
				$tipo = $resultado[2][$i];
				$parseado .= $resultado[1][$i];
				$metodo = "parsear_".$tipo;
				$parseado .= self::$metodo($resultado[3][$i], $resultado[4][$i]);
				$parseado .= $resultado[5][$i];
			}
		} else {
			$parseado = $texto;	
		}
		return $parseado;
	}
	
	protected static function exp_reg()
	{
		$tags = implode('|', self::$tags);
		return  '/([^\[]*)\[('.$tags.'):([^\ ]+)[\ ]([^\[]+)\]([^\[]*)/';
	}
	
	protected static function parsear_wiki($id, $nombre)
	{
		$url = recurso::path_apl()."/doc/wiki/trac/toba/wiki/$id.html";
		$img = recurso::imagen_apl("wiki.gif", true);
		return "<span style='white-space: nowrap'><a href='$url'  target='blank'>$nombre</a>$img</span>";
	}
	
	protected static function parsear_api($id, $nombre)
	{
		$url = recurso::path_apl()."/doc/api/$id.html";
		$img = recurso::imagen_apl("api.gif", true);
		return "<span style='white-space: nowrap'><a href='$url'  target='blank'>$nombre</a>$img</span>";		
	}
	
	protected static function parsear_test($id, $nombre)
	{
		return "<test id='$id'>$nombre</test>";
	}
	
}


?>