<?php

class toba_ei_formulario_ml_molde extends toba_ei_formulario_molde
{
	protected $clase = 'toba_ei_formulario_ml';

	//---------------------------------------------------
	//-- API de construccion
	//---------------------------------------------------	

	function agregar_filas($estado=true)
	{
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'filas_agregar',$estado);
	}

	function agregar_filas_js($tipo=1)
	{
		$tipos_validos = array(0,1);			//1=javascript
		if(!in_array($tipo, $tipos_validos)) {
			throw new toba_error_asistentes('MOLDE ML: El tipo de agregado de filas especificado no es valido');
		}
		$this->agregar_filas();
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'filas_agregar_online',$tipo);
	}	

	function set_analisis_cambios($tipo)
	{
		$tipos_validos = array('NO','LINEA','EVENTOS');
		if(!in_array($tipo, $tipos_validos)) {
			throw new toba_error_asistentes('MOLDE ML: El tipo de analisis especificado no es valido');	
		}
		$this->datos->tabla('prop_basicas')->set_fila_columna_valor(0,'analisis_cambios',$tipo);
	}
}
?>