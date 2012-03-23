<?php
require_once('catalogos/ci_catalogo.php');

class ci_catalogo_objetos extends ci_catalogo
{
	protected $album_fotos;
	protected $catalogo;
	protected $opciones;
	const foto_huerfanos = 'Objetos Huérfanos';
	const foto_ext_rotas = 'Extensiones PHP rotas';
	
	function ini()
	{
		$this->album_fotos = new album_fotos('cat_objeto');
		$this->catalogo = new toba_catalogo_objetos(toba_editor::get_proyecto_cargado());
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
			//Excluyo clases de persistencia del listado
			//$excluir = array('toba_datos_tabla', 'toba_datos_relacion');
			$excluir = array();
			return $this->catalogo->get_objetos($this->s__opciones, false, $excluir);
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
	
	//-- Proveer datos a combos ----------------

	function get_info_tipos_componente()
	{
		$datos = toba_info_editores::get_info_tipos_componente();
		/*foreach (array_keys($datos) as $id) {
			if ($datos[$id]['clase_tipo'] == 9) {
				//unset($datos[$id]);
			}
		}*/
		return $datos;
	}
	
}

?>