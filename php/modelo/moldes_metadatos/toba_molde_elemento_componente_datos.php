<?php

/*
*	
*/
class toba_molde_elemento_componente_datos extends toba_molde_elemento_componente
{
	function archivo_relativo()
	{
		return $this->archivo;		
	}
	
	function archivo_absoluto()
	{
		return $this->directorio_absoluto() .'/'. $this->archivo;		
	}

	function directorio_absoluto()
	{
		$datos = $this->datos->tabla('base')->get_fila(0);		
		if (!is_null($datos['punto_montaje']) && ($datos['punto_montaje'] !== 0)) { 	
			$punto_montaje = toba_pms::instancia()->get_instancia_pm_proyecto($datos['proyecto'], $datos['punto_montaje']);
			return $punto_montaje->get_path_absoluto(). '/' ;
		} else {
			return parent::directorio_absoluto();
		}
	}

	function directorio_relativo()
	{
		//Redefino para eliminar comportamiento, el directorio ya esta incluido dentro de $this->archvo
	}
}
?>