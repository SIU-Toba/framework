<?php
require_once('objetos_toba/ci_editores_toba.php');

class ci_editor extends ci_editores_toba
{
	protected $s__pantalla_dep_asoc;
	protected $s__pantalla_evt_asoc;
	protected $cambio_objeto = false;		//Se esta editando un nuevo objeto?
	protected $clase_actual = 'toba_ci';
	protected $s__pantalla_php_db;			// La base posee registro de la existencia de una extension??
	protected $s__pantalla_php_archivo;		// El archivo de la extension existe en el sistema de archivos??
	protected $pant_sel_temp;
	
	function ini()
	{
		parent::ini();
		$pantalla = toba::memoria()->get_parametro('pantalla');
		//---- ¿Se selecciono una pantalla desde afuera?
		if (isset($pantalla)) {
			$this->set_pantalla(2);
			//Se busca cual es el id interno del ML para enviarselo
			$datos = $this->conf__pantallas_lista($this->dep('pantallas_lista'));
			foreach ($datos as $id => $dato) {
				if ($dato['identificador'] == $pantalla) {
					$this->evt__pantallas_lista__seleccion($id);
				}
			}
		}
	}

	function conf()
	{
		if (isset($this->pant_sel_temp)) {
		 	$this->get_entidad()->tabla('pantallas')->set_cursor($this->pant_sel_temp);			
		}
		parent::conf();
		//Mecanismo para saber si la extension PHP de un AP ya exite en la DB y posee archivo
		if (!isset($this->s__pantalla_php_db)) {
			$this->s__pantalla_php_db = array();
			$this->s__pantalla_php_archivo = array();
			if ($this->componente_existe_en_db()) {
				$datos_pantalla = $this->get_entidad()->tabla('pantallas')->get_filas();
				foreach ($datos_pantalla as $pantalla) {
					if (isset($pantalla['subclase']) && $pantalla['subclase'] &&  isset($pantalla['subclase_archivo'])) {
						$this->s__pantalla_php_db[$pantalla['x_dbr_clave']] = $pantalla['identificador'];
						if (admin_util::existe_archivo_subclase($pantalla['subclase_archivo'], $pantalla['punto_montaje'])) {
							$this->s__pantalla_php_archivo[$pantalla['x_dbr_clave']] = true;
						}
					}
				}
			}
		}
	}

	function evt__procesar()
	{
		parent::evt__procesar();
		unset($this->s__pantalla_php_db);
		unset($this->s__pantalla_php_archivo);
	}
	
	// *******************************************************************
	// ******************* tab PROPIEDADES BASICAS  **********************
	// *********************************************
	//Para no cambiar la visual del editor los que hago es redireccionar
	//la columna posicion_botonera entre el formulario de propiedades
	//basicas y el datos-tabla de base
	//****************************************************************************
	function conf__prop_basicas($form)
	{
		$datos = $this->get_entidad()->tabla('prop_basicas')->get();
		$datos['posicion_botonera'] = $this->get_entidad()->tabla('base')->get_columna('posicion_botonera');
		$form->set_datos($datos);
	}

	function evt__prop_basicas__modificacion($datos)
	{
		$this->get_entidad()->tabla('base')->set_columna_valor('posicion_botonera', $datos['posicion_botonera']);
		unset($datos['posicion_botonera']);
		$this->get_entidad()->tabla('prop_basicas')->set($datos);
	}

	// *******************************************************************
	// *******************  tab DEPENDENCIAS  ****************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/
	function evt__1__salida()
	{
		$this->dep('dependencias')->limpiar_seleccion();
	}

	function get_dbr_dependencias()
	{
		return $this->get_entidad()->tabla('dependencias');
	}
	
	/**
	*	El ci de dependencias avisa que una dependencia cambio su identificacion
	*/
	function modificar_dependencia($anterior, $nuevo)
	{
		//Este cambio se le notifica a las pantallas
		$this->get_entidad()->tabla('pantallas')->cambiar_id_dependencia($anterior, $nuevo);
	}	
	
