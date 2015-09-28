<?php

class ci_dimensiones extends toba_ci
{
	protected $s__carga_ok;

	function ini__operacion()
	{
		if ($editable = toba::zona()->get_editable()) {
			$clave['proyecto'] = toba_editor::get_proyecto_cargado();
			$clave['dimension'] = $editable[1];
			$this->s__carga_ok = $this->dependencia('datos')->cargar($clave);
		}			
	}

	function conf()
	{
		if (!$this->s__carga_ok) {
			$this->pantalla()->eliminar_evento('eliminar');
			$this->pantalla()->eliminar_tab('pant_gatillos_d');
			$this->pantalla()->eliminar_tab('pant_gatillos_i');
			$this->pantalla()->eliminar_tab('pant_elementos');
		}
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__guardar()
	{
		$this->dependencia('datos')->sincronizar();
		$clave = $this->dependencia('datos')->tabla('dimension')->get_clave_valor(0);
		$clave_carga[0] = $clave['proyecto'];
		$clave_carga[1] = $clave['dimension'];
		$zona = toba::solicitud()->zona();
		if (! $zona->cargada()) {
			$zona->cargar(array_values($clave_carga));
		}
		$this->s__carga_ok = true;
		admin_util::refrescar_barra_lateral();
	}

	function evt__eliminar()
	{
		$this->dependencia('datos')->eliminar_todo();
		toba::solicitud()->zona()->resetear();
		$this->s__carga_ok = false;
		admin_util::refrescar_barra_lateral();
	}
	
	//-------------------------------------------------------------------
	//--- Propiedades basicas
	//-------------------------------------------------------------------

	function evt__formulario__modificacion($datos)
	{
		$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		$this->dependencia('datos')->tabla('dimension')->set($datos);
	}

	function conf__formulario($form)
	{
		if ($this->s__carga_ok) {
			$form->ef('fuente_datos')->set_solo_lectura();
			$form->set_datos($this->dependencia('datos')->tabla('dimension')->get());
		}
	}

	//-------------------------------------------------------------------
	//--- GATILLOS
	//-------------------------------------------------------------------

	function get_tablas_gatillo($tipo='directo')
	{
		$temp = $this->dependencia('datos')->tabla('gatillos')->get_filas(array('tipo'=>$tipo));
		$utilizadas = array();
		foreach ($temp as $t) {
			$utilizadas[] = $t['tabla_rel_dim'];
		}
		return $utilizadas;
	}

	//--------- DIRECTOS --------------------------

	function evt__form_gatillos_dir__modificacion($datos)
	{
		$tablas_utilizadas = $this->get_tablas_gatillo();
		foreach (array_keys($datos) as $id) {
			$datos[$id]['tipo'] = 'directo';
		}	
		$this->dependencia('datos')->tabla('gatillos')->procesar_filas($datos);
	}

	function conf__form_gatillos_dir($form)
	{
		$gatillos = $this->dependencia('datos')->tabla('gatillos')->get_filas(array('tipo'=>'directo'));
		$form->set_datos($gatillos);
		$form->set_tablas_utilizadas($this->get_tablas_gatillo('indirecto'), 'INDIRECTOS');
	}

	//--------- INDIRECTOS ------------------------

	function conf__pant_gatillos_i($pantalla)
	{
		if (count($this->get_tablas_gatillo('directo')) == 0) {
			$pantalla->set_descripcion('Para definir gatillos INDIRECTOS, es necesario configurar previamente los gatillos DIRECTOS.');
			$pantalla->eliminar_dep('form_gatillos_indir');
		}	
	}

	function evt__form_gatillos_indir__modificacion($datos)
	{
		foreach (array_keys($datos) as $id) {
			$datos[$id]['tipo'] = 'indirecto';
			//control de caminos validos entre tablas
			$camino = array();
			$camino[] = $datos[$id]['tabla_rel_dim'];
			if (isset($datos[$id]['ruta_tabla_rel_dim'])) {
				//Contiene un camino intermedio
				$temp = explode(',', $datos[$id]['ruta_tabla_rel_dim']);
				$temp = array_map('trim', $temp);
				$camino = array_merge($camino, $temp);
			}
			$camino[] = $datos[$id]['tabla_gatillo'];
			//$datos[$id]['ruta_tabla_rel_dim'] = implode(',', $camino);
		}
		$this->dependencia('datos')->tabla('gatillos')->procesar_filas($datos);
	}

	function conf__form_gatillos_indir($form)
	{
		$gatillos = $this->dependencia('datos')->tabla('gatillos')->get_filas(array('tipo'=>'indirecto'));
		$form->set_datos($gatillos);
		$form->set_tablas_utilizadas($this->get_tablas_gatillo('directo'), 'DIRECTOS');
	}

	//-------------------------------------------------------------------
	//-- Combos
	//-------------------------------------------------------------------

	/**
	*	Lista de tablas
	*/
	function get_tablas($fuente)
	{
		return toba::db($fuente['fuente_datos'], toba_editor::get_proyecto_cargado())->get_lista_tablas_y_vistas();
	}
	
	/**
	*	Lista de tablas que pueden usarse como gatillos
	*		No puede utilizarse dos veces la misma tabla como gatillo, por eso las utilizadas se excluyen
	*/
	function get_tablas_gatillos()
	{
		$temp = $this->dependencia('datos')->tabla('dimension')->get();
		$datos = toba_info_editores::get_schemas_fuente(toba_editor::get_proyecto_cargado(), $temp['fuente_datos']);
		$schemas =  (! empty($datos)) ? aplanar_matriz($datos, 'schema'): null;
		$tablas = toba::db($temp['fuente_datos'], toba_editor::get_proyecto_cargado())->get_lista_tablas(true, $schemas);
		return $tablas;
	}
	
	/**
	*	Lista de gatillos directos, para señalarlos desde los indirectos
	*/
	function get_gatillos_directos()
	{
		$datos = $this->dependencia('datos')->tabla('gatillos')->get_filas(array('tipo'=>'directo'));
		$indirectos = array();
		foreach ($datos as $dato) {
			$indirectos[]['gatillo_directo'] = $dato['tabla_rel_dim'];
		}
		return $indirectos;
	}

	//-------------------------------------------------------------------
	//--- Previsualizacion de elementos
	//-------------------------------------------------------------------

	function conf__elementos()
	{
		$datos = $this->dependencia('datos')->tabla('dimension')->get();
		$id = explode(',', $datos['col_id']);
		$desc = explode(',', $datos['col_desc']);
		$sql = 'SELECT ' . implode(" || ' - ' || ", $id) . ' as clave, ' 
						. implode(' || ', $desc) . " as descripcion
				FROM {$datos['tabla']}
				ORDER BY descripcion";
		$datos = toba_editor::db_proyecto_cargado($datos['fuente_datos'])->consultar($sql);
		return $datos;
	}
}
?>