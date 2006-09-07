<?php
require_once("nucleo/lib/toba_parser_ayuda.php");


/**
 * Brinda servicios generales de salida HTML
 * @package SalidaGrafica
 */
class toba_recurso {

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
	
	/**
	 * Retorna una imagen del proyecto
	 *
	 * @param string $imagen Path relativo a www/img de la imagen a generar
	 * @param boolean $html Generar el TAG 'img' (por def. false)
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $tooltip Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 */
	static function imagen_pro($imagen,$html=false,$ancho=null, $alto=null,$tooltip=null,$mapa=null)
	{
		$src = toba_recurso::path_pro() . "/img/" . $imagen;
		if($html){
			return toba_recurso::imagen($src, $ancho, $alto, $tooltip, $mapa);
		}else{
			return $src;
		}
	}
	
	
	/**
	 * Retorna una imagen del framework
	 *
	 * @param string $imagen Path relativo a www/img de la imagen a generar
	 * @param boolean $html Generar el TAG 'img' (por def. false)
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $tooltip Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 */	
	static function imagen_apl($imagen,$html=false,$ancho=null,$alto=null,$alt=null,$mapa=null)
	{
		$src = toba_recurso::path_apl() . "/img/" . $imagen;
		if($html){
			return toba_recurso::imagen($src, $ancho, $alto, $alt,$mapa);
		}else{
			return $src;
		}
	}
	
	/**
	 * Retorna el tag <img>
	 *
	 * @param string $src Url utilizada en el src del tag
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $alt Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 * @param string $js Evento js (e.g. onclick='...')
	 * @param string $estilo (e.g. style='...')
	 */		
	static function imagen($src,$ancho=null,$alto=null,$alt=null,$mapa=null, $js='', $estilo='')
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
	 * @return Atributos a incluir en un tag img, a, div, etc.
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

	/**
	 * Genera una URL a un recurso js
	 * @param string $javascript Path relativo del recurso
	 */
	static function js($javascript)
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