	// *******************************************************************
	// ******************* tab PANTALLAS  ********************************
	// *******************************************************************

	function existen_deps()
	{
		return count($this->s__pantalla_dep_asoc) > 0;
	}
	
	function existen_evts()
	{
		return count($this->s__pantalla_evt_asoc) > 0;
	}
	
	function hay_pant_sel()
	{
		return $this->get_entidad()->tabla('pantallas')->hay_cursor();
	}
	
	function get_pant_actual()
	{
		return $this->get_entidad()->tabla('pantallas')->get_cursor();
	}
	
	function get_datos_pantalla_actual()
	{
		return $this->get_entidad()->tabla('pantallas')->get();
	}
	
	function conf__2($pantalla)
	{
		//--- Armo la lista de DEPENDENCIAS disponibles
		$this->s__pantalla_dep_asoc = array();
		if ($registros = $this->get_entidad()->tabla('dependencias')->get_filas()) {
			foreach ($registros as $reg) {
				$clase = explode(',', $reg['clase']);
				if (! in_array('toba_datos_tabla', $clase) && ! in_array('toba_datos_relacion', $clase)) {
						$this->s__pantalla_dep_asoc[$reg['identificador']] = $reg['identificador'];
				}
			}
		}
		//--- Armo la lista de EVENTOS disponibles
		$this->s__pantalla_evt_asoc = array();
		if ($registros = $this->get_entidad()->tabla('eventos')->get_filas()) {
			foreach ($registros as $reg) {
				$this->s__pantalla_evt_asoc[$reg['identificador']] = $reg['identificador'];
			}
		}		

		//--- Se selecciono una pantalla?
		if ($this->hay_pant_sel()) {
			$this->dependencia('pantallas_lista')->seleccionar($this->get_pant_actual());
			if (empty($this->s__pantalla_dep_asoc)) {
				$pantalla->eliminar_dep('pantallas_ei');
			}
			if (empty($this->s__pantalla_evt_asoc)) {
				$pantalla->eliminar_dep('pantallas_evt');			
			}			
		} else {
			$pantalla->eliminar_dep('pantallas_ei');
			$pantalla->eliminar_dep('pantallas_evt');
			$pantalla->eliminar_dep('pantallas');
		}
	}
	
	function evt__2__salida()
	{
		$this->get_entidad()->tabla('pantallas')->resetear_cursor();
	}

	function evt__cancelar_pantalla()
	{
		$this->get_entidad()->tabla('pantallas')->resetear_cursor();
	}

	function evt__aceptar_pantalla()
	{
		$this->get_entidad()->tabla('pantallas')->resetear_cursor();
	}

	//----------------------------------------------------------
	//-- Lista -------------------------------------------------
	//----------------------------------------------------------
	
	function evt__pantallas_lista__modificacion($registros)
	{
		/*
			Como en el mismo request es posible dar una columna de alta y seleccionarla,
			tengo que guardar el ID intermedio que el ML asigna en las columnas NUEVAS,
			porque ese es el que se pasa como parametro en la seleccion
		*/
		$dbr = $this->get_entidad()->tabla('pantallas');
		$orden = 1;
		foreach (array_keys($registros) as $id) {
			//Creo el campo orden basado en el orden real de las filas
			//ATENCION:  Ya esta soportado en el ML
			$registros[$id]['orden'] = $orden;
			$orden++;
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch ($accion) {
				case 'A':
					$dbr->nueva_fila($registros[$id], null, $id);
					break;	
				case 'B':
					$dbr->eliminar_fila($id);
					break;	
				case 'M':
					$dbr->modificar_fila($id, $registros[$id]);
					break;	
			}
		}		
	}
	
	function evt__pantallas_lista__seleccion($id)
	{
		$this->pant_sel_temp = $id;
	}
	
