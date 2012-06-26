<?php

class toba_asistente_abms extends toba_asistente_1dt
{
	protected $confirmacion_eliminar = '¿Desea eliminar el registro?';
	protected $mensaje_filtro_incompleto = 'El filtro no posee valores';
	
	#####################################################################################
	################################   Autocompletado   #################################
	#####################################################################################
	
	function posee_informacion_completa()
	{
		$mensajes = array();
		if( parent::posee_informacion_completa() ) {
			$base = $this->dr_molde->tabla('base')->get();	
			if( !isset($base['tabla'])) {
				return false;	
			}
			if(!isset($base['cuadro_carga_origen'])) {
				$mensajes[] = 'Formulario: Falta indicar el origen de la carga del cuadro';
			}			
			$filas = $this->dr_molde->tabla('filas')->get_filas();	
			foreach($filas as $fila) {
				if( $fila['asistente_tipo_dato']== toba_catalogo_asistentes::tipo_dato_referencia()) {
					if(!isset($fila['ef_carga_origen'])) {
						$mensajes[] = 'Formulario: Falta indicar el origen de la carga del campo "'.$fila['columna'].'"';
					}
				}
			}
		}
		return empty($mensajes) ? true : $mensajes;
	}
	
	function autocompletar_informacion($refrescar_todo=false)
	{
		parent::autocompletar_informacion($refrescar_todo=false);
		$this->autocompletar_carga_cuadro();
	}

	function autocompletar_carga_cuadro()
	{
		$nombre_tabla = $this->dr_molde->tabla('base')->get_columna('tabla');
		$nombre_fuente = $this->dr_molde->tabla('base')->get_columna('fuente');
		//Si el nombre de la fuente no esta en el DR, trato de obtenerlo del asistente.
		if (isset($this->molde) && is_null($nombre_fuente)) {
			$nombre_fuente = $this->get_fuente();
		}
		$db = toba::db($nombre_fuente, toba_editor::get_proyecto_cargado());
		$datos = array();
		list($sql, $id) = $db->get_sql_carga_tabla($nombre_tabla);
		$datos['cuadro_carga_sql'] = $sql;
		$datos['cuadro_id'] = $id;
		$datos['cuadro_carga_origen'] = 'datos_tabla';
		$this->dr_molde->tabla('base')->set($datos);		
	}
	
	################################################################################
	################################  GENERACION   #################################
	################################################################################	
	
