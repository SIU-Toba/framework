<?php
class ci_cliente extends toba_ci
{
	protected $s__echo;
	protected $s__datos_password;
	protected $s__adjunto;
	protected $adjunto_respuesta;
	protected $datos_persona;
	protected $path_servicio = "servicios/seguro_configuracion/servicio.php";
	
	function ini()
	{
		if (! extension_loaded('wsf')) {
			toba::notificacion()->error("No se encuentra instalada la extensión wsf de php.".
			" <a href='http://toba.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb'>Ver documentación</a>");
		}
	}
	

	/**
	 * Seguridad configurada en la instalacion
	 */
	function evt__form__enviar($datos)
	{
		//--1- Arma el mensaje	(incluyendo los headers)
		$this->s__echo = $datos;
		$opciones = array(
				'action' => 'test',
		);
		$mensaje = new toba_servicio_web_mensaje($this->s__echo, $opciones);
	
		//--2- Arma el servicio
		$opciones = array(
		    'to' => 'http://localhost/'.toba_recurso::url_proyecto().'/servicios.php/serv_seguro_configuracion',
		);
		$servicio = toba::servicio_web('cli_seguro_configuracion', $opciones);
	
		//-- 3 - Muestra la respuesta
		$respuesta = $servicio->request($mensaje);
		toba::notificacion()->info(print_r($respuesta->get_array(), true));
	}	
	
	
	//-----------------------------------------------------------------------------
	//---- Utilidades  -----------------------------------------------------------
	//------------------------------------------------------------------------------
	
	function post_configurar()
	{
		parent::post_configurar();
		$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		$cliente = 'servicios/seguro_configuracion/ci_cliente.php';
		$url_cliente = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $cliente), array('prefijo'=>toba_editor::get_punto_acceso_editor()));		
		$url_servicio = toba::vinculador()->get_url('toba_editor', '30000014', array('archivo' => $this->path_servicio), array('prefijo'=>toba_editor::get_punto_acceso_editor()));
		$html = "<div style='float:right; background-color:white; padding: 10px'><a target='logger' href='$url_cliente'>$img Ver .php del Cliente</a>";
		$html .= "<br><a target='logger' href='$url_servicio'>$img Ver .php del Servicio</a>";
		$url_ejemplos = 'http://repositorio.siu.edu.ar/trac/toba/wiki/Referencia/ServiciosWeb';
		$html .= "<br>Documentación de <a target='_blank' href='$url_ejemplos'>servicios web en toba</a></div>";
		$html .= $this->pantalla()->get_descripcion();		
		$this->pantalla()->set_descripcion($html);
	}
	
	function formatear_valor($valor)
	{
		$estilo = 'style="background-color: white; border: 1px solid gray; padding: 5px;"';		
		return  "<pre $estilo>".htmlentities($valor).'</pre>';
	}
	
	
		
	
}

?>