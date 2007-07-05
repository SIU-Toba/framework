<?php

class toba_asistente_grilla extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->plan, $this->plan_abms, $this->plan_abms_fila));
		$this->item->cargar_grupos_acceso_activos();
		$this->ci->agregar_pantalla('pantalla_1', 'Pantalla UNO');
		$this->ci->extender('ci','ci.php');
		$this->ci->agregar_dep('toba_ei_formulario_ml', 'formulario', 'pantalla_1');
		$this->generar_formulario_ml($this->ci->dep('formulario'));
		//$this->ci->agregar_dep('toba_datos_tabla', 'tabla');
		//$this->generar_tabla($this->ci->dep('formulario'));
	}
	
	function generar_formulario_ml($form)
	{
		$form->set_nombre($this->plan['nombre'] . ' - Form');
		foreach( $this->plan_abms_fila as $fila ) {
			$form->agregar_ef($fila['columna'], $fila['elemento_formulario'], $fila['etiqueta']);
		}
	}
	
	function generar_datos_tabla($tabla)
	{
		$form->set_nombre($this->plan['nombre'] . ' - Form.');

	}
}
?>