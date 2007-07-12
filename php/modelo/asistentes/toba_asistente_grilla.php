<?php

class toba_asistente_grilla extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->plan, $this->plan_abms, $this->plan_abms_fila));
		$this->ci->set_titulo($this->plan['nombre']);
		$this->ci->agregar_pantalla(1, 'Pantalla');
		$form = $this->ci->agregar_dep('toba_ei_formulario_ml', 'formulario');
		$this->ci->asociar_pantalla_dep(1, $form);
		$this->ci->extender('ci','ci.php');
		$this->generar_formulario_ml($form);
		//$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'tabla');
		//$this->generar_tabla($tabla);
		$evento = $this->ci->agregar_evento('guardar');
		$evento->maneja_datos();
		$evento->en_botonera();
		$evento->set_imagen('guardar.gif');
		$this->ci->asociar_pantalla_evento(1, $evento);
	}
	
	function generar_formulario_ml($form)
	{
		$form->set_nombre($this->plan['nombre'] . ' - Form');
		$form->set_analisis_cambios('LINEA');
		$form->agregar_filas_js();
		foreach( $this->plan_abms_fila as $fila ) {
			$form->agregar_ef($fila['columna'], $fila['elemento_formulario']);
		}
		$evento = $form->agregar_evento('modificacion');
		$evento->maneja_datos();
		$evento->implicito();
	}
	
	function generar_datos_tabla($tabla)
	{
		$form->set_nombre($this->plan['nombre'] . ' - Form.');
	}
}
?>