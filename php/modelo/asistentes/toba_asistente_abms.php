<?php

class toba_asistente_abms extends toba_asistente
{
	protected $confirmacion_eliminar = '¿Desea eliminar el registro?';
	protected $mensaje_filtro_incompleto = 'El filtro no posee valores';

	protected function generar()
	{	
		$clase = $this->molde['prefijo_clases'] . 'ci';
		$this->ci->extender($clase , $clase . '.php');
		$this->ci->set_ancho('500px');
		$this->ci->set_alto('300px');
		//- Creo dependencias -----------------------------------
		$tabla = $this->ci->agregar_dep('toba_datos_tabla', 'datos');
		$cuadro = $this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$form = $this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$this->generar_datos_tabla($tabla, $this->molde_abms['tabla'], $this->molde_abms_fila);
		if ($this->molde_abms['gen_usa_filtro']) {
			$filtro = $this->ci->agregar_dep('toba_ei_filtro', 'filtro');			
			$this->generar_filtro($filtro);
		}
		$this->generar_cuadro($cuadro);
		$this->generar_formulario($form);
		//- Pantallas --------------------------------------------
		if (!$this->molde_abms['gen_separar_pantallas']) {
			//Pantalla UNICA
			$this->ci->agregar_pantalla(1, 'Pantalla');
			$this->ci->asociar_pantalla_dep(1, $cuadro);
			$this->ci->asociar_pantalla_dep(1, $form);
			if ($this->molde_abms['gen_usa_filtro']) {
				$this->ci->asociar_pantalla_dep(1, $filtro);
			}
		} else {
			//Pantallas SELECCION & EDICION
			$this->ci->agregar_pantalla('seleccion', 'Selección');
			$this->ci->agregar_pantalla('edicion', 'Edición');
			if ($this->molde_abms['gen_usa_filtro']) {
				$this->ci->asociar_pantalla_dep('seleccion', $filtro);
			}
			$this->ci->asociar_pantalla_dep('seleccion', $cuadro);
			$this->ci->asociar_pantalla_dep('edicion', $form);
			//----- evt__agregar
			$this->ci->php()->agregar( new toba_codigo_separador_php('EVENTOS CI') );	
			$evento = $this->ci->agregar_evento('agregar');
			$evento->en_botonera();
			$evento->set_etiqueta('Agregar');
			$evento->set_imagen('nucleo/agregar.gif');
			$metodo = new toba_codigo_metodo_php('evt__agregar');
			$metodo->set_contenido("\$this->set_pantalla('edicion');");
			$this->ci->php()->agregar($metodo);
			$this->ci->asociar_pantalla_evento('seleccion', $evento);
		}
	}

	#############################################################################
	################################   FILTRO   #################################
	#############################################################################
	
	protected function generar_filtro($filtro)
	{
		$filtro->set_nombre($this->molde['nombre'] . ' - Filtro');
		//Creo las filas
		$filas = array();
		foreach( $this->molde_abms_fila as $fila ) {
			if($fila['en_filtro']) {
				$filas[] = $fila;
			}
		}
		if(count($filas)==0) {
			throw new toba_error('ASISTENTE ABMS: Se especifico un filtro pero no se definio que filas participan del mismo');	
		}
		$this->generar_efs($filtro, $filas);
		$this->ci->php()->agregar( new toba_codigo_separador_php('Filtro') );	
		//--------------------------------------------------------
		//Varible que maneja los datos
		$propiedad = new toba_codigo_propiedad_php('$s__datos_filtro','protected');
		$this->ci->php()->agregar($propiedad);
		//--------------------------------------------------------
		//--- conf__filtro ---------------------------------------
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('conf__filtro',array('$filtro'));
		$metodo->set_contenido(array(	"if(isset(\$this->s__datos_filtro)){",
										"\t\$filtro->set_datos(\$this->s__datos_filtro);",
										"}"));
		$this->ci->php()->agregar($metodo);		
		//--------------------------------------------------------
		//--- evt__filtro__filtrar -------------------------------
		//--------------------------------------------------------
		$evento = $filtro->agregar_evento('filtrar');
		$evento->en_botonera();
		$evento->set_etiqueta('Filtrar');
		$evento->set_imagen('filtrar.png');
		$evento->maneja_datos();
		$metodo = new toba_codigo_metodo_php('evt__filtro__filtrar',array('$datos'));
		$asignacion = "\$this->s__datos_filtro = \$datos;";
		if($this->molde_abms['filtro_comprobar_parametros']) {
			//Solo guarda el filtro si existe una variable seteada
			$metodo->set_contenido(array(	"if (array_no_nulo(\$datos)) {",
											"\t$asignacion",
											"} else { ",
											"\ttoba::notificacion()->agregar('$this->mensaje_filtro_incompleto');",
											"}"));
		}else{
			$metodo->set_contenido($asignacion);
		}
		$this->ci->php()->agregar($metodo);		
		//--------------------------------------------------------
		//--- evt__filtro__cancelar ------------------------------
		//--------------------------------------------------------
		$evento = $filtro->agregar_evento('cancelar');
		$evento->en_botonera();
		$evento->set_etiqueta('Cancelar');
		$evento->set_imagen('deshacer.png');
		$metodo = new toba_codigo_metodo_php('evt__filtro__cancelar');
		$metodo->set_contenido("unset(\$this->s__datos_filtro);");
		$this->ci->php()->agregar($metodo);		
	}

