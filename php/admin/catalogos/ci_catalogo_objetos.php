<?php

//----------------------------------------------------------------
require_once('admin/catalogos/ci_catalogo.php'); 
require_once('modelo/lib/catalogo_objetos.php');
require_once('nucleo/componentes/info/info_componente.php');

class ci_catalogo_objetos extends ci_catalogo
{
	protected $album_fotos;
	protected $catalogo;
	protected $opciones;
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->album_fotos = new album_fotos('cat_objeto');
		$this->catalogo = new catalogo_objetos(toba::get_hilo()->obtener_proyecto());
	}
	

	function agregar_foto_inicial()
	{
		$this->album_fotos->agregar_foto(apex_foto_inicial, array(), array(), false);
	}

	
	
	//-------------------------------------------------------------
	//-------------------EVENTOS DE OBJETOS -----------------------
	//-------------------------------------------------------------
	
	function evt__listado__carga()
	{
		$this->dependencia('listado')->set_frame_destino(apex_frame_centro);
		if (isset($this->opciones)) {
			return $this->catalogo->get_objetos($this->opciones);
		}
	}
		
	function evt__opciones__filtrar($opciones)
	{
		$this->opciones = $opciones;
	}
	
	function evt__opciones__cancelar()
	{
		unset($this->opciones);	
	}
	
	function evt__opciones__carga()
	{
		if (isset($this->opciones)) {
			$this->dependencia('opciones')->colapsar();
			return $this->opciones;
		}
	}
	
	
	
}

?>