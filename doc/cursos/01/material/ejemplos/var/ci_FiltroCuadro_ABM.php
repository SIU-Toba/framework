<?php
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_FiltroCuadro_ABM extends objeto_ci {
	
	protected $modo;
	protected $seleccion;
	protected $estado_filtro;
	private $tabla;
	
//////////// CI ////////////////

	private function get_tabla() {
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("persisitencia");
			$this->tabla = $this->dependencias["persisitencia"];			
		}
		return $this->tabla;		
	}

	function mantener_estado_sesion() {
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "modo";
		$propiedades[] = "seleccion";
		$propiedades[] = "estado_filtro";
		return $propiedades;
	}

	function evt__agregar() {
		$this->modo = "form";
	}

	function destruir() {
		parent::destruir();
		ei_arbol($this->get_estado_sesion());
		ei_arbol($this->get_tabla()->get_datos());		
	}
	
	function get_etapa_actual() {
		if($this->modo == "form") {
			return "form";
		}
		return "browse";		
	}
	
	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__volver() {
		unset($this->modo);
		unset($this->seleccion);			
		$this->get_tabla()->resetear();			
	}

///////////// Cuadro //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__cuadro__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "form";  
	}
	
////////////// Filtro ////////////////

	function evt__filtro__filtrar($datos) {
		$this->estado_filtro = $datos;				
	}	
	
	function evt__filtro__carga() {
		return $this->estado_filtro;
	}
	
	function evt__filtro__cancelar() {
		unset($this->estado_filtro);
	}
	
///////// Formulario /////////////

	function evt__formulario__alta($datos) {
		$this->get_tabla()->set($datos); // shortcut para nueva_fila
		$this->get_tabla()->sincronizar();		
		$this->evt__volver();
	}
	
	function evt__formulario__carga() {
		if(isset($this->seleccion)) {
			$this->get_tabla()->cargar(array("tipo_tematica_id"=>$this->seleccion));
			return $this->get_tabla()->get(); 		
		}
	}	
	
	function evt__formulario__modificacion($datos) {
		$this->get_tabla()->set($datos);
		$this->get_tabla()->sincronizar();
		$this->evt__volver();		
	}
	
	function evt__formulario__baja() {
		$this->get_tabla()->eliminar_filas();		
		$this->get_tabla()->sincronizar();
		$this->evt__volver();		
	}	
}
?>
