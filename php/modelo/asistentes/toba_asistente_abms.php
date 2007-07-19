<?php

class toba_asistente_abms extends toba_asistente
{
	function generar()
	{	
		//ei_arbol(array($this->molde, $this->molde_abms, $this->molde_abms_fila));
		$this->ci->set_titulo($this->molde['nombre']);
		$this->ci->agregar_pantalla(1, 'Pantalla');
		$clase = $this->molde['prefijo_clases'] . 'ci';
		$this->ci->extender($clase , $clase . '.php');
		//- Creo dependencias -----------------------------------
		$cuadro = $this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$this->ci->asociar_pantalla_dep(1, $cuadro);
		$this->generar_cuadro($cuadro);
		$form = $this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$this->ci->asociar_pantalla_dep(1, $form);
		$this->generar_formulario($form);
		$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'datos');
		$this->generar_datos_tabla($tabla, $this->molde_abms['tabla'], $this->molde_abms_fila);
	}
	
	function generar_formulario($form)
	{
		$form->set_nombre($this->molde['nombre'] . ' - Form');
		$this->generar_efs($form, $this->molde_abms_fila);
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
		//--- conf ---------------------------
		if($this->molde['cuadro_carga_php_metodo']) {
			$this->ci->php()->agregar_archivo_requerido($this->molde['cuadro_carga_php_include']);
			$metodo = new toba_codigo_metodo_php('conf__cuadro',array('$componente'));
			$origen = $this->molde['cuadro_carga_php_clase'] . '::' . $this->molde['cuadro_carga_php_metodo'] . '()';
			$metodo->set_contenido("\$componente->set_datos($origen);");
			$this->ci->php()->agregar($metodo);		
			if(isset($this->molde['cuadro_carga_sql'])){
				$this->crear_consulta_php(	$this->molde['cuadro_carga_php_include'],
											$this->molde['cuadro_carga_php_clase'],
											$this->molde['cuadro_carga_php_metodo'],
											$this->molde['cuadro_carga_sql'] );
			}
		}		
	}
}
?>