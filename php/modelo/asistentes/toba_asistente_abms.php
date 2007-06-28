<?php

class toba_asistente_abms extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->_info, $this->_info_abms, $this->_info_abms_fila));
		$this->ci()->agregar_dep('toba_ei_formulario', 'formulario');
		$this->ci()->agregar_dep('toba_ei_cuadro', 'cuadro');
		$this->ci()->agregar_dep('toba_ei_datos_tabla', 'datos');
		$this->generar_formulario();
		$this->generar_cuadro();
		$this->generar_datos_tabla();
			
	}
	
	function generar_formulario()
	{
		
	}
	
	function generar_cuadro()
	{
		
	}
	
	function generar_datos_tabla()
	{
		
	}
	
}

?>