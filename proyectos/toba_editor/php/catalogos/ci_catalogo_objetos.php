<?php

require_once('catalogos/ci_catalogo.php'); 
require_once('modelo/lib/catalogo_objetos.php');
require_once('modelo/componentes/info_componente.php');

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
		$this->catalogo = new catalogo_objetos(editor::get_proyecto_cargado());
	}
	
	function agregar_foto_inicial()
	{
		$this->album_fotos->agregar_foto(apex_foto_inicial, array(), array(), false);
	}
	
	//-------------------------------------------------------------
	//-------------------EVENTOS DE OBJETOS -----------------------
	//-------------------------------------------------------------
	
	function conf__fotos()
	{
		$fotos = parent::conf__fotos();
		$predefinidas = array();
		$predefinidas[] = self::foto_huerfanos;
		$predefinidas[] = self::foto_ext_rotas;
		foreach ($predefinidas as $id) {
			$foto = array();
			$foto['foto_nombre'] = $id;
			$foto['predeterminada'] = 0;
			$foto['defecto'] = 'nulo.gif';
			$fotos[] = $foto;
		}
		$this->dependencia('fotos')->set_fotos_predefinidas($predefinidas);
		return $fotos;
	}	
	
	function evt__fotos__seleccion($nombre)
	{
		unset($this->s__opciones);						
		switch ( $nombre['foto_nombre']) {
			case self::foto_ext_rotas:
				$this->s__opciones['extensiones_rotas'] = 1;
				break;
			case self::foto_huerfanos:
				$this->s__opciones['huerfanos'] = 1;
				break;
			default:
				parent::evt__fotos__seleccion($nombre);
		}
	}
	
	function conf__listado()
	{
		$this->dependencia('listado')->set_frame_destino(apex_frame_centro);
		if (isset($this->s__opciones)) {
			return $this->catalogo->get_objetos($this->s__opciones);
		}
	}
	
	function evt__listado__cargar_nodo($id)
	{
		$this->dependencia('listado')->set_frame_destino(apex_frame_centro);		
		if (isset($this->s__opciones)) {
			$opciones = $this->s__opciones;
		}
		$opciones['id'] = $id;
		$obj = $this->catalogo->get_objetos($opciones, true);
		return $obj;
	}	
		
	function conf__filtro()
	{
		if (isset($this->s__opciones)) {
			$this->dependencia('filtro')->colapsar();			
			return $this->s__opciones;
		}
	}	
	
	
	
}

?>