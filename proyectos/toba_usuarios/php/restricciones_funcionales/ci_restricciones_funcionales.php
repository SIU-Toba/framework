<?php 
class ci_restricciones_funcionales extends toba_ci
{
	protected $s__arbol_cargado = false;	
	protected $s__filtro;
	protected $s__restriccion = -1;
	
	function conf__seleccion()
	{
		if (!isset($this->s__filtro)) {
			$this->pantalla('seleccion')->eliminar_evento('agregar');
		}
	}

	function conf__edicion(){
		$img_oculto = toba_recurso::imagen_toba('error.png', true);
		$img_visible = toba_recurso::imagen_toba('vacio.png', true);
		$img_solo_lectura = toba_recurso::imagen_toba('editar.gif', true);
		$img_editable = toba_recurso::imagen_toba('no_editar.gif', true);
		$titulo = 'Ayuda: ';
		$titulo .= $img_visible.": Visible ".$img_oculto.": Oculto ";
		$titulo .= $img_solo_lectura.": No Editable ".$img_editable.": Editable";
		$this->pantalla('edicion')->set_descripcion($titulo);
	}

	function conf__arbol(arbol_restricciones_funcionales $arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_restricciones_funcionales( $this->s__filtro['proyecto'], $this->s__restriccion );
			$catalogador->cargar();
			$raiz = $catalogador->buscar_carpeta_inicial();
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
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
		$proyecto = $this->s__filtro['proyecto'];
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
	
	function evt__cuadro_restricciones__seleccion($seleccion)
	{
		$this->s__restriccion = $seleccion['restriccion_funcional'];
		$this->dep('restricciones')->cargar($seleccion);
		$this->set_pantalla('edicion');	
	}
	
	function conf__cuadro_restricciones($componente)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas_instancia::get_restricciones_proyecto($this->s__filtro['proyecto']);
			$componente->set_datos($datos);
		}
	}
	
	function conf__form_restriccion($componente)
	{
		if ($this->dep('restricciones')->esta_cargada()) {
			$datos = $this->dep('restricciones')->get();	
		}else{
			$datos['proyecto'] = $this->s__filtro['proyecto'];
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
	
	function cortar_arbol()
	{
		unset($this->s__arbol_cargado);
		$this->s__restriccion = -1;	
	}
	
}

?>