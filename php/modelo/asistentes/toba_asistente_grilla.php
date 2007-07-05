<?php

class toba_asistente_grilla extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->plan, $this->plan_abms, $this->plan_abms_fila));
		$this->item->cargar_grupos_acceso_activos();
		$this->ci->agregar_pantalla('pantalla_1', 'Pantalla UNO');
		$this->ci->extender('ci','ci.php');
		$this->ci->agregar_dep('toba_ei_formulario', 'formulario', 'pantalla_1');
		$this->generar_formulario($this->ci->dep('formulario'));
		$this->ci->agregar_dep('toba_ei_cuadro', 'cuadro', 'pantalla_1');
		$this->generar_cuadro($this->ci->dep('cuadro'));
		//$this->generar_datos_tabla();
	}
	
	function generar_formulario($form)
	{
		$form->set_nombre($this->plan['nombre'] . ' - Form.');
		foreach( $this->plan_abms_fila as $fila ) {
			$form->agregar_ef('pepe','ef_editable');
		}
		//ALTA
		$form->agregar_evento('alta');
		$this->ci->php()->agregar( new toba_codigo_metodo_php('ini') );
	}
	
	function generar_cuadro($cuadro)
	{
		$cuadro->set_clave('id');
		$this->ci->dep('cuadro')->set_nombre($this->plan['nombre'] . ' - Cuadro.');
		foreach( $this->plan_abms_fila as $fila ) {
			$cuadro->agregar_columna('pepe');
		}
		$cuadro->agregar_evento('test');
	}
	
	function generar_datos_tabla()
	{
		$this->ci->agregar_dep('toba_datos_tabla', 'datos');
	}
}
?>