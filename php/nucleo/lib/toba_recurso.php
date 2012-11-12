<?php
/**
 * Brinda servicios generales de salida HTML
 * @package SalidaGrafica
 */
class toba_recurso 
{
	
	/**
	 * Retorna la URL base del proyecto
	 * @param string $proyecto Opcional, sino se toma el actual si hay sesión
     * @param boolean $pres Opcional, si está en true entonces se devuelve la url relativa a la personalización
	 * @return string
	 */	
	static function url_proyecto($proyecto = null, $pers = false)
	{
		if (! isset($proyecto)) {
			$proyecto = toba::proyecto()->get_id();	
		}
		if ($pers) {
			return toba::instancia()->get_url_proyecto_pers($proyecto);
		} else {
			return toba::instancia()->get_url_proyecto($proyecto);
		}		
	}
	
	/**
	 * Retorna la URL base del runtime toba (donde esta el js, img y demas recursos globales a todos los proyectos)
	 * @return string
	 * @see toba_instalacion::get_url
	 */		
	static function url_toba()
	{
		$alias = toba::instalacion()->get_url();
		if (isset($alias)) {
			return $alias;
		}
		//-- Compatibilidad hacia atrás
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

	/**
	 * Retorna la URL base del skin actual del proyecto
	 * @return string
	 */			
	static function url_skin($skin=null, $proyecto=null)
	{
		if (! isset($skin)) {
			$skin = toba::proyecto()->get_parametro('estilo');
		}
		if (! isset($proyecto)) {
			$proyecto = toba::proyecto()->get_parametro('estilo_proyecto');
		}
		if ($proyecto == 'toba') {
			$url = self::url_toba();
		} else {
			$url = self::url_proyecto($proyecto);
		}
		return $url.'/skins/'.$skin;
	}

	//------------   ACCESO A IMAGENES   --------------

	/**
	 * Alias de imagen_Toba o imagen_proyecto dependiendo del origen
	 * @param string $origen Si es 'apex' usa imagen_toba sino del proyecto actual
	 */
	static function imagen_de_origen($nombre, $origen)
	{
		if ($origen == 'apex' || $origen == 'toba') {
			return self::imagen_toba($nombre);
		} elseif ($origen == 'proyecto') {
			return self::imagen_proyecto($nombre);
		} elseif ($origen == 'skin') {
			return self::imagen_skin($nombre);
		} else {
			throw new toba_error("No existe el origen de recurso $origen");	
		}
	}
	
	/**
	 * Retorna una imagen ubicada en el directorio www/img del proyecto
	 *
	 * @param string $imagen Path relativo a www/img de la imagen a generar
	 * @param boolean $html Generar el TAG 'img' (por def. false)
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $tooltip Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 */
	static function imagen_proyecto($imagen,$html=false,$ancho=null, $alto=null,$tooltip=null,$mapa=null, $proyecto=null)
	{
		if (toba::proyecto()->personalizacion_activa()) {
			$www = toba::proyecto()->get_www_pers("img/".$imagen);
			if (file_exists($www['path'])) {
				$src = $www['url'];
			} else { // el proy es personalizable pero no está definida esta imagen en particular
				$src = toba_recurso::url_proyecto($proyecto) . "/img/" . $imagen;
			}
		} else {
			$version = toba::memoria()->get_dato_instancia('proyecto_revision_recursos_cliente');
			$agregado_url = (!  is_null($version) && trim($imagen) != '') ? "?av=$version": '';
			$src = toba_recurso::url_proyecto($proyecto) . "/img/" . $imagen. $agregado_url;
		}

		if ($html){
			return toba_recurso::imagen($src, $ancho, $alto, $tooltip, $mapa);
		}else{
			return $src;
		}
	}
	
	/**
	 * Retorna una imagen perteneciente al skin actual del proyecto
	 *
	 * @param string $imagen Path relativo a www/skins/SKIN de la imagen a generar
	 * @param boolean $html Generar el TAG 'img' (por def. false)
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $tooltip Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 */
	static function imagen_skin($imagen,$html=false,$ancho=null, $alto=null,$tooltip=null,$mapa=null)
	{
		$src = toba_recurso::url_skin() . '/'. $imagen;
		if ($html){
			return toba_recurso::imagen($src, $ancho, $alto, $tooltip, $mapa);
		}else{
			return $src;
		}
	}	
	
	/**
	 * Retorna una imagen comun a todo el framework (ubicada en $toba_dir/www/img)
	 *
	 * @param string $imagen Path relativo a www/img de la imagen a generar
	 * @param boolean $html Generar el TAG 'img' (por def. false)
	 * @param string $ancho Ancho de la imagen (no oblig.)
	 * @param string $alto Alto de la imagen (no oblig.)
	 * @param string $tooltip Ayuda o tooltip que se muestra (por def. ninguna)
	 * @param string $mapa (no oblig.)
	 */		
	static function imagen_toba($imagen,$html=false,$ancho=null,$alto=null,$alt=null,$mapa=null,$js=null)
	{
		$version = toba::memoria()->get_dato_instancia('toba_revision_recursos_cliente');
		$agregado_url = (!  is_null($version) && trim($imagen) != '') ? "?av=$version": '';
		$src = toba_recurso::url_toba() . '/img/' . $imagen . $agregado_url ;
		if($html){
			return toba_recurso::imagen($src, $ancho, $alto, $alt,$mapa,$js);
		}else{
			return $src;
		}		
	}
	
	/**
	 * Construye un tag <img>
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
		if (toba_editor::modo_prueba()) {
			$img = "<img alt='' onerror='toba.falta_imagen(this.src)' src=$src $x $y $a $m  $estilo $js/>";
		} else {
			$img = "<img alt=\"\" src=$src $x $y $a $m  $estilo $js/>";
		}
		return $img;
	}

	/**
	 * Convierte una ayuda y una tecla de acceso en atributos html adecuados para un TAG
	 * Parseando los links y el accesskey
	 * @param char $tecla Tecla utiliza para acceder a la acción que contiene la ayuda, puede ser nula
	 * @param string $ayuda Ayuda que se va a incluir en la acción, no debe contener comillas simples sin quotear
	 * @param string $clases_css Clases css que se deben incluir en el tag en donde va la ayuda
	 * @param int $delay_ayuda Milisegundos que tarda en mostrarse la ayuda
	 * @return Atributos a incluir en un tag img, a, div, etc.
	 */
	static function ayuda($tecla, $ayuda='', $clases_css='', $delay_ayuda=1000)
	{
		$ayuda_extra = '';
		$a = '';
		if ($tecla !== null) {
			$ayuda_extra = "[alt + shift + $tecla]";
			$a = "accesskey='$tecla'";
		}
		if ($ayuda != '') {
			$ayuda .= ' '.$ayuda_extra;
			$ayuda = str_replace(array("\n", "\r"), '', $ayuda);
			$ayuda = str_replace(array("\""), "`", $ayuda);
			$a .= " onmouseover=\"if (typeof window.tipclick != 'undefined' &amp;&amp; window.tipclick !== null) return window.tipclick.show('$ayuda',this,event, $delay_ayuda);\" onmouseout=\"if (typeof window.tipclick != 'undefined' &amp;&amp; window.tipclick !== null) return window.tipclick.hide();\" ";
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
		return toba_recurso::url_toba() . "/js/" . $javascript;
	}
	
	static function link_css($archivo='toba', $rol='screen', $buscar_en_proyecto=true)
	{
		$link = '';

		$version = toba::memoria()->get_dato_instancia('toba_revision_recursos_cliente');
		$agregado_url = (!  is_null($version)) ? "?av=$version": '';		

		//--- Incluye primero el del nucleo
		$url = toba_recurso::url_toba()."/css/$archivo.css$agregado_url";
		$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";			

		//--- Incluye el del skin si es el estandar
		if ($archivo == 'toba') {
			$url = toba_recurso::url_skin()."/toba.css$agregado_url";
			$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";
		}

		//--- Incluye el del proyecto, si existe
		if ($buscar_en_proyecto) {
			$version = toba::memoria()->get_dato_instancia('proyecto_revision_recursos_cliente');
			$agregado_url = (!  is_null($version)) ? "?av=$version": '';		
			
			$proyecto = toba_proyecto::get_id();
			$path = toba::instancia()->get_path_proyecto($proyecto)."/www/css/$archivo.css";
			if (file_exists($path)) {
				$url = toba_recurso::url_proyecto($proyecto) . "/css/$archivo.css$agregado_url";
				$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";
			}
			if (toba::proyecto()->personalizacion_activa()) {
				$www = toba::proyecto()->get_www_pers("css/$archivo.css");
				if (file_exists($www['path'])) {
					$url = $www['url']. $agregado_url;
					$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";
				}
			}

			$path = toba::instancia()->get_path_proyecto($proyecto)."/www/css/".$archivo."_hack_ie.css";
			if (file_exists($path)) {
				$url = toba_recurso::url_proyecto($proyecto) . "/css/".$archivo."_hack_ie.css";
				$link .= "<!--[if lt IE 8]>\n";
				$link .= "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n\n";			
				$link .= "<![endif]-->\n";
			}			
		}
		return $link;		
	}
	
	
}
?>