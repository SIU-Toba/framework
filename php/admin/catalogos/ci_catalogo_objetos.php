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
	const foto_huerfanos = 'Objetos Huérfanos';
	const foto_ext_rotas = 'Extensiones PHP rotas';
	
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
	
	function evt__fotos__carga()
	{
		$fotos = parent::evt__fotos__carga();
		$huerfanos['foto_nombre'] = self::foto_huerfanos;
		$huerfanos['predeterminada'] = 0;
		$huerfanos['defecto'] = 'nulo.gif';
		$ext_rotas['foto_nombre'] = self::foto_ext_rotas;
		$ext_rotas['predeterminada'] = 0;
		$ext_rotas['defecto'] = 'nulo.gif';
		$fotos[] = $huerfanos;
		$fotos[] = $ext_rotas;
		return $fotos;
		//ei_arbol($fotos);
	}
	
	function evt__fotos__seleccion($nombre)
	{
		unset($this->opciones);						
		switch ( $nombre['foto_nombre']) {
			case self::foto_ext_rotas:
				$this->opciones['extensiones_rotas'] = 1;
				break;
			case self::foto_huerfanos:
				$this->opciones['huerfanos'] = 1;
				break;
			default:
				parent::evt__fotos__seleccion($nombre);
		}
	}
	
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