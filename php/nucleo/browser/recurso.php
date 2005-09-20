<?php
require_once("nucleo/lib/motor_wiki.php");

class recurso {

	function preambulo()
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
	
	function path_pro()
	//#@desc: Genera un vinculo a un elemento del proyecto.
	{
		return recurso::preambulo(). "/". toba::get_hilo()->obtener_proyecto();
	}
	
	function path_apl()
	//#@desc: Genera un vinculo a un elemento general (comun a todos los proyectos).
	{
		if (defined('apex_pa_toba_alias'))
			$alias = apex_pa_toba_alias;
		else
			$alias = "toba";
		return recurso::preambulo(). "/". $alias;
	}

	//------------   ACCESO A IMAGENES   --------------

	function imagen_de_origen($nombre, $origen)
	{
		if ($origen == 'apex')
			return self::imagen_apl($nombre);
		else
			return self::imagen_pro($nombre);
	}
	
	function imagen_pro($imagen,$html=false,$ancho=null, $alto=null,$alt=null,$mapa=null)
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
	
	function imagen_apl($imagen,$html=false,$ancho=null,$alto=null,$alt=null,$mapa=null)
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
	
	function imagen($src,$ancho=null,$alto=null,$alt=null,$mapa=null, $js='', $estilo='')
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
/*		if(isset($alt)) {
			$a = " onMouseover=\"ddrivetip('". ereg_replace("/\n|\r/","",$alt) ."')\" onMouseout=\"hideddrivetip()\" ";
		}
*/
		if(isset($alt)) {
			$wiki_entrar = "";
			$wiki_salir = "";
			if (motor_wiki::tiene_wiki($alt)) {
				$ayuda = motor_wiki::formato_texto($alt)."\n Presione una tecla para ver más ayuda";
				$wiki = motor_wiki::link_wiki($alt);
				$wiki_entrar = "url_wiki=\"{$wiki[0]}\"";
				$wiki_salir = "url_wiki=null";
			} else {
				$ayuda = $alt;
			}
			$ayuda = str_replace(array("\n", "\r"), '', $ayuda);
			$ayuda = str_replace(array("'"), "`", $ayuda);	
			$a = " title='$ayuda' onmouseover='window.status=this.title; $wiki_entrar' onmouseout='window.status=\"\"; $wiki_salir'";
		}
		if(isset($mapa)) 
			$m = " usemap='$mapa'";
		$img = "<img border='0' src='$src' $x $y $a $m  style='margin: 0px 0px 0px 0px; $estilo' $js/>";
		return $img;
	}

	//------------   ACCESO A OTROS   --------------
	
	function js($javascript)
	//#@desc: Genera un vinculo a un archivo javascript independiente
	//@@par archivo (requerido) Archivo javascript que se desea cargar
	{
		return recurso::path_apl() . "/js/" . $javascript;
	}
	
	function css($estilo="")
	//Genera un vinculo a la hoja de estilos.
    //Por defecto saca el estilo del proyecto de la sesion.
    //Cuando se llama en un momento que no hay sesion (Como en la pantalla de LOGON)
    //hay que llamarlo con un parametro.
	{
		if($estilo!="")
			return recurso::path_apl() . "/css/" . $estilo .".css";	
		else {
            //SI no hay una sesion esto da error.
			$proyecto = toba::get_hilo()->obtener_proyecto();
			if($proyecto != "toba") {
				$path = toba_dir() . "/proyectos/$proyecto/www/css/" . apex_proyecto_estilo . ".css";
				if (file_exists($path))
					return recurso::path_pro() . "/css/" . apex_proyecto_estilo .".css";
			} 
			return recurso::path_apl() . "/css/" . apex_proyecto_estilo .".css"; 
        }
	}

	function link_css($estilo=apex_proyecto_estilo)
	{
		return "<link href='". recurso::css($estilo). "' rel='stylesheet' type='text/css'>";
	}
}
?>