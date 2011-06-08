<?php
/**
 * Clase abstracta para manejo de un gadget.
 * @package Centrales 
 */

abstract class toba_gadget
{
	protected $_id_gadget;
	protected $_datos = array();
	
	function __construct($id = null)
	{
			//No hago obligatorio el ID por si algun dia se agregan gadgets en runtime sin que esten en la base de toba.
			if (! is_null($id)) {
				$this->_id_gadget = $id;
			}
	}
		
	//----------------------------------------------------------------------------------------- MANIPULACION DE PROPIEDADES --------------------------------------------------------------------------------
	function get_titulo()
	{
		$result = (isset($this->_datos['titulo'])) ? $this->_datos['titulo'] : '';
		return $result;
	}

	function get_descripcion()
	{
		$result = (isset($this->_datos['descripcion'])) ? $this->_datos['descripcion'] : '';
		return $result;
	}
	
	function get_gadget_url()
	{
		$result = (isset($this->_datos['gadget_url'])) ? $this->_datos['gadget_url'] : '';
		return $result;
	}

	function get_tipo()
	{
		return apex_tipo_gadget_interno;
	}

	function get_clase()
	{
		//Busco las dos claves que me sirven, van ambas juntas o nada.
		$result = array_intersect_keys($this->_datos, array('subclase' => '1', 'subclase_archivo'  => '1'));
		if (count($result) < 2) {
			throw toba_error_def('La definición de subclase para el gadget esta incompleta');
		}
		return $result;
	}

	function get_orden()
	{
		$result = (isset($this->_datos['orden'])) ? $this->_datos['orden'] : '1';
		return $result;
	}

	function es_eliminable()
	{
		return (isset($this->_datos['eliminable']) && ($this->_datos['eliminable'] == 'S'));
	}
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	function set_titulo($titulo)
	{
		$this->_datos['titulo'] = $titulo;
	}

	function set_descripcion($descripcion)
	{
		$this->_datos['descripcion'] = $descripcion;
	}

	function set_gadget_url($url)
	{
		$this->_datos['gadget_url'] = $url;
	}

	function set_clase($subclase, $subclase_archivo)
	{
		$this->_datos['subclase']  = $subclase;
		$this->_datos['subclase_archivo'] = $subclase_archivo;
	}

	function set_orden($orden)
	{
		$this->_datos['orden'] = $orden;
	}

	function set_eliminable($eliminable)
	{
		$this->_datos['eliminable'] = $eliminable;
	}

	//--------------------------------------------------------------------------------------------------------------- SALIDA DEL GADGET -----------------------------------------------------------------------------------------
	function generar_html()
	{
	}	
}
?>

<?php
// -------------------------------------------------------------------	ESTO QUEDA AQUI A MODO DE RECORDATORIO PARA REVISAR EL BUG DEL SERVICIO generar_html ----------------------------------------------------------------------
/*
	static function generar_gadget_interno($gadget) {
		toba::logger()->seccion('== Inicio de gadget interno '.$gadget['titulo'].' ==','toba');
		$solicitud = toba_constructor::get_runtime(array('proyecto'=>$gadget['proyecto'],'componente'=>$gadget['item']), 'toba_item');
		echo "<div id='gadget-chrome-{$gadget['orden']}' class='gadgets-gadget-chrome'>";
		echo "<div id='gadgets-gadget-title-bar-{$gadget['orden']}' class='gadgets-gadget-title-bar'>";
		echo "<span class='gadgets-gadget-title' id='remote_iframe_{$gadget['orden']}_title'>{$gadget['titulo']}</span> | ";
		echo "<span class='gadgets-gadget-title-button-bar' onclick='var c=document.getElementById(\"gadget-content-{$gadget['orden']}\");if(c.style.display==\"none\") {c.style.display=\"block\"} else {c.style.display=\"none\"}' style='text-decoration: underline; cursor:pointer'>toggle</span>";
		if ($gadget['eliminable']=='S') {
		echo "<img style='float:right; margin-right: 5px; margin-left: 5px; margin-top: 1px; margin-bottom: 1px; width: 15px; height: 15px' onClick='this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)' src='".toba_recurso::imagen_toba('finalizar_sesion.gif',false)."'/>";
		}
		echo "</div>\n";
		echo "<div id='gadget-content-{$gadget['orden']}' class='gadgets-gadget-content'>";
		echo "<div style='background:none; width: 250px; padding: 3px;'>";
		if($solicitud->get_datos_item('tipo_pagina_clase') == 'toba_tipo_pagina') {
			/*
			 * bug en la linea 231, de la función servicio__generar_html de toba_solicitud_web
			 * aunque se le indique que el componente no tiene salida (clase 'toba_tipo_pagina') en toba editor,
			 * lo mismoincluye el cierre de dos divs (barra superior y encabezado) aunque no hayan sido creados.
			 *
			 *  Se debería chequear el tipo de página.
			 */
		/*	echo "<div style='display: none'><div>";
		}
		$solicitud->procesar();
		echo "</div>\n";
		echo "</div>\n";
		echo "</div>\n";
		toba::logger()->seccion('== Fin de gadget interno '.$gadget['titulo'].' ==','toba');
	}*/
// -------------------------------------------------------------------	ESTO QUEDA AQUI A MODO DE RECORDATORIO PARA REVISAR EL BUG DEL SERVICIO generar_html ----------------------------------------------------------------------
?>