<?php 
class ci_restricciones_funcionales extends toba_ci
{
	protected $s__arbol_cargado = false;	
	protected $s__restriccion = -1;
	protected $s__filtro;
	
	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}		
	}	
	
	function evt__guardar()
	{
		$raices = $this->dep('arbol')->get_datos();
		if ($this->dep('restricciones')->esta_cargada()) {
			$alta = false;
		} else {
			$alta = true;
		}
		$this->dep('restricciones')->persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		$this->dep('restricciones')->sincronizar();
		if ($alta) {
			$restriccion = toba::db()->recuperar_secuencia('apex_restriccion_funcional_seq');	
		}		
		foreach ($raices as $raiz) {
			if ($alta) {
				$raiz->set_restriccion($restriccion);
			}
			$raiz->sincronizar();	
		}
		toba::db()->cerrar_transaccion();
		$this->dep('restricciones')->resetear();
		$this->cortar_arbol();
		$this->set_pantalla('seleccion');
	}
	
	function evt__agregar()
	{
		$this->set_pantalla('edicion');
	}
	
	function evt__cancelar()
	{
		$this->dep('restricciones')->resetear();
		$this->cortar_arbol();
		$this->set_pantalla('seleccion');
	}
	
	function evt__eliminar()
	{
		$proyecto = $this->get_proyecto_seleccionado();
		
		$this->dep('restricciones')->persistidor()->desactivar_transaccion();
		
		toba::db()->abrir_transaccion();
		
		$sql = array();
		$sql[] = "DELETE FROM apex_restriccion_funcional_ef WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_pantalla WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_evt WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_ei WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_cols WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		
		toba::db()->ejecutar($sql);
		
		$this->dep('restricciones')->eliminar_fila($this->dep('restricciones')->get_cursor());
		$this->dep('restricciones')->sincronizar();
		
		toba::db()->cerrar_transaccion();
		
		$this->cortar_arbol();
		$this->set_pantalla('seleccion');
	}
	
	//---------------------------------------------------------------------
	//------  FILTRO
	//---------------------------------------------------------------------
	
	function evt__filtro_proyectos__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro_proyectos__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro_proyectos($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}
	
	function conf__seleccion() 
	{
		if (! isset($this->s__filtro)) {
			$this->pantalla()->eliminar_evento('agregar');
			$this->pantalla()->eliminar_dep('cuadro_restricciones');
		}	
	}
	
	//---------------------------------------------------------------------
	//------  CUADRO
	//---------------------------------------------------------------------
	
	function evt__cuadro_restricciones__seleccion($seleccion)
	{
		$this->s__restriccion = $seleccion['restriccion_funcional'];
		$this->dep('restricciones')->cargar($seleccion);
		$this->set_pantalla('edicion');	
	}
	
	function conf__cuadro_restricciones($componente)
	{
		if (isset($this->s__filtro)) {
			$datos = toba_info_permisos::get_restricciones_proyecto($this->get_proyecto_seleccionado());
			$componente->set_datos($datos);	
		}		
	}

	//---------------------------------------------------------------------
	//------  FORM
	//---------------------------------------------------------------------
		
	function conf__form_restriccion(toba_ei_formulario $componente)
	{
		if ($this->dep('restricciones')->esta_cargada()) {
			$datos = $this->dep('restricciones')->get();
			
			if (toba::instalacion()->es_produccion() && !$datos['permite_edicion']) {
				$this->pantalla()->eliminar_evento('guardar');
				$this->pantalla()->eliminar_evento('eliminar');
			}
		} else {
			$datos['proyecto'] = $this->get_proyecto_seleccionado();
		}
		$componente->set_datos($datos);
		
		if (toba::instalacion()->es_produccion()) {
			$componente->desactivar_efs(array('restriccion_funcional', 'permite_edicion'));
			
		}
	}
	
	function evt__form_restriccion__modificacion($datos)
	{
		if ($this->dep('restricciones')->esta_cargada()) {
			$this->dep('restricciones')->set($datos);	
		} else {
			$this->dep('restricciones')->nueva_fila($datos);	
		}
	}	

	
	//---------------------------------------------------------------------
	//------  ARBOL
	//---------------------------------------------------------------------
	
	function conf__arbol(arbol_restricciones_funcionales $arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_restricciones_funcionales($this->get_proyecto_seleccionado(), $this->s__restriccion );
			$catalogador->set_expandir_dependencias_sin_pantalla(false);						//Esto puede cambiar si es necesario
			$catalogador->cargar(null);
			$raiz = $catalogador->buscar_carpeta_inicial();
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	/**
	 * Método que se invoca en el pedido AJAX, se busca el nodo en cuestión, se lo carga en profundidad y se lo retorna
	 */
	function evt__arbol__cargar_nodo($id)
	{
		$raiz = $this->dep('arbol')->get_datos();
		$nodo = $this->buscar_nodo($id, current($raiz));
		if (isset($nodo)) {
			$nodo->cargar_hijos();
			return array($nodo);
		}
	}	
	
	function buscar_nodo($id_nodo, $padre)
	{
		if ($padre->get_id() == $id_nodo) {
			return $padre;
		}
		foreach ($padre->get_hijos() as $hijo) {
			$encontrado = $this->buscar_nodo($id_nodo, $hijo);
			if (isset($encontrado)) {
				return $encontrado;
			}
		}
	}
	
	function cortar_arbol()
	{
		unset($this->s__arbol_cargado);
		$this->s__restriccion = -1;	
	}
	
	function get_proyecto_seleccionado()
	{
		return $this->s__filtro['proyecto'];
	}
	
}

?>
