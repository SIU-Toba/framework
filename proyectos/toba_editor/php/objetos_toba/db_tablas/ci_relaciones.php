<?php
require_once(toba_dir().'/php/3ros/Graph/Graph.php');	//Necesario para el calculo de orden topologico de las tablas

class ci_relaciones extends toba_ci
{
	protected $s__seleccion_relacion;
	protected $s__seleccion_relacion_anterior;
	private $id_intermedio_relaciones;
	private $rel_activa_padre;
	private $rel_activa_hijo;

	function get_entidad()
	{
		return $this->controlador->get_entidad();
	}

	function mostrar_detalle_relacion()
	{
		if (isset($this->s__seleccion_relacion)) {
			return true;
		}
		return false;
	}

	function limpiar_seleccion()
	{
		unset($this->s__seleccion_relacion);
		unset($this->s__seleccion_relacion_anterior);
	}

	function evt__cancelar()
	{
		$this->limpiar_seleccion();
	}
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
		if ($this->mostrar_detalle_relacion()) {
			$this->get_datos_relacion_activa();
			$this->pantalla()->eliminar_dep('relaciones_esquema');
			$this->dependencia('relaciones_lista')->set_fila_protegida($this->s__seleccion_relacion);
			$this->dependencia('relaciones_lista')->seleccionar($this->s__seleccion_relacion);
		} else {
			$this->pantalla()->eliminar_dep('relaciones_columnas');
		}	
	}

	//-----------------------------------------------------------------------------------
	//---- relaciones_columnas ----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__relaciones_columnas(toba_ei_formulario_ml $form_ml)
	{
		$this->s__seleccion_relacion_anterior = $this->s__seleccion_relacion;
		$this->get_entidad()->tabla('relaciones')->set_cursor($this->s__seleccion_relacion);
		$datos = $this->get_entidad()->tabla('columnas_relacion')->get_filas();
		foreach ($datos as $klave => $valor) {
			$datos[$klave]['columna_padre'] = $valor['padre_clave'];
			$datos[$klave]['columna_hija'] = $valor['hijo_clave'];
		}
		$form_ml->set_datos($datos);
		$this->get_entidad()->tabla('relaciones')->resetear_cursor();
	}

	function evt__relaciones_columnas__modificacion($datos)
	{
		// Primero borro las relaciones existentes para poder dar lugar a las nuevas relaciones.
		$busqueda = $this->get_entidad()->tabla('columnas_relacion')->nueva_busqueda();
		$busqueda->set_padre('relaciones', $this->s__seleccion_relacion_anterior);
		$ids = $busqueda->buscar_ids();
		foreach ($ids as $id) {
			$this->get_entidad()->tabla('columnas_relacion')->eliminar_fila($id);
		}

		//Ahora tengo que dar de alta las relaciones nuevas
		$this->get_entidad()->tabla('relaciones')->set_cursor($this->s__seleccion_relacion_anterior);
		foreach ($datos as $klave => $valor) {
			$datos[$klave]['padre_clave'] = $valor['columna_padre'];
			$datos[$klave]['hijo_clave'] = $valor['columna_hija'];
			unset($datos[$klave]['columna_padre']);
			unset($datos[$klave]['columna_hija']);
			$this->get_entidad()->tabla('columnas_relacion')->nueva_fila($datos[$klave]);
		}
		$this->get_entidad()->tabla('relaciones')->resetear_cursor();
	}

	function evt__relaciones_columnas__aceptar($datos)
	{
		$this->evt__relaciones_columnas__modificacion($datos);
		$this->evt__relaciones_columnas__cancelar();
	}

	function evt__relaciones_columnas__cancelar()
	{
		$this->limpiar_seleccion();
	}
	//-----------------------------------------------------------------------------------
	//---- relaciones_esquema -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__relaciones_esquema(toba_ei_esquema $esquema)
	{
		$tablas = $this->get_entidad()->tabla('dependencias')->get_filas();
		$relaciones = $this->get_entidad()->tabla('relaciones')->get_filas();
		$grafo = toba_datos_relacion::grafo_relaciones($tablas, $relaciones);
		$diagrama = "digraph G { \n";
		$diagrama .= "size=\"7,7\";\n";
		$diagrama .= "node [shape=record];\n";
		foreach ($grafo->getNodes() as $nodo) {
			$datos = $nodo->getData();
			$diagrama .= $datos['identificador']."\n";
			foreach ($nodo->getNeighbours() as $nodo_vecino) {
				$datos_vecino = $nodo_vecino->getData();
				$diagrama .= $datos['identificador'] . ' -> ' . $datos_vecino['identificador'] . "\n";
			}
		}
		$diagrama .= '}';
		$esquema->set_datos($diagrama);
	}
	//-----------------------------------------------------------------------------------
	//---- relaciones_lista -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__relaciones_lista(eiform_abm_detalle $form_ml)
	{
		$datos = array();
		$relaciones = $this->get_entidad()->tabla('relaciones')->get_filas();
		foreach ($relaciones as $key => $valor) {
			$datos[$key] = $this->conversion_fila_a_form($valor);
		}
		$form_ml->set_datos($datos);
		if (isset($this->s__seleccion_relacion)) {
			$form_ml->set_solo_lectura();
		}
	}

	function evt__relaciones_lista__seleccion($id)
	{
		if (isset($this->id_intermedio_relaciones[$id])) {
			$id = $this->id_intermedio_relaciones[$id];
		}
		$this->s__seleccion_relacion = $id;
	}

	function evt__relaciones_lista__modificacion($registros)
	{
		$tabla = $this->get_entidad()->tabla('relaciones');
		foreach (array_keys($registros) as $id)
		{
			$accion = $registros[$id][apex_ei_analisis_fila];
			unset($registros[$id][apex_ei_analisis_fila]);
			switch ($accion) {
				case 'A':
					$fila = $this->conversion_form_a_fila($registros[$id]);
					$this->id_intermedio_relaciones[$id] = $tabla->nueva_fila($fila);
					break;
				case 'B':
					$tabla->eliminar_fila($id);
					break;
				case 'M':
					//Convierto los datos nuevos al formato del dT
					$fila = $this->conversion_form_a_fila($registros[$id]);

					//Antes de modificar la fila tengo que matar las relaciones existentes entre columnas.. de otra forma van a quedar mal asociadas las columnas.
					if ($tabla->es_campo_modificado('padre_objeto', $id, $fila) || $tabla->es_campo_modificado('hijo_objeto', $id, $fila)) {
						$tabla->set_cursor($id);
						$this->get_entidad()->tabla('columnas_relacion')->eliminar_filas(true);
						$tabla->restaurar_cursor();
					}

					//Ahora si modifico la relacion.
					$tabla->modificar_fila($id, $fila);
					break;
			}
		}
		//Se buscan ciclos
		$tablas = $this->get_entidad()->tabla('dependencias')->get_filas();
		$relaciones = $this->get_entidad()->tabla('relaciones')->get_filas();
		if ($this->hay_ciclos($tablas, $relaciones)) {
			$msg = 'El esquema de relaciones actual contiene ciclos. En un esquema con ciclos el'.
			' mecanismo de sincronización no puede encontrar automaticamente un orden sin violar'.
			'las constraints de la BD. Se recomienda deshabilitar el chequeo de constraints hasta el'.
			'final de la transacción.';
			$this->informar_msg($msg, 'info');
		}
	}

	//-------------------------------------------------------------------------------------------
	function get_columnas_padre()
	{
		$datos = toba_info_editores::get_lista_dt_columnas($this->rel_activa_padre);
		return $datos;
	}

	function get_columnas_hija()
	{
		$datos = toba_info_editores::get_lista_dt_columnas($this->rel_activa_hijo);
		return $datos;
	}

	function get_datos_relacion_activa()
	{
		$relacion_activa = $this->get_entidad()->tabla('relaciones')->get_fila($this->s__seleccion_relacion);
		$this->rel_activa_padre = $relacion_activa['padre_objeto'];
		$this->rel_activa_hijo = $relacion_activa['hijo_objeto'];
	}

	function conversion_form_a_fila($datos)
	{		//Adapta el contenido del form a una fila
		//-- PADRE --
		$padre = explode(',', $datos['padre']);
		$datos['padre_id'] = $padre[0];
		$datos['padre_proyecto'] = toba_editor::get_proyecto_cargado();
		$datos['padre_objeto'] = $padre[1];
		unset($datos['padre']);
		//-- HIJO --
		$hijo = explode(',', $datos['hija']);
		$datos['hijo_id'] = $hijo[0];
		$datos['hijo_proyecto'] = toba_editor::get_proyecto_cargado();
		$datos['hijo_objeto'] = $hijo[1];
		unset($datos['hija']);
		return $datos;
	}

	function conversion_fila_a_form($fila)
	{	//Adapta el contenido de una fila al form
		$fila['padre'] = $fila['padre_id'] . ',' . $fila['padre_objeto'];
		$fila['hija'] = $fila['hijo_id'] . ',' . $fila['hijo_objeto'];
		unset($fila['padre_id']);
		unset($fila['padre_objeto']);
		unset($fila['hijo_id']);
		unset($fila['hijo_objeto']);
		return $fila;
	}

	function get_lista_tablas()
	{
		return $this->controlador->get_lista_tablas();
	}

	function hay_ciclos($tablas, $relaciones)
	{
		$tester = new Structures_Graph_Manipulator_AcyclicTest();
		$grafo = toba_datos_relacion::grafo_relaciones($tablas, $relaciones);
		return ! $tester->isAcyclic($grafo);
	}	
}
?>