	function conf__pantallas_lista($ml)
	{
		$datos_dbr = $this->get_entidad()->tabla('pantallas')->get_filas();
		if (!empty($datos_dbr)) {
			//Ordeno los registros segun la 'posicion'
			//ei_arbol($datos_dbr,"Datos para el ML: PRE proceso");
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$orden[] = $datos_dbr[$a]['orden'];
			}
			array_multisort($orden, SORT_ASC, $datos_dbr);
			//EL formulario_ml necesita necesita que el ID sea la clave del array
			// No se solicita asi del DBR porque array_multisort no conserva claves numericas
			// y las claves internas del DBR lo son
			for ($a = 0; $a < count($datos_dbr); $a++) {
				$id_dbr = $datos_dbr[$a][apex_db_registros_clave];
				unset($datos_dbr[$a][apex_db_registros_clave]);
				$datos[$id_dbr] = $datos_dbr[$a];
			}
			//ei_arbol($datos,"Datos para el ML: POST proceso");
		} else {
			//--Carga inicial
			$datos = array(array(
							'identificador' => 'pant_inicial', 
							'etiqueta' => 'Pantalla Inicial',
							apex_ei_analisis_fila => 'A'
					));
		}
		$ml->set_datos($datos);		
		
		//--- Se setea al ml el proximo ID
		$ml->set_proximo_id($this->get_entidad()->tabla('pantallas')->get_proximo_id());
	