	#############################################################################
	################################   CUADRO   #################################
	#############################################################################

	protected function generar_cuadro($cuadro)
	{
		//Cabecera
		$cuadro->set_clave($this->molde_abms['cuadro_id']);
		$this->ci->dep('cuadro')->set_nombre($this->molde['nombre'] . ' - Cuadro.');
		if ($this->molde_abms['cuadro_eof']) {
			$cuadro->set_eof($this->molde_abms['cuadro_eof']);
		}
		$cuadro->set_ancho('100%');
		if (!$this->molde_abms['gen_separar_pantallas']) {
			//Si todo es en la misma pantalla le pongo scroll al cuadro
			$cuadro->set_scroll('250px');
		}
		//Construyo las filas
		foreach( $this->molde_abms_fila as $fila ) {
			if($fila['en_cuadro']) {
				$columna = $cuadro->agregar_columna($fila['columna'], 4);
			}
		}
		$this->ci->php()->agregar( new toba_codigo_separador_php('Cuadro') );	
		//--------------------------------------------------------
		//--- conf__cuadro  --------------------------------------
		//--------------------------------------------------------
		if(isset($this->molde_abms['cuadro_carga_php_metodo'])&&$this->molde_abms['cuadro_carga_php_metodo']) {
			$metodo = new toba_codigo_metodo_php('conf__cuadro',array('$cuadro'));
			//Los datos son provistos por un archivo de consultas php
			if($this->molde_abms['cuadro_carga_origen'] == 'consulta_php' ) {
				$php_recuperacion = $this->molde_abms['cuadro_carga_php_clase'] . '::' . $this->molde_abms['cuadro_carga_php_metodo'];
				if(isset($this->molde_abms['cuadro_carga_sql'])){ // La consulta no existes
					$this->ci->php()->agregar_archivo_requerido($this->molde_abms['cuadro_carga_php_include']);
					$this->crear_consulta_php(	$this->molde_abms['cuadro_carga_php_include'],
												$this->molde_abms['cuadro_carga_php_clase'],
												$this->molde_abms['cuadro_carga_php_metodo'],
												$this->molde_abms['cuadro_carga_sql'] );
				}
			//Los datos son provistos por un datos_tabla
			} elseif ($this->molde_abms['cuadro_carga_origen'] == 'datos_tabla' ) {
				$php_recuperacion = '$this->dep(\'datos\')->' . $this->molde_abms['cuadro_carga_php_metodo'];
				if(isset($this->molde_abms['cuadro_carga_sql'])){ // La consulta no existes
					$this->crear_consulta_dt(	$this->ci->dep('datos'),
												$this->molde_abms['cuadro_carga_php_metodo'],
												$this->molde_abms['cuadro_carga_sql'] );
				}				
			} else {
				throw new toba_error('El tipo de origen de datos no fue definido correctamente [' . $this->molde_abms['cuadro_carga_php_clase'] . ']');	
			}
			//-- SI la operacion tiene FILTRO....
			if ($this->molde_abms['gen_usa_filtro']) {
				$php = array();
				$php[] = "if(isset(\$this->s__datos_filtro)){";
				$php[] = "\t\$cuadro->set_datos(".$php_recuperacion."(\$this->s__datos_filtro));";
				if($this->molde_abms['cuadro_forzar_filtro']) {
					// El cuadro solo se carga si el filtro esta seteado
					$php[] = "}";
				} else {
					$php[] = "} else {";
					$php[] = "\t\$cuadro->set_datos($php_recuperacion());";
					$php[] = "}";
				}
			}else{
				$php[] = "\$cuadro->set_datos($php_recuperacion());";
			}
			$metodo->set_contenido($php);
			$this->ci->php()->agregar($metodo);		
		}		
		//--------------------------------------------------------
		//--- evt__cuadro__seleccion -----------------------------
		//--------------------------------------------------------
		$evento = $cuadro->agregar_evento('seleccion');
		$evento->sobre_fila();
		$evento->set_imagen('doc.gif');
		$metodo = new toba_codigo_metodo_php('evt__cuadro__seleccion',array('$datos'));
		$php = array("\$this->dep('datos')->cargar(\$datos);");
		if ($this->molde_abms['gen_separar_pantallas']) {
			$php[] = "\$this->set_pantalla('edicion');";
		}
		$metodo->set_contenido($php);
		$this->ci->php()->agregar($metodo);		
		//--------------------------------------------------------
		//--- evt__cuadro__elimimar ------------------------------
		//--------------------------------------------------------
		if ($this->molde_abms['cuadro_eliminar_filas']) {
			$evento = $cuadro->agregar_evento('eliminar');
			$evento->sobre_fila();
			$evento->set_imagen('borrar.gif');
			$evento->set_confirmacion($this->confirmacion_eliminar);
			$metodo = new toba_codigo_metodo_php('evt__cuadro__eliminar',array('$datos'));
			$metodo->set_contenido( array(	"\$this->dep('datos')->resetear();",
											"\$this->dep('datos')->cargar(\$datos);",
											"\$this->dep('datos')->eliminar_filas();",
											"\$this->dep('datos')->sincronizar();",
											"\$this->dep('datos')->resetear();"));
			$this->ci->php()->agregar($metodo);
		}
	}

