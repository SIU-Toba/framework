<?php

class toba_ci_info extends toba_ei_info
{
	static function get_tipo_abreviado()
	{
		return "CI";		
	}
	
	function get_nombre_instancia_abreviado()
	{
		return "ci";	
	}	
	
	/**
	*	Retorna la metaclase correspondiente a la pantalla
	*/
	function get_metaclase_subcomponente($subcomponente)
	{
		for ($i = 0 ; $i < count($this->datos['_info_ci_me_pantalla']) ; $i++) {
			if ($this->datos['_info_ci_me_pantalla'][$i]['identificador'] === $subcomponente) {
				return new toba_ci_pantalla_info($this->datos['_info_ci_me_pantalla'][$i],array(), $this->proyecto, $this->id, $this->datos['_info_obj_pantalla']);
			}
		}
		throw new toba_error("No se encuentra la pantalla '$id'");
	}
	
	/**
	 * Se redefine para clonar la subclase de la pantalla
	 */
	protected function clonar_subclase($dr, $dir_subclases, $proyecto_dest)
	{
		parent::clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		foreach ($this->get_hijos(true) as $pantalla) {
			$pantalla->clonar_subclase($dr, $dir_subclases, $proyecto_dest);
		}
	}	
	
	
	
	//---------------------------------------------------------------------	
	//-- Recorrible como ARBOL
	//---------------------------------------------------------------------

	function es_hoja()
	{
		$es_hoja = parent::es_hoja() && $this->get_cant_pantallas() == 0;
	}
	
	function tiene_hijos_cargados()
	{
		return (!$this->es_hoja() && count($this->subelementos) != 0) || $this->get_cant_pantallas() != 0;
	}
		
	function get_pantalla($id)
	{
		for ($i = 0 ; $i < count($this->datos['_info_ci_me_pantalla']) ; $i++) {
			if ((string) $this->datos['_info_ci_me_pantalla'][$i]['pantalla'] === (string) $id) {
				return new toba_ci_pantalla_info($this->datos['_info_ci_me_pantalla'][$i],
											$this->subelementos, $this->proyecto, $this->id, $this->datos['_info_obj_pantalla']);
			}
		}
		throw new toba_error("No se encuentra la pantalla $id");
	}
	
	function get_cant_pantallas()
	{
		if ($this->carga_profundidad) {
			return count($this->datos['_info_ci_me_pantalla']);
		} else {
			return 0;	
		}
	}

	function get_hijos($solo_pantallas=false)
	{
		//Las dependencias son sus hijos
		//Hay una responsabilidad no bien limitada
		//Este objeto tiene las dependencias, cada pantalla debería poder sacar las que les concierne
		//Pero tambien este objeto debería saber cuales no son utilizadas por las pantallas
		$pantallas = array();
		if ($this->carga_profundidad && count($this->datos['_info_ci_me_pantalla'])>0) {
			//Se ordena por la columna orden
			$datos_pantallas = rs_ordenar_por_columna($this->datos['_info_ci_me_pantalla'],'orden');
			foreach ($datos_pantallas as $pantalla) {
				$pantallas[] = new toba_ci_pantalla_info($pantalla, $this->subelementos, $this->proyecto, $this->id, $this->datos['_info_obj_pantalla']);
			}
		}
		//Busca Dependencias libres
		$dependencias_libres = array();
		foreach ($this->subelementos as $dependencia) {
			$libre = true;
			foreach ($pantallas as $pantalla) {
				if ($pantalla->tiene_dependencia($dependencia)) {
					$libre = false;
				}
			}
			if ($libre) {
				$dependencias_libres[] = $dependencia;
			}
		}
		if ($solo_pantallas) {
			return $pantallas;
		} else {
			return array_merge($pantallas, $dependencias_libres);
		}
	}	

	function get_utilerias()
	{
		$iconos = array();
		$iconos[] = array(
			'imagen' => toba_recurso::imagen_toba("objetos/objeto_nuevo.gif", false),
			'ayuda' => "Crear un nuevo componente asociado al controlador",
			'vinculo' => toba::vinculador()->get_url(toba_editor::get_id(),"1000247",
								array(	'destino_tipo' => 'toba_ci', 
										'destino_proyecto' => $this->proyecto,
										'destino_id' => $this->id ),
								array(	'menu' => true,
										'celda_memoria' => 'central')
						),
			'plegado' => true										
		);
		return array_merge($iconos, parent::get_utilerias());	
	}		

