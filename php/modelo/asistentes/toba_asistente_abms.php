<?php

class toba_asistente_abms extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->_info, $this->_info_abms, $this->_info_abms_fila));
		$this->generar_formulario();
		$this->generar_cuadro();
		$this->generar_datos_tabla();
	}
	
	function generar_formulario()
	{
		$this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$this->ci->dep('formulario')->set_nombre($this->_info['nombre'] . ' - Form.');		
	}
	
	function generar_cuadro()
	{
		$this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$this->ci->dep('cuadro')->set_nombre($this->_info['nombre'] . ' - Cuadro.');
	}
	
	function generar_datos_tabla()
	{
		$this->ci->agregar_dep('toba_datos_tabla', 'datos');
		
	}
	
}
?>