<?php
// El cuadro se tiene que llamar "browse"
// El formulario se tiene que llamar "form"
// La pantalla 1 se tiene que llamar "browse"
// La pantalla 2 se tiene que llamar "form"

/*
	Esto es una subparte de la transaccion y no tiene
	que sincronizar datos con la DB 
	(eso lo tiene que hacer el contenedor)
*/
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_Browse_Form_DatosTabla extends objeto_ci {
	
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
		$this->modo = "form";
	}

	function get_etapa_actual(){
		$etapa = "browse";
		if(isset($this->modo)){
			if($this->modo == "form") {
				$etapa = "form";
			}
		}
		return $etapa;		
	}
	
	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__volver() {
		unset($this->modo);
		unset($this->seleccion);			
	}

///////////// Cuadro //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__browse__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "form";  
	}
	
	// Esto va en la segunda
	function evt__browse__carga() {
		return $this->get_tabla()->get_filas();
	}
	
	
///////// Formulario /////////////

	function evt__form__alta($datos) {
		$this->get_tabla()->nueva_fila($datos); // shortcut para nueva_fila
		$this->evt__volver();
	}
	
	function evt__form__carga() {
		if(isset($this->seleccion)) {
			return $this->get_tabla()->get_fila($this->seleccion); 		
		}
	}	
	
	function evt__form__modificacion($datos) {
		$this->get_tabla()->modificar_fila($this->seleccion, $datos);
		$this->evt__volver();		
	}
	
	function evt__form__baja() {
		$this->get_tabla()->eliminar_fila($this->seleccion);
		$this->evt__volver();		
	}	
}
?>