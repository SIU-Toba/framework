<?php
require_once("nucleo/browser/clases/objeto_ci.php");

class ci_navegacionMD extends objeto_ci {
	
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
		$this->modo = "form";
	}

	function destruir() {
		parent::destruir();
//		ei_arbol($this->get_estado_sesion());		
	}
	
	function get_etapa_actual() {
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
	}
	
	// Antes de generar la interface la pantalla 2 (que se llama form)
	function evt__pre_cargar_datos_dependencias__form() {
		if($this->nueva_seleccion) { // ver el comentario en el metodo seleccion
			$this->dependencias["editor"]->cargar($this->seleccion);
		}
	}

///////////// Cuadro //////////////

	// Este se ejecuta en la pasada de "procesar eventos" (va primera)
	function evt__cuadro__seleccion($id) {
		$this->seleccion = $id;
		$this->modo = "form";  
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
