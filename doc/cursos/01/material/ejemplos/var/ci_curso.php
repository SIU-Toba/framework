<?php
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_curso extends objeto_ci {
	
	protected $modo;
	protected $seleccion;
	private $relacion;

//////////// CI ////////////////

/*
	El problema de perdida de memoria se debia a que el CI no solicitaba
	siempre la relacion sobre la que trabajaba. Cuando un elemento no se
	solicita durante un request entra en el reciclado de memoria y es borrado
	en el request siguiente. Como el objeto relacion mantenia todos los datos
	de la pantalla, su reciclado equivale a perder todo el estado de la relacion
	meter una llamada en el evento inicializar garantiza que siempre se va a mantener
*/

	function evt__inicializar(){
		$this->get_relacion();
	}

	private function get_relacion() {
		if(!isset($this->relacion)) {
			$this->cargar_dependencia("persistencia");
			$this->relacion = $this->dependencias["persistencia"];			
		}
		return $this->relacion;		
	}
	
	function cargar($id) {
		$this->get_relacion()->cargar(array("curso_id" => $id));		
	}

	function mantener_estado_sesion() {
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "modo";
		$propiedades[] = "seleccion";
		return $propiedades;
	}
	
	function evt__guardar() {
		$this->get_relacion()->sincronizar();
		$this->get_relacion()->resetear(); // no está implementado todavía
		$this->controlador->evt__volver();				
	}
	
	function evt__eliminar() {
		$this->get_relacion()->eliminar();
		$this->controlador->evt__volver();				
	}

/////////////// Pantalla 1 /////////////////////

	function evt__form_curso__modificacion($datos) { // (Se dispara por defecto, no está definido en el administrador
		$this->get_relacion()->tabla("curso")->set($datos);		
	}
	
	function evt__form_curso__carga() {
		return $this->get_relacion()->tabla("curso")->get();		
	}		
	
/////////////// Pantalla 2 /////////////////////

	function get_comision() {
		return $this->get_relacion()->tabla("comision");
	}	
	
/////////////// Pantalla 3 /////////////////////	

	function evt__categoria_ml__modificacion($datos) {
		$this->get_relacion()->tabla("categoria")->procesar_filas($datos);		
	}
	
	function evt__categoria_ml__carga() {
		return $this->get_relacion()->tabla("categoria")->get_filas(null,true); // el true quiere decir traeme todas las filas y traeme claves asociativas con las claves internas de la tabla. El ml lo necesita de esa manera.		
	}	
}
?>