	protected function generar()
	{
		$pm = $this->dr_molde->tabla('molde')->get_fila_columna(0, 'punto_montaje');
		
		$clase = 'ci'.$this->molde['prefijo_clases'];
		$this->ci->set_nombre($this->molde['nombre'] . ' - CI');
		$this->ci->set_punto_montaje($pm);
		$this->ci->extender($clase , $clase . '.php');
		$this->ci->set_ancho('500px');
		$this->ci->set_alto('300px');
		//- Creo dependencias -----------------------------------
		$relacion = $this->ci->agregar_dep('toba_datos_relacion', 'datos');
		$relacion->set_punto_montaje($pm);		
		$relacion->agregar_tabla($this->molde_abms['tabla']);
		$relacion->agregar_definicion_tabla($this->molde_abms['tabla'], $this->molde_abms_fila);
		
		$cuadro = $this->ci->agregar_dep('toba_ei_cuadro', 'cuadro');
		$cuadro->set_punto_montaje($pm);
		
		$form = $this->ci->agregar_dep('toba_ei_formulario', 'formulario');
		$form->set_punto_montaje($pm);
		
		$this->generar_datos_relacion($relacion);		

		if ($this->molde_abms['gen_usa_filtro']) {
			$filtro = $this->ci->agregar_dep('toba_ei_formulario', 'filtro');			
			$filtro->set_punto_montaje($pm);
			$this->generar_filtro($filtro);
		}
		$this->generar_cuadro($cuadro);
		$this->generar_formulario($form);
		//- Pantallas --------------------------------------------
		if (!$this->molde_abms['gen_separar_pantallas']) {
			//Pantalla UNICA
			$this->ci->agregar_pantalla('pant_edicion', 'Pantalla');
			if ($this->molde_abms['gen_usa_filtro']) {
				$this->ci->asociar_pantalla_dep('pant_edicion', $filtro);
			}
			
			$this->ci->asociar_pantalla_dep('pant_edicion', $cuadro);
			$this->ci->asociar_pantalla_dep('pant_edicion', $form);
		} else {
			//Pantallas SELECCION & EDICION
			$this->ci->agregar_pantalla('pant_seleccion', 'Selección');
			$this->ci->agregar_pantalla('pant_edicion', 'Edición');
			if ($this->molde_abms['gen_usa_filtro']) {
				$this->ci->asociar_pantalla_dep('pant_seleccion', $filtro);
			}
			$this->ci->asociar_pantalla_dep('pant_seleccion', $cuadro);
			$this->ci->asociar_pantalla_dep('pant_edicion', $form);
			//----- evt__agregar
			$this->ci->php()->agregar( new toba_codigo_separador_php('EVENTOS CI') );	
			$evento = $this->ci->agregar_evento('agregar');
			$evento->en_botonera();
			if (! $this->molde_abms['gen_usa_filtro']) {
				$evento->set_predeterminado();
			}
			$evento->set_etiqueta('Agregar');
			$evento->set_imagen('nucleo/agregar.gif');
			$metodo = new toba_codigo_metodo_php('evt__agregar');
			$metodo->set_contenido("\$this->set_pantalla('pant_edicion');");
			$this->ci->php()->agregar($metodo);
			$this->ci->asociar_pantalla_evento('pant_seleccion', $evento);

			//----- evt__volver
			$evento = $this->ci->agregar_evento('volver');
			$evento->en_botonera();
			$evento->set_etiqueta('Volver');
			$evento->set_imagen('deshacer.png');
			$evento->set_estilo('ei-boton-izq');
			$metodo = new toba_codigo_metodo_php('evt__volver');
			$metodo->set_contenido("\$this->resetear();");
			$this->ci->php()->agregar($metodo);
			$this->ci->asociar_pantalla_evento('pant_edicion', $evento);		

			//----- evt__eliminar
			$evento = $this->ci->agregar_evento('eliminar');
			$evento->en_botonera();
			$evento->set_etiqueta('Eliminar');
			$evento->set_imagen('borrar.png');
			$evento->set_confirmacion($this->confirmacion_eliminar);			
			$metodo = new toba_codigo_metodo_php('evt__eliminar');
			$metodo->set_contenido( array("\$this->dep('datos')->eliminar_todo();",
										"\$this->resetear();"));			
			$this->ci->php()->agregar($metodo);
			$this->ci->asociar_pantalla_evento('pant_edicion', $evento);	

			//----- evt__guardar
			$evento = $this->ci->agregar_evento('guardar');
			$evento->en_botonera();
			$evento->maneja_datos();
			$evento->set_etiqueta('Guardar');
			$evento->set_imagen('guardar.gif');
			$evento->set_predeterminado();
			$metodo = new toba_codigo_metodo_php('evt__guardar');
			$metodo->set_contenido( array("\$this->dep('datos')->sincronizar();",
										"\$this->resetear();"));			
			$this->ci->php()->agregar($metodo);
			$this->ci->asociar_pantalla_evento('pant_edicion', $evento);					
			
		}
	}

	#############################################################################
	################################   FILTRO   #################################
	#############################################################################
	
