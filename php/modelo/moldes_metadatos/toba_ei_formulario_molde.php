<?php
/*
*	
*/
class toba_ei_formulario_molde extends toba_molde_elemento_componente_ei
{
	protected $clase = 'toba_ei_formulario';
	protected $efs;
	protected $proximo_ef = 0;
	
	function ini()
	{
		parent::ini();
		$this->datos->tabla('prop_basicas')->nueva_fila(array());
		$this->datos->tabla('prop_basicas')->set_cursor(0);
	}
	
	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function set_comportamiento_filtro()
	{
		$datos = array();
		$datos['no_imprimir_efs_sin_estado'] = 1;
		$datos['resaltar_efs_con_estado'] = 1;
		$this->datos->tabla('prop_basicas')->set($datos);
	}
	
	function agregar_ef($identificador, $tipo)
	{
		$this->efs[$identificador] = new toba_molde_ef($identificador, $tipo);
		$this->efs[$identificador]->set_orden($this->proximo_ef);
		$this->proximo_ef++;
		return $this->efs[$identificador];
	}

	function ef($identificador)
	{
		if(!isset($this->efs[$identificador])) {
			throw new toba_error_asistentes('Molde formulario: El ef solicitado no existe');	
		}
		return $this->efs[$identificador];
	}

	//---------------------------------------------------
	//-- Generacion de METADATOS & ARCHIVOS
	//---------------------------------------------------
	
	function generar()
	{
		foreach($this->efs as $ef) {
			//-- Se generan los datos_tabla necesarios para la carga de los combos			
			if ($ef->tiene_carga_datos_tabla()) {
				$ef->generar_datos_tabla_carga();
			}
		 	$this->datos->tabla('efs')->nueva_fila($ef->get_datos());
		}
		parent::generar();
	}

}
?>