	//---------------------------------------------------------------------	
	//-- EVENTOS
	//---------------------------------------------------------------------

	function eventos_predefinidos()
	{
		$eventos = parent::eventos_predefinidos();	
		/*	Hay que agregar entradas y salidas de pantallas */
		return $eventos;
	}

	static function get_modelos_evento()
	{
		$modelo[0]['id'] = 'proceso';
		$modelo[0]['nombre'] = 'Guardar - Cancelar';
		$modelo[1]['id'] = 'abm';
		$modelo[1]['nombre'] = 'ABM / CRUD';
		$modelo[2]['id'] = 'imprimir';
		$modelo[2]['nombre'] = 'Imprimir la Pantalla';
		return $modelo;
	}

	static function get_lista_eventos_estandar($modelo)
	{
		$evento = array();
		switch($modelo){
			case 'proceso':
				//Procesar
				$evento[0]['identificador'] = "procesar";
				$evento[0]['etiqueta'] = "&Guardar";
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'guardar.gif';
				$evento[0]['maneja_datos'] = 1;
				$evento[0]['orden'] = 0;
				$evento[0]['en_botonera'] = 1;
				$evento[0]['defecto'] = 1;
				//Cancelar
				$evento[1]['identificador'] = "cancelar";
				$evento[1]['etiqueta'] = "&Cancelar";
				$evento[1]['maneja_datos'] = 0;
				$evento[1]['orden'] = 1;
				$evento[1]['en_botonera'] = 1;
				break;
			case 'abm':
				//Agregar
				$evento[0]['identificador'] = "agregar";
				$evento[0]['etiqueta'] = "&Agregar";
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'nucleo/agregar.gif';
				$evento[0]['maneja_datos'] = 0;
				$evento[0]['orden'] = 0;
				$evento[0]['en_botonera'] = 1;
				//volver
				$evento[1]['identificador'] = "cancelar";
				$evento[1]['etiqueta'] = "&Volver";
				$evento[1]['imagen_recurso_origen'] = 'apex';
				$evento[1]['imagen'] = 'deshacer.png';
				$evento[1]['maneja_datos'] = 0;
				$evento[1]['orden'] = 1;
				$evento[1]['en_botonera'] = 1;
				//Eliminar
				$evento[2]['identificador'] = "eliminar";
				$evento[2]['etiqueta'] = "&Eliminar";
				$evento[2]['imagen_recurso_origen'] = 'apex';
				$evento[2]['imagen'] = 'borrar.png';
				$evento[2]['confirmacion'] = "¿Esta seguro que desea ELIMINAR los datos?";
				$evento[2]['maneja_datos'] = 0;
				$evento[2]['orden'] = 2;
				$evento[2]['en_botonera'] = 1;
				//Guardar
				$evento[3]['identificador'] = "guardar";
				$evento[3]['etiqueta'] = "&Guardar";
				$evento[3]['imagen_recurso_origen'] = 'apex';
				$evento[3]['imagen'] = 'guardar.gif';
				$evento[3]['maneja_datos'] = 1;
				$evento[3]['orden'] = 3;
				$evento[3]['en_botonera'] = 1;
				$evento[3]['defecto'] = 1;
				break;
			case 'imprimir':
				//Procesar
				$evento[0]['identificador'] = "imprimir";
				$evento[0]['etiqueta'] = "&Imprimir";
				$evento[0]['imagen_recurso_origen'] = 'apex';
				$evento[0]['imagen'] = 'impresora.gif';
				$evento[0]['maneja_datos'] = 0;
				$evento[0]['orden'] = 10;
				$evento[0]['en_botonera'] = 1;
				$evento[0]['defecto'] = 0;
				$evento[0]['accion'] = 'H';
				$evento[0]['accion_imphtml_debug'] = 1;
				break;
		}
		return $evento;
	}

	//---------------------------------------------------------------------	
	//-- METACLASE
	//---------------------------------------------------------------------