		//--Protejo la evento seleccionada de la eliminacion		
		if ($this->hay_pant_sel()) {
			$this->dependencia('pantallas_lista')->set_fila_protegida($this->get_pant_actual());
		}
		return $datos;
	}
	
	//------------------------------------------------------
	//-- Informacion extendida de la pantalla  -------------
	//------------------------------------------------------

	function evt__pantallas__modificacion($datos)
	{
		$this->get_entidad()->tabla('pantallas')->set($datos);
	}
	
	function conf__pantallas(toba_ei_formulario $obj)
	{
		$id_actual = $this->get_pant_actual();		
		if (!isset($this->s__pantalla_php_db[$id_actual])) {
			$obj->eliminar_evento('ver_php');
			$obj->eliminar_evento('abrir_php');
			//-- Debo pasarle el id de la pantalla a extender
			$pantalla = $this->get_entidad()->tabla('pantallas')->get();
			$obj->evento('extender')->vinculo()->agregar_parametro('subcomponente', $pantalla['identificador']);
			//$obj->evento('extender')->vinculo()->agregar_parametro('punto_montaje', $pantalla['punto_montaje']);
		} else {
			$obj->eliminar_evento('extender');			
			// Link al editor
			$parametros = toba_componente_info::get_utileria_editor_parametros(array('proyecto'=>$this->id_objeto['proyecto'],
																			'componente'=> $this->id_objeto['objeto']),
																			$this->s__pantalla_php_db[$id_actual]);
			$obj->evento('ver_php')->vinculo()->set_parametros($parametros);

			if (isset($this->s__pantalla_php_archivo[$id_actual])) {
				// Apertura de archivos
				$abrir = toba_componente_info::get_utileria_editor_abrir_php(array('proyecto'=>$this->id_objeto['proyecto'],
																			'componente'=> $this->id_objeto['objeto']),
																			$this->s__pantalla_php_db[$id_actual]);
				$obj->set_js_abrir($abrir['js']);
			} else {
				$obj->evento('ver_php')->set_imagen('nucleo/php_inexistente.gif');
				$obj->eliminar_evento('abrir_php');
			}
		}
		$obj->set_datos($this->get_entidad()->tabla('pantallas')->get());
	}

	//------------------------------------------------------
	//--- Asociacion de DEPENDENCIAS a pantallas  ----------
	//------------------------------------------------------

	function conf__pantallas_ei($componente)
	{		
		//Ahora busco las que estan asociadas a la pantalla actual
		$busqueda = $this->get_entidad()->tabla('objetos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$ids = $busqueda->buscar_ids();
		$objetos_en_pantalla = array();
		foreach ($ids as $id) {
			$obj_a = $this->get_entidad()->tabla('objetos_pantalla')->get_fila_columna($id, 'dependencia');
			$orden = $this->get_entidad()->tabla('objetos_pantalla')->get_fila_columna($id, 'orden');
			$objetos_en_pantalla[] = array('dependencia' => $obj_a, 'orden' => $orden);
		}
		$salida = rs_ordenar_por_columna($objetos_en_pantalla, 'orden');
		$componente->set_datos($salida);
	}

	function evt__pantallas_ei__modificacion($datos)
	{
		//Primero busco lo que esta y lo elimino, asi no quedan registros raros dando vueltas
		$busqueda = $this->get_entidad()->tabla('objetos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$ids = $busqueda->buscar_ids();
		foreach ($ids as $id) {
			$this->get_entidad()->tabla('objetos_pantalla')->eliminar_fila($id);
		}

		//Seteo los cursores correspondientes y doy de alta los registros
		$this->get_entidad()->tabla('pantallas')->set_cursor($this->get_pant_actual());
		$orden = 0;
		foreach ($datos as $dato) {
			$id = $this->get_entidad()->tabla('dependencias')->get_id_fila_condicion(array('identificador' => $dato['dependencia']));
			$this->get_entidad()->tabla('dependencias')->set_cursor(current($id));
			$this->get_entidad()->tabla('objetos_pantalla')->nueva_fila(array('orden' => $orden, 'dependencia' => $dato['dependencia']));
			$orden++;
		}
		//Reseteo el cursor asi no se queda apuntando a donde no debe
		$this->get_entidad()->tabla('dependencias')->resetear_cursor();
	}

	function combo_dependencias()
	{
		$datos = null;
		$a = 0;
		foreach ($this->s__pantalla_dep_asoc as $dep => $info) {
			$datos[$a]['id'] = $dep; 
			$datos[$a]['desc'] = $info; 
			$a++;
		}
		return $datos;
	}

	//------------------------------------------------------
	//--- Asociacion de EVENTOS a pantallas  ---------------
	//------------------------------------------------------

	function conf__pantallas_evt(toba_ei_formulario $form)
	{
		$datos = array();
		//Meto los eventos asociados actuales por si agregaron alguno.
		foreach ($this->s__pantalla_evt_asoc as $dep) {
			$datos[$dep] = array('evento' => $dep, 'asociar' => 0);
		}
		//Busco la asociacion hecha
		$busqueda = $this->get_entidad()->tabla('eventos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$ids = $busqueda->buscar_ids();
		foreach ($ids as $id) {
			$id_evt_padre = $this->get_entidad()->tabla('eventos_pantalla')->get_id_padres(array($id), 'eventos');
			$evt_involucrado = $this->get_entidad()->tabla('eventos')->get_fila_columna(current($id_evt_padre), 'identificador');
			$datos[$evt_involucrado] = array('evento' => $evt_involucrado, 'asociar' => 1);
		}
		$form->set_datos(array_values($datos));
	}

	function evt__pantallas_evt__modificacion($datos)
	{
		//Busco la asociacion hecha para borrar los datos de las tablas.
		$busqueda = $this->get_entidad()->tabla('eventos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$ids = $busqueda->buscar_ids();
		foreach ($ids as $id) {
			$evt_involucrado = $this->get_entidad()->tabla('eventos_pantalla')->eliminar_fila($id);
		}

		//Ahora meto las filas nuevas
		$this->get_entidad()->tabla('pantallas')->set_cursor($this->get_pant_actual());
		foreach ($datos as $evt) {
			if ($evt['asociar'] == '1') {
				$id_ev = $this->get_entidad()->tabla('eventos')->get_id_fila_condicion(array('identificador' => $evt['evento']));
				$this->get_entidad()->tabla('eventos')->set_cursor(current($id_ev));
				$this->get_entidad()->tabla('eventos_pantalla')->nueva_fila(array('identificador' => $evt['evento']));
			}
		}
		$this->get_entidad()->tabla('eventos')->resetear_cursor();
	}
	
	
	
	// *******************************************************************
	// *******************  tab LAYOUT   *********************************
	// *******************************************************************

	function conf__4()
	{
		if (! $this->get_entidad()->tabla('pantallas')->hay_cursor()) {
			$this->pantalla()->eliminar_dep('form_layout');
		}
	}
	
	function conf__cuadro_layout(toba_ei_cuadro $cuadro)
	{
		$cursor = $this->get_entidad()->tabla('pantallas')->get_cursor();
		$cuadro->seleccionar(array(apex_datos_clave_fila => $cursor));
		$filas = $this->get_entidad()->tabla('pantallas')->get_filas();
		foreach (array_keys($filas) as $id) {
			$filas[$id]['template'] = (!isset($filas[$id]['template']) || trim($filas[$id]['template']) == '') ? 0 : 1;
		}
		$cuadro->set_datos($filas);
	}
	
	function evt__cuadro_layout__seleccion($id)
	{
		$this->pant_sel_temp = $id;
	}
	
	function conf__form_layout(toba_ei_formulario $form)
	{
		$vinculo = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'ejecutar'));		
		$form->ef('template')->set_botonera('Layout');
		$form->ef('template')->set_alto('400px');		
		$form->ef('template')->set_path_template(array($vinculo));		
		toba::logger()->debug($vinculo);
		$datos = $this->get_entidad()->tabla('pantallas')->get();
		if (isset($datos['template']) && trim($datos['template']) != '') {
			$datos['tipo_layout'] = 'L';
		}
		$form->set_datos($datos);
	}
	
	function evt__form_layout__modificacion($datos)
	{
		if (!isset($datos['tipo_layout'])) {
			$datos['template'] = null;
		}
		$this->get_entidad()->tabla('pantallas')->set($datos);
	}

	function evt__4__salida()
	{	//Limpio cursor y variable temporal para que no influya en la pantalla de layout impresion
		unset($this->pant_sel_temp);
		$this->get_entidad()->tabla('pantallas')->resetear_cursor();
	}

	// *******************************************************************
	// *******************  tab LAYOUT IMPRESION *******************
	// *******************************************************************

	function conf__5()
	{
		if (! $this->get_entidad()->tabla('pantallas')->hay_cursor()) {
			$this->pantalla()->eliminar_dep('form_layout_impresion');
		}
	}

	function conf__cuadro_layout_impresion(toba_ei_cuadro $cuadro)
	{
		$cursor = $this->get_entidad()->tabla('pantallas')->get_cursor();
		$cuadro->seleccionar(array(apex_datos_clave_fila => $cursor));
		$filas = $this->get_entidad()->tabla('pantallas')->get_filas();
		foreach (array_keys($filas) as $id) {
			$filas[$id]['template'] = (!isset($filas[$id]['template_impresion']) || trim($filas[$id]['template_impresion']) == '') ? 0 : 1;
		}
		$cuadro->set_datos($filas);
	}

	function evt__cuadro_layout_impresion__seleccion($id)
	{
		$this->pant_sel_temp = $id;
	}

	function conf__form_layout_impresion(toba_ei_formulario $form)
	{
		$vinculo = toba::vinculador()->get_url(null, null, array(), array('servicio' => 'ejecutar'));
		$form->ef('template')->set_botonera('Layout');
		$form->ef('template')->set_alto('400px');		
		$form->ef('template')->set_path_template(array($vinculo));		
		$datos = $this->get_entidad()->tabla('pantallas')->get();
		unset($datos['template']);
		if (isset($datos['template_impresion']) && trim($datos['template_impresion']) != '') {
			$datos['tipo_layout'] = 'L';
			$datos['template'] = $datos['template_impresion'];
		}
		$form->set_datos($datos);
	}

	function evt__form_layout_impresion__modificacion($datos)
	{
		if (!isset($datos['tipo_layout'])) {
			$datos['template_impresion'] = null;
		} else {
			$datos['template_impresion'] = $datos['template'];	//Cuando existe un template lo paso al otro campo
			unset($datos['template']);												//asi puedo reusar el objeto
		}
		$this->get_entidad()->tabla('pantallas')->set($datos);
	}

	function evt__5__salida()
	{	//Limpio cursor y variable temporal para que no influya en la pantalla de layout de la pantalla
		unset($this->pant_sel_temp);
		$this->get_entidad()->tabla('pantallas')->resetear_cursor();
	}

	function get_tipos_layout()
	{
		return array(
			array('clave' => 'L', 'valor' => 'Usando template')
		);
	}	
	
	function servicio__ejecutar()
	{
		//Determina si el ejecutar es por este ci o por el del parent 
 		$imagen = toba::memoria()->get_parametro('imagen');
		if (isset($imagen)) {
 			return parent::servicio__ejecutar();		
 		}
 		$url = toba::proyecto()->get_www('img/fck_templates/') ;
		$salida = "
		CKEDITOR.addTemplates('default', 
		{
			imagesPath: '{$url['url']}', \n
			templates: \n
				[ \n
					{ \n
						title: 'Lineal', \n
						image: 'tabla_1_col.gif', \n
						description: 'Un componente debajo del otro, separado por una línea, es el layout original' , \n
						html: '{$this->get_template_lineal()}' \n
					},\n
					{ \n
						title: 'Tabla Lineal', \n
						image: 'tabla_1_col.gif', \n
						description: 'Tabla con un componente debajo del otro' , \n
						html: '{$this->get_template_columnas(1)}' \n
					},\n
					{ \n
						title: 'Tabla Dos Columnas',  \n
						image: 'tabla_2_col.gif', \n
						description: 'Se arma una tabla tomando los componentes en el orden definido, incluyendo dos por fila' ,  \n
						html: '{$this->get_template_columnas(2)}' \n
					} \n
				] \n
		}); ";
		echo $salida;
	}	
	
	protected function get_template_lineal()
	{
		$salida = '';
		$busqueda = $this->get_entidad()->tabla('objetos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$deps = $busqueda->buscar_filas();
		$deps = rs_ordenar_por_columna($deps, 'orden');
		$existe_previo = false;
		foreach ($deps as $dep) {
			if ($existe_previo) {
				$salida .= "<hr>";
			}
			$salida .= '[dep id='.$dep['dependencia'].']';
			$existe_previo = true;
		}
		return $salida;
	} 	
	
	protected function get_template_columnas($columnas)
	{
		$salida = '<table>';
		$busqueda = $this->get_entidad()->tabla('objetos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('pantallas', $this->get_pant_actual());
		$deps = $busqueda->buscar_filas();
		$deps = rs_ordenar_por_columna($deps, 'orden');
		$i = 0;
		$total = count($deps);
		foreach ($deps as $dep) {
			if ($i % $columnas == 0) {
				$salida .= '<tr>';
			}				
			$salida .= '<td>[dep id='.$dep['dependencia'].']</td>';
			$i++;
			if ($i % $columnas == 0) {
				$salida .= '</tr>';
			}				
		}
		$salida .= '</table>';
		return $salida;		
	}
		
	
	// *******************************************************************
	// *******************  tab EVENTOS  *********************************
	// *******************************************************************
	/*
		Metodos necesarios para que el CI de eventos funcione
	*/

	function evt__3__salida()
	{
		$this->dependencia('eventos')->limpiar_seleccion();
	}

	function get_dbr_eventos()
	{
		return $this->get_entidad()->tabla('eventos');
	}
	
	function get_eventos_estandar($modelo)
	{
		return toba_ci_info::get_lista_eventos_estandar($modelo);
	}



	/**
	 * Se actualiza la aparicion de un evento en las pantallas dadas
	 *
	 * @param array $pant_presentes Pantallas en las que el evento aparece (si es null se asumen todas)
	 * @param string $evento Identificador del evento (ej: procesar)
	 */
	function set_pantallas_evento($pant_presentes, $evento)
	{
		$pant_disponibles = $this->get_entidad()->tabla('pantallas')->get_id_filas();
		$busqueda = $this->get_entidad()->tabla('eventos_pantalla')->nueva_busqueda();
		foreach ($pant_disponibles as $pantalla_id) {
			//Busco el evento en la pantalla para ver si ya esta.
			$busqueda->set_padre('pantallas', $pantalla_id);
			$busqueda->set_condicion('identificador', '==', $evento);
			$id_evt = $busqueda->buscar_ids();
			$evento_esta = (! empty($id_evt));

			//Miro si la pantalla esta entre las presentes
			$pantalla = $this->get_entidad()->tabla('pantallas')->get_fila_columna($pantalla_id, 'identificador');
			$evento_debe_estar = (is_null($pant_presentes) || in_array($pantalla, $pant_presentes));


			if ($evento_debe_estar && !$evento_esta) {
				//Hay que agregarlo
				$this->get_entidad()->tabla('pantallas')->set_cursor($pantalla_id);
				$id_evt = $this->get_entidad()->tabla('eventos')->get_id_fila_condicion(array('identificador' => $evento));
				$this->get_entidad()->tabla('eventos')->set_cursor(current($id_evt));
				$this->get_entidad()->tabla('eventos_pantalla')->nueva_fila(array('identificador' => $evento));
			} elseif (!$evento_debe_estar && $evento_esta) {
				//Hay que eliminarlo de la pantalla
				$this->get_entidad()->tabla('eventos_pantalla')->eliminar_fila(current($id_evt));
			}
		}
		$this->get_entidad()->tabla('eventos')->resetear_cursor();
	}
	
	/**
	 * Retorna las pantallas en las que esta incluido el evento
	 */
	function get_pantallas_evento($evento)
	{
		//Busco el id del evento y lo seteo como cursor para la busqueda
		$id_evento = $this->get_entidad()->tabla('eventos')->get_id_fila_condicion(array('identificador' => $evento));
		$busqueda = $this->get_entidad()->tabla('eventos_pantalla')->nueva_busqueda();
		$busqueda->set_padre('eventos', current($id_evento));
		$ids_eventos_p = $busqueda->buscar_ids();
		
		//Agrego todas las pantallas para las cuales el evento es valido
		$pantallas = array();
		foreach ($ids_eventos_p as $id_p) {
			$datos_p = $this->get_entidad()->tabla('eventos_pantalla')->get_fila($id_p);
			//Busco el Id de la pantalla, accediendo al padre porque si es fila nueva aun no esta seteado en eventos_pantalla
			$id_pant = $this->get_entidad()->tabla('eventos_pantalla')->get_id_padres(array($id_p), 'pantallas');
			$pantallas[] = $this->get_entidad()->tabla('pantallas')->get_fila_columna(current($id_pant), 'identificador');
		}
		return $pantallas;
	}
	
	function get_pantallas_posibles()
	{
		$pantallas = $this->get_entidad()->tabla('pantallas')->get_filas();
		//Se contruye un nombre mas completo
		foreach (array_keys($pantallas) as $pant) {
			$pantallas[$pant]['nombre'] = '('.$pantallas[$pant]['identificador'].') '.$pantallas[$pant]['etiqueta'];
		}
		return $pantallas;
	}
}
?>
