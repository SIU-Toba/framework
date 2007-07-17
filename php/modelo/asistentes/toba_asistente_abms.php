<?php

class toba_asistente_abms extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->molde, $this->molde_abms, $this->molde_abms_fila));
		$this->ci->set_titulo($this->molde['nombre']);
		$this->ci->agregar_pantalla(1, 'Pantalla');
		$this->ci->extender('ci','ci.php');
		//- Creo dependencias -----------------------------------
		$cuadro = $this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$this->ci->asociar_pantalla_dep(1, $cuadro);
		$this->generar_cuadro($cuadro);
		$form = $this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$this->ci->asociar_pantalla_dep(1, $form);
		$this->generar_formulario($form);
		$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'datos');
		$this->generar_datos_tabla($tabla);
	}
	
	function generar_formulario($form)
	{
		$form->set_nombre($this->molde['nombre'] . ' - Form');
		foreach( $this->molde_abms_fila as $fila ) {
			$ef = $form->agregar_ef($fila['columna'], $fila['elemento_formulario']);
			if($fila['dt_largo']){
				$ef->set_propiedad('edit_tamano',$fila['dt_largo']);
				$ef->set_propiedad('edit_maximo',$fila['dt_largo']);
			}
		}
		//- Evento ALTA ----
		$evento = $form->agregar_evento('alta');
		$evento->en_botonera();
		$evento->maneja_datos();
		$metodo = new toba_codigo_metodo_php('evt__formulario__alta',array('$datos'));
		$metodo->set_contenido( array(	"\$this->dep('datos')->nueva_fila(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->dep('datos')->resetear();"));
		$this->ci->php()->agregar($metodo);
	}
	
	function generar_cuadro($cuadro)
	{
		$cuadro->set_clave('id');
		$this->ci->dep('cuadro')->set_nombre($this->molde['nombre'] . ' - Cuadro.');
		foreach( $this->molde_abms_fila as $fila ) {
			$columna = $cuadro->agregar_columna($fila['columna'], 4);
		}
		$evento = $cuadro->agregar_evento('seleccion');
		$evento->sobre_fila();
		$evento->set_imagen('doc.gif');
	}
	
	function generar_datos_tabla($tabla)
	{
		$tabla->set_tabla($this->molde_abms['tabla']);
		foreach( $this->molde_abms_fila as $fila ) {
			$col = $tabla->agregar_columna($fila['columna'], $fila['dt_tipo_dato']);
			if($fila['dt_pk']){
				$col->pk();
			}
			if($fila['dt_secuencia']){
				$col->set_secuencia($fila['dt_secuencia']);
			}
		}
	}
}
?>