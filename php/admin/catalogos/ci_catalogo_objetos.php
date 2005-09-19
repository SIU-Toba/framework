<?php

//----------------------------------------------------------------
require_once('admin/catalogos/ci_catalogo.php'); 

class ci_catalogo_objetos extends ci_catalogo
{
	protected $album_fotos;
	protected $objetos;
	
	function __construct($id)
	{
		parent::__construct($id);
		$this->album_fotos = new album_fotos('cat_objeto');
		//$this->objetos = new catalogo_objetos(toba::get_hilo()->obtener_proyecto());
	}
	
	function evt__listado__carga()
	{
		$this->dependencias['listado']->set_frame_destino(apex_frame_centro);
		$this->dependencias['listado']->set_mostrar_raiz(false);
		//return $this->objetos;
		
	}
		
	
	
	

}

?>