	function get_molde_subclase()
	{
		$molde = $this->get_molde_vacio();
		//************** Elementos PROPIOS *************
		//-- Ini operacion
		$doc = array();
		$doc[] = "Se ejecuta por única vez cuando el componente entra en la operación.";
		$doc[] = "Es útil por ejemplo para inicializar un conjunto de variables de sesion y evitar el chequeo continuo de las mismas";
		$doc[] = "Hay situaciones en las que su ejecución no coincide con el instante inicial de operación:";
		$doc[] = " - Si el componente es un ci dentro de otro ci, recién se ejecuta cuando entra a la operacion que no necesariamente es al inicio, si por ejemplo se encuentra en la 3er pantalla del ci principal.";
		$doc[] = " - Si se ejecuta una limpieza de memoria (comportamiento por defecto del evt__cancelar)";		
		$metodo = new toba_codigo_metodo_php('ini__operacion', array(), $doc);
		$metodo->set_doc('[api:Componentes/Eis/toba_ci#ini__operacion Ver doc]');
		$molde->agregar($metodo);
		
		//-- Final
		$doc = "Ventana de extensión previa a la destrucción del componente, al final de la atención de los servicios";
		$metodo = new toba_codigo_metodo_php('fin', array(), array($doc));
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		

		$molde->agregar( new toba_codigo_separador_php('Configuraciones','Configuracion','grande') );
		
		//-- Conf
		$doc = array();
		$doc[] = "Ventana que se ejecuta al inicio de la etapa de configuración. Antes de la configuración de la pantalla y sus componentes";
		$doc[] = "Se utiliza por ejemplo para determinar qué pantalla mostrar, eliminar tabs, etc.";
		$metodo = new toba_codigo_metodo_php('conf', array(), $doc);
		$metodo->set_doc('[api:Componentes/Eis/toba_ci#conf Ver doc]');
		$molde->agregar($metodo);
		
		
		//-- Configuracion de pantallas -----------
		$datos_pantallas = rs_ordenar_por_columna($this->datos['_info_ci_me_pantalla'],'orden');
		foreach($datos_pantallas as $pantalla) {
			$doc = array();
			$doc[] = 'Ventana de extensión para configurar la pantalla. Se ejecuta previo a la configuración de los componentes pertenecientes a la pantalla ';
			$doc[] = 'por lo que es ideal por ejemplo para ocultarlos en base a una condición dinámica, ej. $pant->eliminar_dep("tal") ';
			$doc[] = '@param toba_ei_pantalla $pantalla';
			$metodo = new toba_codigo_metodo_php('conf__' . $pantalla['identificador'], array('toba_ei_pantalla $pantalla'), $doc);
			$metodo->set_doc(implode("\n", $doc));
			$molde->agregar($metodo);

			//Aca incluyo los metodos de entrada y salida de pantallas.
			$doc = array();
			$doc[] = 'Ventana de extension para ejecutar controles antes de entrar a la pagina.';
			$doc[] = 'Se ejecuta luego de lanzar los eventos del ci.';
			$doc[] = 'Si se lanza una excepcion se evita el cambio de pantalla.';
			$doc[] = '[wiki:Referencia/Objetos/ci#Controlandolaentradaylasalida Ver más]';
			$nombre_metodo_entrada = 'evt__'. $pantalla['identificador'] . '__entrada';
			$metodo = new toba_codigo_metodo_php($nombre_metodo_entrada, array(), $doc);
			$metodo->set_doc(implode("\n", $doc));
			$molde->agregar($metodo);

			$doc = array();
			$doc[] = 'Ventana de extension para ejecutar controles antes de salir de la pagina.';
			$doc[] = 'Se ejecuta luego de lanzar los eventos del ci.';
			$doc[] = 'Si se lanza una excepcion se evita el cambio de pantalla.';
			$doc[] = '[wiki:Referencia/Objetos/ci#Controlandolaentradaylasalida Ver más]';
			$nombre_metodo_salida = 'evt__' . $pantalla['identificador'] . '__salida';
			$metodo = new toba_codigo_metodo_php($nombre_metodo_salida, array(), $doc);
			$metodo->set_doc(implode("\n", $doc));
			$molde->agregar($metodo);
		}
		
		//-- Post Configurar
		$doc = "Ventana para insertar lógica de la configuración del ci y sus dependencias";
		$metodo = new toba_codigo_metodo_php('post_configurar', array(), array($doc));
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		
		
		//-- Eventos propios ----------------------
		if (count($this->eventos_predefinidos()) > 0) {
			$molde->agregar( new toba_codigo_separador_php('Eventos',null,'grande') );
			foreach ($this->eventos_predefinidos() as $evento => $info) {
				if ($info['info']['accion'] != 'V') { //No es Vinculo
					if ($evento == 'cancelar') {
						$doc = array("Originalmente este método limpia las variables y definiciones del componente, y en caso de exisitr un CN asociado ejecuta su cancelar. Para mantener este comportamiento llamar a parent::evt__cancelar");
					} elseif ($evento == 'procesar') {
						$doc = array("Originalmente este método si existe un CN asociado ejecuta su procesar. Para mantener este comportamiento llamar a parent::evt__procesar");
					} else {
						$doc = array("Atrapa la interacción del usuario a través del botón asociado. El método no recibe parámetros");
					}
					$metodo = new toba_codigo_metodo_php('evt__' . $evento, array(), $doc);
					$metodo->set_doc(implode("\n", $doc)); 
					$molde->agregar($metodo);
				}
			}
		}

		//-- Post Eventos
		$doc = "Ventana que se ejecuta una vez que todos los eventos se han disparado para este objeto";
		$metodo = new toba_codigo_metodo_php('post_eventos', array(), array($doc));
		$metodo->set_doc($doc);
		$molde->agregar($metodo);		

			
		//**************** DEPENDENCIAS ***************
		if (count($this->subelementos)>0) {
			foreach ($this->subelementos as $id => $elemento) {
				$es_ei = ($elemento instanceof toba_ei_info) && !($elemento instanceof toba_ci_info);
				$rol = $elemento->rol_en_consumidor();
				if ($es_ei) {
					$molde->agregar( new toba_codigo_separador_php($rol, null, 'grande') );
					//Metodo de CONFIGURACION
					$tipo = $elemento->get_clase_nombre_final();
					$nombre_instancia = $elemento->get_nombre_instancia_abreviado();
					
					$metodo = new toba_codigo_metodo_php('conf__' . $rol,	
																array($tipo.' $'.$nombre_instancia),
																$elemento->get_comentario_carga());
					$metodo->set_grupo($rol);
					$ei = get_class($elemento);
					$ei = substr($ei, 5, strlen($ei) - 10);
					$metodo->set_doc("Ventana para configurar al componente. Por lo general se le brindan datos a través del método <pre>set_datos(\$datos)</pre>. 
										[wiki:Referencia/Objetos/$ei#Configuraci%C3%B3n Ver más]");
					$molde->agregar($metodo);
					
					//Eventos predefinidos del elemento
					if (count($elemento->eventos_predefinidos()) > 0) {
						foreach ($elemento->eventos_predefinidos() as $evento => $info) {
							$metodo = new toba_codigo_metodo_php('evt__' . $rol . '__' .$evento,	
																		$info['parametros'],
																		$info['comentarios']);
							$metodo->set_grupo($rol);
							$metodo->set_doc('Atrapa la interacción del usuario con el botón asociado a la dependencia. 
												Recibe por parámetro los datos que acarrea el evento, por ejemplo si es un formulario los datos del mismo.
												[wiki:Referencia/Eventos#Listeners Ver más]');
							$molde->agregar($metodo);
							
							//Si es evento sobre fila brindo la oportunidad de configurarlo (caso ML y Cuadro)
							if (isset($info['info']['sobre_fila']) && ($info['info']['sobre_fila'] == 1)) {
								$nombre ='conf_evt__' . $rol . '__' .$evento;
								$parametros = array('toba_evento_usuario $evento', '$fila');
								$doc = array();
								$doc[] = 'Permite configurar el evento por fila.';
								$doc[] = 'Útil para decidir si el evento debe estar disponible o no de acuerdo a los datos de la fila';
								$doc[] = "[wiki:Referencia/Objetos/$ei#Filtradodeeventosporfila Ver más]";

								//Agrego el metodo correspondiente para la configuracion del evento
								$metodo = new toba_codigo_metodo_php($nombre,	$parametros, $doc);
								$metodo->set_doc(implode("\n", $doc));
								$metodo->set_grupo($rol);
								$molde->agregar($metodo);
							}
						}
					}
				}
			}
		}
		//***************** JAVASCRIPT *****************
		$molde->agregar_bloque( $this->get_molde_eventos_js() );
		return $molde;
	}

	static function get_eventos_internos(toba_datos_relacion $dr)
	{
		$eventos = array();
		$navegacion = $dr->tabla('prop_basicas')->get_columna('tipo_navegacion');
		if (isset($navegacion)) {
			if ($navegacion == 'wizard') {
				$eventos['cambiar_tab__siguiente'] = "El usuario avanza de pantalla, generalmente con el botón <em>Siguiente</em>.";
				$eventos['cambiar_tab__anterior'] = "El usuario retrocede de pantalla, generalmente con el botón <em>Anterior</em>.";
			} else {
				$eventos['cambiar_tab_X'] = "El usuario cambia a la pantalla X utilizando los tabs o solapas.";
			}
		}
		return $eventos;
	}	
}
?>