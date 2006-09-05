<?php
require_once("nucleo/lib/toba_parser_ayuda.php");

class toba_recurso {

	static function preambulo()
	//#@desc: Devuelve el preambulo de los links (protocolo utilizado).
	{
		if(defined('apex_pa_SSL') && apex_pa_SSL){
			$protocolo = "https://";
		}else{
			$protocolo = "http://";
		}
		if (isset($_SERVER['HTTP_HOST']))	//Necesario para ejecutar los test desde consola
			return $protocolo . $_SERVER["HTTP_HOST"];
	}

	//------------   PATH a las carpetas de toba_recursos   --------------

	/**
	 * Retorna la URL base del proyecto
	 * @param string $proyecto Opcional, sino se toma el actual si hay sesión
	 * @return string
	 */
	static function path_pro($proyecto=null)
	{
		if (isset($_SERVER['TOBA_PROYECTO_ALIAS'])) {
			$alias = $_SERVER['TOBA_PROYECTO_ALIAS'];
		} else {
			if (!isset($proyecto)) {
				$alias = toba::proyecto()->get_id();
			} else {
				$alias = $proyecto;
			}
		}
		return '/'.$alias;
	}
	
	/**
	 * Retorna la URL base de toba
	 * @return string
	 */	
	static function path_apl()
	{
		if (isset($_SERVER['TOBA_ALIAS'])) {
			$alias = $_SERVER['TOBA_ALIAS'];
		}else{
			if (defined('apex_pa_toba_alias')){
				$alias = apex_pa_toba_alias;
			}else{
				$alias = "toba";
			}
		}
		return '/'.$alias;
	}

	//------------   ACCESO A IMAGENES   --------------

	static function imagen_de_origen($nombre, $origen)
	{
		if ($origen == 'apex')
			return self::imagen_apl($nombre);
		else
			return self::imagen_pro($nombre);
	}
	
	static function imagen_pro($imagen,$html=false,$ancho=null, $alto=null,$alt=null,$mapa=null)
/*	
    @@acceso: actividad
    @@desc: Genera un vinculo a las imagenes del proyecto
	@@param: Imagen (requerido) Imagen que se desea cargar
	@@param: boolean | Generar el TAG 'img' | false
	@@param: int | Ancho de la imagen | null
	@@param: int | Alto de la imagen | null
	@@param: string | Tooltip de la imagen | null
	@@param: string | Agregar un MAPA de navegacion | null
 	@@retorno: string | URL del toba_recurso o TAG 'img' generado, de acuerdo al parametro 2
*/
	{
		$src = toba_recurso::path_pro() . "/img/" . $imagen;
		if($html){
			return toba_recurso::imagen($src, $ancho, $alto, $alt, $mapa);
		}else{
			return $src;
		}
	}
	
	static function imagen_apl($imagen,$html=false,$ancho=null,$alto=null,$alt=null,$mapa=null)
/*
 	@@acceso: actividad
	@@desc: Genera la URL de un toba_recurso de tipo imagen (puede crear el TAG 'img')
	@@param: string | path relativo de la imagen
	@@param: boolean | Generar el TAG 'img' | false
	@@param: int | Ancho de la imagen | null
	@@param: int | Alto de la imagen | null
	@@param: string | Tooltip de la imagen | null
	@@param: string | Agregar un MAPA de navegacion | null
 	@@retorno: string | URL del toba_recurso o TAG 'img' generado, de acuerdo al parametro 2
*/
	{
		$src = toba_recurso::path_apl() . "/img/" . $imagen;
		if($html){
			return toba_recurso::imagen($src, $ancho, $alto, $alt,$mapa);
		}else{
			return $src;
		}
	}
	
	static function imagen($src,$ancho=null,$alto=null,$alt=null,$mapa=null, $js='', $estilo='')
/*
 	@@acceso: interno
	@@desc: Genera la URL de un toba_recurso de tipo imagen. Funcion utilizada por imagen_apl e imagen_pro
	@@param: string | path relativo de la imagen
	@@param: int | Ancho de la imagen | null
	@@param: int | Alto de la imagen | null
	@@param: string | Tooltip de la imagen | null
	@@param: string | Agregar un MAPA de navegacion | null
	@@param: string | Eventos js | null
	@@param: string | estilos extra | null
 	@@retorno: string | URL del toba_recurso o TAG 'img' generado
*/
	{
		$wiki = false;
		$x = ""; $y = ""; $a="";$m="";
		if(isset($ancho)) $x = " width='$ancho' ";
		if(isset($alto)) $y = " height='$alto' ";

		if(isset($alt)) {
			$a = self::ayuda(null, $alt);
		}
		if(isset($mapa) && $mapa != '') {
			$m = " usemap='$mapa'";
		}
		if ($estilo != '') {
			$estilo ="style=\"$estilo\"";
		}
		$img = "<img border=0 src=$src $x $y $a $m  alt=\"\" $estilo $js/>";
		return $img;
	}

	/**
	 * Convierte una ayuda y una tecla de acceso en atributos html adecuados para un TAG
	 * Parseando los links y el accesskey
	 * @param char $tecla Tecla utiliza para acceder a la acción que contiene la ayuda, puede ser nula
	 * @param string $ayuda Ayuda que se va a incluir en la acción, no debe contener comillas simples sin quotear
	 * @param string $clases_css Clases css que se deben incluir en el tag en donde va la ayuda
	 * @return unknown
	 */
	static function ayuda($tecla, $ayuda='', $clases_css='')
	{
		$ayuda_extra = '';
		$a = '';
		if ($tecla !== null) {
			$ayuda_extra = "[ALT $tecla]";
			$a = "accesskey='$tecla'";
		}
		if ($ayuda != '') {
			$ayuda .= ' '.$ayuda_extra;
			$ayuda = toba_parser_ayuda::parsear($ayuda);
			$ayuda = str_replace(array("\n", "\r"), '', $ayuda);
			$ayuda = str_replace(array("\""), "`", $ayuda);
			$a .= " onmouseover=\"if (window.tipclick) return tipclick.show('$ayuda',this,event);\" onmouseout=\"if (window.tipclick) return tipclick.hide();\" ";
			$clases_css .= ' ayuda';
		} else {
			$a .= " title='$ayuda_extra'";
		}
		if ($clases_css != "") {
			$a .= " class='$clases_css'";
		}
		return $a;
	}	
	
	//------------   ACCESO A OTROS   --------------
	
	static function js($javascript)
	//#@desc: Genera un vinculo a un archivo javascript independiente
	//@@par archivo (requerido) Archivo javascript que se desea cargar
	{
		return toba_recurso::path_apl() . "/js/" . $javascript;
	}
	
	

	/**
	*	Crea el tag <link>
	*	@param string $estilo Nombre de la plantilla (sin incluir extension)
	*	@param string $rol 	  Tipo de medio en el html (tipicamente screen o print)
	*/
	static function link_css($estilo=null,  $rol='screen')
	{
		$estilo = isset($estilo) ? $estilo : toba::proyecto()->get_parametro('estilo');
		$link = '';
		
		//Busca primero en el nucleo
 		if (file_exists(toba_dir()."/www/css/$estilo.css")) {
			$url = toba_recurso::path_apl()."/css/$estilo.css";
			$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";			
		}
		//Busca tambien en el proyecto
		$proyecto = toba_proyecto::get_id();
		$path = toba_instancia::get_path_proyecto($proyecto)."/www/css/$estilo.css";
		if (file_exists($path)) {
			$url = toba_recurso::path_pro($proyecto) . "/css/$estilo.css";
			$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";			
		}
		return $link;
	}
}
?>