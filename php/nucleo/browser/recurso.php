<?php
require_once("nucleo/lib/parser_ayuda.php");

class recurso {

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

	//------------   PATH a las carpetas de recursos   --------------

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
				$alias = toba::get_hilo()->obtener_proyecto();
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
 	@@retorno: string | URL del recurso o TAG 'img' generado, de acuerdo al parametro 2
*/
	{
		$src = recurso::path_pro() . "/img/" . $imagen;
		if($html){
			return recurso::imagen($src, $ancho, $alto, $alt, $mapa);
		}else{
			return $src;
		}
	}
	
	static function imagen_apl($imagen,$html=false,$ancho=null,$alto=null,$alt=null,$mapa=null)
/*
 	@@acceso: actividad
	@@desc: Genera la URL de un recurso de tipo imagen (puede crear el TAG 'img')
	@@param: string | path relativo de la imagen
	@@param: boolean | Generar el TAG 'img' | false
	@@param: int | Ancho de la imagen | null
	@@param: int | Alto de la imagen | null
	@@param: string | Tooltip de la imagen | null
	@@param: string | Agregar un MAPA de navegacion | null
 	@@retorno: string | URL del recurso o TAG 'img' generado, de acuerdo al parametro 2
*/
	{
		$src = recurso::path_apl() . "/img/" . $imagen;
		if($html){
			return recurso::imagen($src, $ancho, $alto, $alt,$mapa);
		}else{
			return $src;
		}
	}
	
	static function imagen($src,$ancho=null,$alto=null,$alt=null,$mapa=null, $js='', $estilo='')
/*
 	@@acceso: interno
	@@desc: Genera la URL de un recurso de tipo imagen. Funcion utilizada por imagen_apl e imagen_pro
	@@param: string | path relativo de la imagen
	@@param: int | Ancho de la imagen | null
	@@param: int | Alto de la imagen | null
	@@param: string | Tooltip de la imagen | null
	@@param: string | Agregar un MAPA de navegacion | null
	@@param: string | Eventos js | null
	@@param: string | estilos extra | null
 	@@retorno: string | URL del recurso o TAG 'img' generado
*/
	{
		$wiki = false;
		$x = ""; $y = ""; $a="";$m="";
		if(isset($ancho)) $x = " width='$ancho' ";
		if(isset($alto)) $y = " height='$alto' ";

		if(isset($alt)) {
			$a = self::ayuda(null, $alt);
		}
		if(isset($mapa)) {
			$m = " usemap='$mapa'";
		}
		$img = "<img border=0 src=$src $x $y $a $m  style='margin: 0px 0px 0px 0px; $estilo' $js/>";
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
			$ayuda .= $ayuda_extra;
			$ayuda = parser_ayuda::parsear($ayuda);
			$ayuda = str_replace(array("\n", "\r"), '', $ayuda);
			$ayuda = str_replace(array("\""), "`", $ayuda);
			$a = " class='ayuda $clases_css' onmouseover=\"return dhtml_tooltip.show('$ayuda',this,event);\" onmouseout=\"return dhtml_tooltip.hide();\" ";
		} else {
			$a .= " title='$ayuda_extra'";
		}
		return $a;
	}	
	
	//------------   ACCESO A OTROS   --------------
	
	static function js($javascript)
	//#@desc: Genera un vinculo a un archivo javascript independiente
	//@@par archivo (requerido) Archivo javascript que se desea cargar
	{
		return recurso::path_apl() . "/js/" . $javascript;
	}
	
	
	/**
	*	Dado el nombre de una plantilla, encuentra la url  si es que existe
	*	Para esto primero busca en el proyecto y si no lo encuentra lo busca en el mismo toba
	*/
	static function css($nombre=apex_proyecto_estilo)
	{
		$hilo = toba::get_hilo();
		//Si esta abierta la sesion
		if ($hilo->sesion_abierta()) {
			$proyecto = $hilo->obtener_proyecto();
		} else {
			//Si no se trata de buscar el proyecto en el PA
			if (defined('apex_pa_proyecto')) {
				$proyecto = apex_pa_proyecto;
			} else {
				$proyecto = "toba";	
			}
		}
		//Si es un proyecto particular, buscar primero en el mismo		
		if($proyecto != "toba") {
			$path = toba_dir() . "/proyectos/$proyecto/www/css/$nombre.css";
			if (file_exists($path)) {
				return recurso::path_pro($proyecto) . "/css/$nombre.css";
			}
		}
		//Sino buscarlo en el proyecto toba
 		if (file_exists(toba_dir()."/www/css/$nombre.css")) {
			return recurso::path_apl()."/css/$nombre.css";
		}
	}


	/**
	*	Crea el tag <link>
	*	@param string $estilo Nombre de la plantilla (sin incluir extension)
	*	@param string $rol 	  Tipo de medio en el html (tipicamente screen o print)
	*/
	static function link_css($estilo=apex_proyecto_estilo,  $rol='screen')
	{
		$url = recurso::css($estilo);
		if ($url != null) {
			return "<link href='$url' rel='stylesheet' type='text/css' media='$rol'/>\n";
		}
	}
}
?>