<?php
// El filtro se tiene que asociar como "filtro" (puede no estar)
// El cuadro se tiene que asociar como "browse"
// El formulario se tiene que asociar como "form"
// La pantalla 1 se tiene que llamar "pant_browse"
// La pantalla 2 se tiene que llamar "pant_form"
// Tiene que haber un objeto de persistencia de tipo datos_tabla que se tiene que asociar
//   en la lista de dependencias con el nombre "persistencia"

require_once("nucleo/browser/clases/objeto_ci.php");

class FiltroBrowse_Form extends objeto_ci {
	
	protected $modo;
	protected $seleccion;
	protected $estado_filtro;
	private $tabla;
	
//////////// CI ////////////////

	protected function get_tabla() {
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("persistencia");
			$this->tabla = $this->dependencias["persistencia"];			
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
		$this->modo = "pant_form";
	}

	// Solo extenderlo si se quiere agregar algo al final
//	function destruir() {}
	
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
		$this->get_tabla()->resetear();			
	}

///////////// Browse //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__browse__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "pant_form";  
	}
	
	// La tienen que definir los hijos
//	function evt__browse__carga() {}
	
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
	
///////// Form /////////////

	function evt__form__alta($datos) {
		$this->get_tabla()->set($datos); // shortcut para nueva_fila
		$this->get_tabla()->sincronizar();		
		$this->evt__volver();
	}

	// La tienen que definir los hijos	
//	function evt__form__carga() {}
	
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