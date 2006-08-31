<?php

/**
 * Parser de links en una ayuda
 */
class toba_parser_ayuda
{
	protected static $tags = array('wiki', 'api', 'link', 'url', 'test');
	
	/**
	 * @todo Ver una forma de no usar exp.reg. para saber si parsear o no!
	 */
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
		$url = toba_recurso::path_pro()."/doc/wiki/trac/toba/wiki/$id.html";
		$img = toba_recurso::imagen_apl("wiki.gif", true);
		$tag = "<a href=$url target=wiki>$nombre</a>$img";
		return str_replace("'", "\\'", $tag);
	}
	
	protected static function parsear_api($id, $nombre)
	{
		$url = toba_recurso::path_pro()."/doc/api/$id.html";
		$img = toba_recurso::imagen_apl("api.gif", true);
		$tag = "<a href=$url  target=api>$nombre</a>$img";
		return str_replace("'", "\\'", $tag);
	}
	
	protected static function parsear_link($id, $nombre)
	{
		$url = toba_recurso::path_pro()."/".$id;
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