	#############################################################################
	################################   FORMULARIO   #############################
	#############################################################################

	protected function generar_formulario($form)
	{
		$form->set_nombre($this->molde['nombre'] . ' - Form');
		//Creo las filas
		$filas = array();
		foreach( $this->molde_abms_fila as $fila ) {
			if($fila['en_form']) {
				$filas[] = $fila;
			}
		}
		$this->generar_efs($form, $filas);
		$this->ci->php()->agregar( new toba_codigo_separador_php('Formulario') );	
		//--------------------------------------------------------
		//--- conf__formulario  ----------------------------------
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('conf__formulario',array('$form'));
		$metodo->set_contenido(array(	"if(\$this->dep('datos')->esta_cargada()){",
										"\t\$form->set_datos(\$this->dep('datos')->get());",
										"}"));
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- evt__formulario__alta ------------------------------
		//--------------------------------------------------------
		$evento = $form->agregar_evento('alta');
		$evento->en_botonera();
		$evento->set_etiqueta('Alta');
		$evento->set_imagen('nucleo/agregar.gif');
		$evento->maneja_datos();
		$evento->set_grupos('no_cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__alta',array('$datos'));
		$metodo->set_contenido( array(	"\$this->dep('datos')->nueva_fila(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->resetear();"));
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- evt__formulario__baja ------------------------------
		//--------------------------------------------------------
		$evento = $form->agregar_evento('baja');
		$evento->set_etiqueta('Eliminar');
		$evento->en_botonera();
		$evento->set_imagen('borrar.gif');
		$evento->set_estilo('ei-boton-baja');
		$evento->set_confirmacion($this->confirmacion_eliminar);
		$evento->set_grupos('cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__baja');
		$metodo->set_contenido( array(	"\$this->dep('datos')->eliminar_filas();",
										"\$this->dep('datos')->sincronizar();",
										"\$this->resetear();"));
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- evt__formulario__modificacion ----------------------
		//--------------------------------------------------------
		$evento = $form->agregar_evento('modificacion');
		$evento->set_etiqueta('Modificar');
		$evento->en_botonera();
		$evento->maneja_datos();
		$evento->set_imagen('refrescar.png');
		$evento->set_grupos('cargado');
		$metodo = new toba_codigo_metodo_php('evt__formulario__modificacion',array('$datos'));
		$metodo->set_contenido( array(	"\$this->dep('datos')->set(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->resetear();"));
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- evt__formulario__cancelar --------------------------
		//--------------------------------------------------------
		$evento = $form->agregar_evento('cancelar');
		$evento->set_etiqueta('Cancelar');
		$evento->en_botonera();
		$evento->set_imagen('deshacer.png');
		if ($this->molde_abms['gen_separar_pantallas']) {
			$evento->set_grupos(array('cargado','no_cargado'));
		} else {
			$evento->set_grupos('cargado');
		}
		$metodo = new toba_codigo_metodo_php('evt__formulario__cancelar');
		$metodo->set_contenido( array("\$this->resetear();"));
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- Metodo para resetear la operacion
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('resetear');
		$php[] = "\$this->dep('datos')->resetear();";
		if ($this->molde_abms['gen_separar_pantallas']) {
			$php[] = "\$this->set_pantalla('seleccion');";
		}
		$metodo->set_contenido($php);
		$this->ci->php()->agregar($metodo);
	}
}
?>