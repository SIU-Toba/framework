<?php
/*
*	
*/
class toba_ci_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ci';
	protected $deps = array();
	protected $mapeo_pantallas;

	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->nueva_fila(array());
		$this->datos->tabla('prop_basicas')->set_cursor(0);
	}

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function agregar_pantalla($identificador, $etiqueta=null)
	{
		$datos = array('identificador'=>$identificador, 'etiqueta'=>$etiqueta);
		$id = $this->datos->tabla('pantallas')->nueva_fila($datos);
		$this->mapeo_pantallas[$identificador] = $id;
	}	
	
	//---------------------------------------------------
	//-- Manejo de SUBCOMPONENTES
	//---------------------------------------------------	

	function agregar_dep($tipo, $id, $pantalla=null)
	{
		$clase = $tipo . '_molde';
		$this->deps[$id] = new $clase($this->asistente);
		//Asignacion a pantallas
		if(isset($pantalla)){
			if(!isset($this->mapeo_pantallas[$pantalla])){
				throw new toba_error('Molde CI: La pantalla solicitada no existe.');
			}
			$id_fila = $this->mapeo_pantallas[$pantalla];
			$this->datos->tabla('pantallas')->set_dependencias_pantalla($id_fila,array($id));
		}
	}

	function dep($id)
	{
		if(!isset($this->deps[$id])){
			throw new toba_error("La dependencia '$id' no existe");
		}
		return $this->deps[$id];
	}
	
	function generar()
	{
		foreach($this->deps as $id => $dep) {
			$dep->generar();
			$clave = $dep->get_clave_componente_generado();
			$this->asociar_dependencia($id, $clave['clave']);
		}
		parent::generar();
	}
	
	function asociar_dependencia($id, $clave)
	{
		$datos = array('proyecto'=>$this->proyecto, 'objeto_proveedor'=>$clave, 'identificador'=> $id);
		$this->datos->tabla('dependencias')->nueva_fila( $datos );
	}
}
?>