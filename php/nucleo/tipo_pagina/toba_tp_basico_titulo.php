<?php
/**
* 
* Incluye una barra con nombre y ayuda contextual de la operación, 
* y centraliza el contenido de la salida de la operación
* 
* @package SalidaGrafica
*/
class toba_tp_basico_titulo extends toba_tp_basico
{
	protected $clase_encabezado = 'encabezado';	

	//---------------------------------------------------------------------------------------------------------------------------------//
	//					INTERFACES ORIGINALES
	//---------------------------------------------------------------------------------------------------------------------------------//
	function barra_superior()
	{
		echo toba::output()->get('PaginaTitulo')->getContenidoBarraSuperior($this->titulo_item(), $this->info_version(), $this->generar_ayuda());
	}
	
	protected function estilos_css()
	{
		parent::estilos_css();
		echo toba::output()->get('PaginaTitulo')->getEstiloCss();
	}	
	
	protected function generar_ayuda()
	{
		$mensaje = toba::mensajes()->get_operacion_actual();
		if (isset($mensaje)) {
			return toba::output()->get('PaginaTitulo')->getParseAyuda($mensaje);
		}	
	}
	
	/**
	 * Retorna el título de la opreación actual, utilizado en la barra superior
	 */
	protected function titulo_item()
	{
		return toba::solicitud()->get_datos_item('item_nombre');
	}

	protected function info_version()
	{
		$version = toba::proyecto()->get_parametro('version');
		if( $version && ! (toba::proyecto()->get_id() == 'toba_editor') ) {
			$version_fecha = toba::proyecto()->get_parametro('version_fecha');
			$version_link = toba::proyecto()->get_parametro('version_link');
			$version_detalle = toba::proyecto()->get_parametro('version_detalle');
			return toba::output()->get('PaginaTitulo')->getParseVersion($version, $version_fecha, $version_detalle, $version_link);				
		}
	}	
		
	function pre_contenido()
	{
		echo toba::output()->get('PaginaTitulo')->getPreContenido();
	}
	
	function post_contenido()
	{
		echo toba::output()->get('PaginaTitulo')->getPostContenido();
	}

	//---------------------------------------------------------------------------------------------------------------------------------//
	//					INTERFACES AGREGADAS
	//---------------------------------------------------------------------------------------------------------------------------------//
	function inicio_encabezado_html()
	{
		echo toba::output()->get('PaginaTitulo')->getPreEncabezadoHtml();
	}
	
	function fin_encabezado_html()
	{
		echo toba::output()->get('PaginaTitulo')->getPostEncabezadoHtml();
	}
	
	function inicio_barra_superior()
	{
		echo toba::output()->get('PaginaTitulo')->getInicioBarraSuperior();
	}
		
	function fin_barra_superior()
	{
		echo toba::output()->get('PaginaTitulo')->getFinBarraSuperior();
	}
	
	protected function footer()
	{
		echo toba::output()->get('PaginaTitulo')->getFooterHtml();
	}			
}
?>
