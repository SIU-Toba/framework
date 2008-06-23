<?php 
class ci_restricciones_funcionales extends toba_ci
{
	protected $s__arbol_cargado = false;	
	protected $s__restriccion = -1;
	
	function evt__guardar()
	{
		$raices = $this->dep('arbol')->get_datos();
		if ($this->dep('restricciones')->esta_cargada()) {
			$alta = false;
		}else{
			$alta = true;
		}
		$this->dep('restricciones')->get_persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		$this->dep('restricciones')->sincronizar();
		if ($alta) {
			$restriccion = toba::db()->recuperar_secuencia('apex_restriccion_funcional_seq');	
		}		
		foreach($raices as $raiz) {
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
		$proyecto = toba_editor::get_proyecto_cargado();
		$this->dep('restricciones')->get_persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		$sql = array();
		$sql[] = "DELETE FROM apex_restriccion_funcional_ef 
				  WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_pantalla
				  WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_evt
				  WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_ei
				  WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		$sql[] = "DELETE FROM apex_restriccion_funcional_cols 
				  WHERE restriccion_funcional = '$this->s__restriccion' and proyecto = '$proyecto';";
		toba::db()->ejecutar($sql);
		$this->dep('restricciones')->eliminar_fila($this->dep('restricciones')->get_cursor());
		$this->dep('restricciones')->sincronizar();
		toba::db()->cerrar_transaccion();
		
		$this->cortar_arbol();
		$this->set_pantalla('seleccion');
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
		$proyecto = toba_editor::get_proyecto_cargado();
		$datos = toba_info_permisos::get_restricciones_proyecto($proyecto);
		$componente->set_datos($datos);
	}

	//---------------------------------------------------------------------
	//------  FORM
	//---------------------------------------------------------------------
		
	function conf__form_restriccion($componente)
	{
		if ($this->dep('restricciones')->esta_cargada()) {
			$datos = $this->dep('restricciones')->get();	
		}else{
			$datos['proyecto'] = toba_editor::get_proyecto_cargado();
		}
		$componente->set_datos($datos);
	}
	
	function evt__form_restriccion__modificacion($datos)
	{
		if ($this->dep('restricciones')->esta_cargada()) {
			$this->dep('restricciones')->set($datos);	
		}else{
			$this->dep('restricciones')->nueva_fila($datos);	
		}
	}	

	
	//---------------------------------------------------------------------
	//------  ARBOL
	//---------------------------------------------------------------------
	
	function conf__arbol(arbol_restricciones_funcionales $arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_restricciones_funcionales(toba_editor::get_proyecto_cargado(), $this->s__restriccion );
			$catalogador->cargar();
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
	
}

?>