<?php
/*
*	
*/
class toba_ci_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ci';
	protected $deps;

	function agregar_dep($tipo, $id)
	{
		$clase = $tipo . '_molde';
		$this->deps[$id] = new $clase($this->asistente);
	}

	function dep($id)
	{
		if(!isset($this->deps[$id])){
			throw new toba_error("Molde CI: La dependencia '$id' no existe");
		}
		return $this->deps[$id];
	}
	
	function generar()
	{
		foreach($this->deps as $dep) {
			$dep->generar();	
		}
		//Asociar dependencias
		parent::generar();
	}
}
?>