	protected function generar_filtro($filtro)
	{
		$filtro->set_comportamiento_filtro();
		$filtro->set_nombre($this->molde['nombre'] . ' - Filtro');
		//Creo las filas
		$filas = array();
		foreach( $this->molde_abms_fila as $fila ) {
			if($fila['en_filtro']) {
				$filas[] = $fila;
			}
		}
		if(count($filas)==0) {
			throw new toba_error_asistentes('ASISTENTE ABMS: Se especifico un filtro pero no se definio que filas participan del mismo');	
		}
		$this->generar_efs($filtro, $filas, true);
		$this->ci->php()->agregar( new toba_codigo_separador_php('Filtro') );	
		//--------------------------------------------------------
		//Varible que maneja los datos
		$propiedad = new toba_codigo_propiedad_php('$s__datos_filtro','protected');
		$this->ci->php()->agregar($propiedad);
		//--------------------------------------------------------
		//--- conf__filtro ---------------------------------------
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('conf__filtro',array('toba_ei_formulario $filtro'));
		$metodo->set_contenido(array(	"if (isset(\$this->s__datos_filtro)) {",
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
		if ($this->molde_abms['gen_separar_pantallas']) {
			$evento->set_predeterminado();
		}
		$evento->set_grupos(array('cargado','no_cargado'));
		$metodo = new toba_codigo_metodo_php('evt__filtro__filtrar',array('$datos'));
		$asignacion = "\$this->s__datos_filtro = \$datos;";
		if($this->molde_abms['filtro_comprobar_parametros']) {
			//Solo guarda el filtro si existe una variable seteada
			$metodo->set_contenido(array("if (array_no_nulo(\$datos)) {",
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
		$evento->set_etiqueta('Limpiar');
		$evento->set_imagen('limpiar.png');
		$evento->set_grupos('cargado');		
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
		$cuadro->set_nombre($this->molde['nombre'] . ' - Cuadro.');
		if (trim($this->molde_abms['cuadro_eof']) != '') {
			$cuadro->set_eof(trim($this->molde_abms['cuadro_eof']));
		} else {
			$cuadro->set_eof_invisible();
		}
		$cuadro->set_ancho('100%');
		if (!$this->molde_abms['gen_separar_pantallas']) {
			//Si todo es en la misma pantalla le pongo scroll al cuadro
			$cuadro->set_scroll('250px');
		}
		//Construyo las filas 
		$clave_dt = array();
		foreach($this->molde_abms_fila as $fila) {
			if ($fila['dt_pk'] == '1') {										//busco una posible clave para el cuadro
				$clave_dt[] = $fila['columna'];
			}
			if ($fila['en_cuadro']) {
				$columna = $fila['columna'];
				//-- Si es una FK la columna no esta en la tabla y requiere carga, por lo que es mejor no nombrarla como la clave ya que trae problemas de reuso en la SQL
				if ($fila['ef_carga_origen']) {
					$columna .= '_nombre';
				}
				$columna = $cuadro->agregar_columna($columna, 4);				
				$columna->set_etiqueta($fila['etiqueta']);
				$columna->set_estilo($fila['cuadro_estilo']);
				$columna->set_formato($fila['cuadro_formato']);
			}
		}
		if (! empty($clave_dt) && $this->molde_abms['cuadro_id'] == '') {			//Seteo la clave del cuadro
			$cuadro->set_clave($clave_dt);
		}else{
			$cuadro->set_clave($this->molde_abms['cuadro_id']);
		}		
		$this->ci->php()->agregar( new toba_codigo_separador_php('Cuadro') );	
		//--------------------------------------------------------
		//--- conf__cuadro  --------------------------------------
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('conf__cuadro',array('toba_ei_cuadro $cuadro'));
		//Si hay un filtro, armo los parametros
		if ($this->molde_abms['gen_usa_filtro']) {
			$filtro = array();
			foreach( $this->molde_abms_fila as $fila ) {
				if($fila['en_filtro']) {
					$filtro[$fila['columna']] = $fila['filtro_operador'];
				}
			}
		} else {
			$filtro = null;	
		}
		if($this->molde_abms['cuadro_carga_origen'] == 'consulta_php' ) {
			if( !$this->molde_abms['cuadro_carga_php_metodo'] ) {
				throw new toba_error_asistentes('ASISTENTE ABMS: El metodo de carga del cuadro no esta definido (MODO consulta_php).');	
			}
			if( !$this->molde_abms['cuadro_carga_php_clase'] ) {
				throw new toba_error_asistentes('ASISTENTE ABMS: La clase de carga del cuadro no esta definido (MODO consulta_php).');	
			}
			//----> Los datos son provistos por un archivo de consultas php
			$php_recuperacion = "toba::consulta_php('{$this->molde_abms['cuadro_carga_php_clase']}')->{$this->molde_abms['cuadro_carga_php_metodo']}";
			if(isset($this->molde_abms['cuadro_carga_sql'])) { // La consulta no existes
				//$this->ci->php()->agregar_archivo_requerido($this->molde_abms['cuadro_carga_php_include']);
				$this->crear_consulta_php($this->molde_abms['cuadro_carga_php_include'],
											$this->molde_abms['cuadro_carga_php_clase'],
											$this->molde_abms['cuadro_carga_php_metodo'],
											$this->molde_abms['cuadro_carga_sql'],
											$filtro );
			}
		} elseif ($this->molde_abms['cuadro_carga_origen'] == 'datos_tabla' ) {
			if(!$this->molde_abms['cuadro_carga_php_metodo']){
				$metodo_recuperacion = 'get_listado';
			}else{
				$metodo_recuperacion = $this->molde_abms['cuadro_carga_php_metodo'];
			}
			//----> Los datos son provistos por un datos_tabla
			$tabla_usada = $this->molde_abms['tabla'];
			$php_recuperacion = '$this->dep(\'datos\')->' . "tabla('$tabla_usada')->". $metodo_recuperacion;
			if(isset($this->molde_abms['cuadro_carga_sql'])){ // La consulta existe
				$this->ci->dep('datos')->crear_metodo_consulta($metodo_recuperacion,
																$this->molde_abms['cuadro_carga_sql'],
																$filtro );
			}
		} else {
			throw new toba_error_asistentes('El tipo de origen de datos no fue definido correctamente [' . $this->molde_abms['cuadro_carga_php_clase'] . ']');	
		}
		//-- SI la operacion tiene FILTRO....
		if ($this->molde_abms['gen_usa_filtro']) {
			$php = array();
			$php[] = "if (isset(\$this->s__datos_filtro)) {";
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
	
		//--------------------------------------------------------
		//--- evt__cuadro__elimimar ------------------------------
		//--------------------------------------------------------
		if ($this->molde_abms['cuadro_eliminar_filas']) {
			$evento = $cuadro->agregar_evento('eliminar');
			$evento->en_botonera(false);
			if (! empty($clave_dt)) {
					$evento->sobre_fila();
			}
			$evento->en_botonera(false);
			$evento->set_imagen('borrar.gif');
			$evento->set_confirmacion($this->confirmacion_eliminar);
			$metodo = new toba_codigo_metodo_php('evt__cuadro__eliminar',array('$datos'));
			$metodo->set_contenido( array(	"\$this->dep('datos')->resetear();",
											"\$this->dep('datos')->cargar(\$datos);",
											"\$this->dep('datos')->eliminar_todo();",											
											"\$this->dep('datos')->resetear();"));
			$this->ci->php()->agregar($metodo);
		}
		
		//--------------------------------------------------------
		//--- evt__cuadro__seleccion -----------------------------
		//--------------------------------------------------------
		$evento = $cuadro->agregar_evento('seleccion');
		$evento->en_botonera(false);
		if (! empty($clave_dt)) {
			$evento->sobre_fila();
		}
		$evento->set_imagen('doc.gif');
		$metodo = new toba_codigo_metodo_php('evt__cuadro__seleccion',array('$datos'));
		$php = array("\$this->dep('datos')->cargar(\$datos);");
		if ($this->molde_abms['gen_separar_pantallas']) {
			$php[] = "\$this->set_pantalla('pant_edicion');";
		}
		$metodo->set_contenido($php);
		$this->ci->php()->agregar($metodo);			
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
		$this->ci->php()->agregar(new toba_codigo_separador_php('Formulario'));	
		//--------------------------------------------------------
		//--- conf__formulario  ----------------------------------
		//--------------------------------------------------------
		$tabla_actual = $this->molde_abms['tabla'];
		$metodo = new toba_codigo_metodo_php('conf__formulario', array('toba_ei_formulario $form'));
		$contenido = array("if (\$this->dep('datos')->esta_cargada()) {",
										"\t\$form->set_datos(\$this->dep('datos')->tabla('$tabla_actual')->get());");
		if ($this->molde_abms['gen_separar_pantallas']) {
			$contenido[] = "} else {";
			$contenido[] = "\t\$this->pantalla()->eliminar_evento('eliminar');";
		}
		$contenido[] = "}";
		$metodo->set_contenido($contenido);
		$this->ci->php()->agregar($metodo);
		//--------------------------------------------------------
		//--- evt__formulario__alta ------------------------------
		//--------------------------------------------------------
		if (! $this->molde_abms['gen_separar_pantallas']) {		
			$evento = $form->agregar_evento('alta');
			$evento->en_botonera();
			$evento->set_etiqueta('Alta');
			$evento->set_imagen('nucleo/agregar.gif');
			$evento->maneja_datos();
			$evento->set_predeterminado();
			$evento->set_grupos('no_cargado');
			$metodo = new toba_codigo_metodo_php('evt__formulario__alta', array('$datos'));
			$metodo->set_contenido( array("\$this->dep('datos')->tabla('$tabla_actual')->set(\$datos);",
											"\$this->dep('datos')->sincronizar();",
											"\$this->resetear();"));
			$this->ci->php()->agregar($metodo);
		}
		
		//--------------------------------------------------------
		//--- evt__formulario__modificacion ----------------------
		//--------------------------------------------------------
		$evento = $form->agregar_evento('modificacion');
		$evento->maneja_datos();
		$metodo = new toba_codigo_metodo_php('evt__formulario__modificacion',array('$datos'));		
		if ($this->molde_abms['gen_separar_pantallas']) {
			$evento->implicito();
			$metodo->set_contenido( array(	"\$this->dep('datos')->tabla('$tabla_actual')->set(\$datos);"));
		} else {
			$evento->en_botonera();
			$evento->set_predeterminado();
			$evento->set_etiqueta('Modificar');
			$evento->set_imagen('refrescar.png');
			$evento->set_grupos('cargado');
			$metodo->set_contenido( array(	"\$this->dep('datos')->tabla('$tabla_actual')->set(\$datos);",
										"\$this->dep('datos')->sincronizar();",
										"\$this->resetear();"));
			
		}
		$this->ci->php()->agregar($metodo);
				
		//--------------------------------------------------------
		//--- evt__formulario__baja ------------------------------
		//--------------------------------------------------------
		if (! $this->molde_abms['gen_separar_pantallas']) {
			$evento = $form->agregar_evento('baja');
			$evento->set_etiqueta('Eliminar');
			$evento->en_botonera();
			$evento->set_imagen('borrar.gif');
			$evento->set_estilo('ei-boton-baja');
			$evento->set_confirmacion($this->confirmacion_eliminar);
			$evento->set_grupos('cargado');
			$metodo = new toba_codigo_metodo_php('evt__formulario__baja');
			$metodo->set_contenido( array(	"\$this->dep('datos')->eliminar_todo();",
											"\$this->resetear();"));
			$this->ci->php()->agregar($metodo);
		}
					

		//--------------------------------------------------------
		//--- evt__formulario__cancelar --------------------------
		//--------------------------------------------------------
		if (! $this->molde_abms['gen_separar_pantallas']) {		
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
		}
		//--------------------------------------------------------
		//--- Metodo para resetear la operacion
		//--------------------------------------------------------
		$metodo = new toba_codigo_metodo_php('resetear');
		$php[] = "\$this->dep('datos')->resetear();";
		if ($this->molde_abms['gen_separar_pantallas']) {
			$php[] = "\$this->set_pantalla('pant_seleccion');";
		}
		$metodo->set_contenido($php);
		$this->ci->php()->agregar($metodo);
	}
}
?>