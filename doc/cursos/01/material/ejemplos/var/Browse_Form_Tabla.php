<?php
// El cuadro se tiene que llamar "browse"
// El formulario se tiene que llamar "form"
// La pantalla 1 se tiene que llamar "pant_browse"
// La pantalla 2 se tiene que llamar "pant_form"

require_once("nucleo/browser/clases/objeto_ci.php");

class Browse_Form_Tabla extends objeto_ci {
	
	protected $modo;
	protected $seleccion;
	protected $tabla;
	
//////////// CI ////////////////

	function mantener_estado_sesion() {
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "modo";
		$propiedades[] = "seleccion";
		return $propiedades;
	}

	function evt__agregar() {
		$this->modo = "pant_form";
	}

	function get_etapa_actual() {
		if($this->modo == "pant_form") {
			return "pant_form";
		}
		return "pant_browse";		
	}
	
	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__volver() {
		unset($this->modo);
		unset($this->seleccion);			
	}

///////////// Browse //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__browse__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "pant_form";  
	}
	
	// Esto va en la segunda
	function evt__browse__carga() {
		return $this->get_tabla()->get_filas();
	}
	
	
///////// Form /////////////

	function evt__form__alta($datos) {
		$this->get_tabla()->set($datos); // shortcut para nueva_fila
		$this->get_tabla()->sincronizar();		
		$this->evt__volver();
	}
	
	function evt__form__carga() {
		if(isset($this->seleccion)) {
			return $this->get_tabla()->get_fila($this->seleccion); 		
		}
	}	
	
	function evt__form__modificacion($datos) {
		$this->get_tabla()->set($datos);
		$this->get_tabla()->sincronizar();
		$this->evt__volver();		
	}
	
	function evt__form__baja() {
		$this->get_tabla()->eliminar_filas();		
		$this->get_tabla()->sincronizar();
		$this->evt__volver();		
	}	
}
?>