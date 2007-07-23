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
		$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'datos');
		$this->generar_datos_tabla($tabla, $this->molde_abms['tabla'], $this->molde_abms_fila);
		$cuadro = $this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$this->ci->asociar_pantalla_dep(1, $cuadro);
		$this->generar_cuadro($cuadro);
		$form = $this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$this->ci->asociar_pantalla_dep(1, $form);
		$this->generar_formulario($form);
	}


	function generar_cuadro($cuadro)
	{
		$cuadro->set_clave($this->molde_abms['cuadro_id']);
		$this->ci->dep('cuadro')->set_nombre($this->molde['nombre'] . ' - Cuadro.');
		foreach( $this->molde_abms_fila as $fila ) {
			$columna = $cuadro->agregar_columna($fila['columna'], 4);
		}
		$this->ci->php()->agregar( new toba_codigo_separador_php('Cuadro') );	
		//--- Configuracion del cuadro ---------------------------
		if(isset($this->molde_abms['cuadro_carga_php_metodo'])&&$this->molde_abms['cuadro_carga_php_metodo']) {
			$metodo = new toba_codigo_metodo_php('conf__cuadro',array('$componente'));
			//Los datos son provistos por un archivo de consultas php
			if($this->molde_abms['cuadro_carga_origen'] == 'consulta_php' ) {
				$php_recuperacion = $this->molde_abms['cuadro_carga_php_clase'] . '::' . $this->molde_abms['cuadro_carga_php_metodo'] . '()';
				if(isset($this->molde_abms['cuadro_carga_sql'])){ // La consulta no existes
					$this->ci->php()->agregar_archivo_requerido($this->molde_abms['cuadro_carga_php_include']);
					$this->crear_consulta_php(	$this->molde_abms['cuadro_carga_php_include'],
												$this->molde_abms['cuadro_carga_php_clase'],
												$this->molde_abms['cuadro_carga_php_metodo'],
												$this->molde_abms['cuadro_carga_sql'] );
				}
			//Los datos son provistos por un datos_tabla
			} elseif ($this->molde_abms['cuadro_carga_origen'] == 'datos_tabla' ) {
				$php_recuperacion = '$this->dep(\'datos\')->' . $this->molde_abms['cuadro_carga_php_metodo'] . '()';
				if(isset($this->molde_abms['cuadro_carga_sql'])){ // La consulta no existes
					$this->crear_consulta_dt(	$this->ci->dep('datos'),
												$this->molde_abms['cuadro_carga_php_metodo'],
												$this->molde_abms['cuadro_carga_sql'] );
				}				
			} else {
				throw new toba_error('El tipo de origen de datos no fue definido correctamente [' . $this->molde_abms['cuadro_carga_php_clase'] . ']');	
			}
			$metodo->set_contenido("\$componente->set_datos($php_recuperacion);");
			$this->ci->php()->agregar($metodo);		
		}		
		//--- Evento SELECCION -------------------------
		$evento = $cuadro->agregar_evento('seleccion');
		$evento->sobre_fila();
		$evento->set_imagen('doc.gif');
		$metodo = new toba_codigo_metodo_php('evt__cuadro__seleccion',array('$datos'));
		$metodo->set_contenido("\$this->dep('datos')->cargar(\$datos);");
		$this->ci->php()->agregar($metodo);		
	}

	
	function generar_formulario($form)
	{
		$form->set_nombre($this->molde['nombre'] . ' - Form');
		$this->generar_efs($form, $this->molde_abms_fila);
		$this->ci->php()->agregar( new toba_codigo_separador_php('Formulario') );	
		//- CONF -----------
		$metodo = new toba_codigo_metodo_php('conf__formulario',array('$componente'));
		$metodo->set_contenido(array(	"if(\$this->dep('datos')->esta_cargada()){",
										"\t\$componente->set_datos(\$this->dep('datos')->get());",
										"}"));
		$this->ci->php()->agregar($metodo);
		//- Evento ALTA ----
		$evento = $form->agregar_evento('alta');
		$evento->en_botonera();
		$evento->set_etiqueta('Alta');
		$evento->maneja_datos();
		$evento->set_grupos('no_cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__alta',array('$datos'));
		$metodo->set_contenido( array(	"\$this->dep('datos')->nueva_fila(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->dep('datos')->resetear();"));
		$this->ci->php()->agregar($metodo);
		//- Evento BAJA ----
		$evento = $form->agregar_evento('baja');
		$evento->set_etiqueta('Eliminar');
		$evento->en_botonera();
		$evento->set_grupos('cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__baja');
		$metodo->set_contenido( array(	"\$this->dep('datos')->eliminar_filas();",
										"\$this->dep('datos')->sincronizar();",
										"\$this->dep('datos')->resetear();"));
		$this->ci->php()->agregar($metodo);
		//- Evento MODIFICACION ----
		$evento = $form->agregar_evento('modificacion');
		$evento->set_etiqueta('Modificar');
		$evento->en_botonera();
		$evento->maneja_datos();
		$evento->set_grupos('cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__modificacion',array('$datos'));
		$metodo->set_contenido( array(	"\$this->dep('datos')->set(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->dep('datos')->resetear();"));
		$this->ci->php()->agregar($metodo);
		//- Evento CANCELAR ----
		$evento = $form->agregar_evento('cancelar');
		$evento->set_etiqueta('Cancelar');
		$evento->en_botonera();
		$evento->set_grupos('cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__cancelar');
		$metodo->set_contenido( array("\$this->dep('datos')->resetear();"));
		$this->ci->php()->agregar($metodo);
	}
}
?>