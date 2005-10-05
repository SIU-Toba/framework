<?php

// El CI que contiene la segunda pantalla se debe llamar "editor"
// La pantalla 1 tiene el fitro y el cuadro
// El filtro se tiene que asociar como "filtro" (puede no estar)
// El cuadro se tiene que asociar como "browse"
// La pantalla 1 se tiene que llamar "pant_browse"
// La pantalla 2 se tiene que llamar "pant_form"

require_once("nucleo/browser/clases/objeto_ci.php");

class MasterDetail extends objeto_ci {
	
	protected $modo;
	protected $seleccion;
	protected $estado_filtro;
	private $nueva_seleccion = false;
	
//////////// CI ////////////////

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

//  Se redefine unicamente si se agrega nuevo comportamiento al final.
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
	}
	
	// Antes de generar la interface la pantalla 2 (que se llama pant_form)
	function evt__pre_cargar_datos_dependencias__pant_form() {
		if($this->nueva_seleccion) { // ver el comentario en el metodo seleccion
			$this->dependencias["editor"]->cargar($this->seleccion);
		}
	}

///////////// Browse //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__browse__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "pant_form";  
		$this->nueva_seleccion = true; // Es un flag para que sólo se dispare la carga de la segunda etapa con este evento (como no se persiste, cuando navego entre los tabs del segundo se pierde)
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
}
?>
