<?php

/**
 * Parser de links tipo wiki en una ayuda o descripci�n
 * @package Varios
 */
class toba_parser_ayuda
{
	protected static $tags = array('wiki', 'api', 'link', 'url', 'test');
	
	/**
	 * Determina si una cadena es texto plano o contiene algun formato a parsear y convertir
	 * @todo Ver una forma de no usar exp.reg. para saber si parsear o no!
	 */
	static function es_texto_plano($texto)
	{
		return ! preg_match(self::exp_reg(), $texto);
	}
	
	/**
	 * Busca y reemplaza el formato wiki en un texto
	 */
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
	
	static function parsear_wiki($id, $nombre, $proyecto=null)
	{
		$anchor = '';
		if (strpos($id, '#') !== false) {
			$anchor = substr($id, strpos($id, '#')+1);			
			$id = substr($id, 0, strpos($id, '#'));
		}
		$url = toba_recurso::url_proyecto($proyecto)."/doc/wiki/trac/toba/wiki/$id.html#$anchor";
		$img = toba_recurso::imagen_toba("wiki.gif", true);
		$tag = "<a href=$url target=wiki>$nombre</a>$img";
		return str_replace("'", "\\'", $tag);
	}
	
	static function parsear_api($id, $nombre, $proyecto=null)
	{
		$anchor = '';
		if (strpos($id, '#') !== false) {
			$anchor = substr($id, strpos($id, '#')+1);			
			$id = substr($id, 0, strpos($id, '#'));
		}
		
		$url = toba_recurso::url_proyecto($proyecto)."/doc/api/$id.html#$anchor";
		$img = toba_recurso::imagen_toba("api.gif", true);
		$tag = "<a href=$url  target=api>$nombre</a>$img";
		return str_replace("'", "\\'", $tag);
	}
	
	protected static function parsear_link($id, $nombre)
	{
		$url = toba_recurso::url_proyecto()."/".$id;
		$tag = "<a href=$url  target=_blank>$nombre</a>";
		return str_replace("'", "\\'", $tag);		
	}
	
	protected static function parsear_url($id, $nombre)
	{
		$url = $id;
		$tag = "<a href=$url  target=_blank>$nombre</a>";
		return str_replace("'", "\\'", $tag);		
	}
	
	protected static function parsear_test($id, $nombre)
	{
		return "<test id='$id'>$nombre</test>";
	}
	
}


?>