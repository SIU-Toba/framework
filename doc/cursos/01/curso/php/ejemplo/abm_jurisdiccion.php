<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class abm_jurisdiccion extends objeto_ci
{
	protected $seleccion;
	protected $filtro;
	protected $modo = 'hola';
	private $tabla;

	private function get_tabla() 
	{
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("datos");
			$this->tabla = $this->dependencias["datos"];			
		}
		return $this->tabla;		
	}
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion";
		$propiedades[] = "filtro";
		$propiedades[] = "modo";
		return $propiedades;
	}

	function extender_objeto_js()
	{
	}

	function get_pantalla_actual()
	{
		return $this->modo;	
	}
	//-------------------------------------------------------------------
	//--- Eventos
	//-------------------------------------------------------------------

	//----------------------------- cuadro -----------------------------
	function evt__cuadro__carga()
	{
		if(isset($this->filtro)){
			return consulta::get_jurisdicciones_filtro($this->filtro['descripcion']);
		}
		else
		{
			return consulta::get_jurisdicciones();
		}

	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->seleccion = $seleccion;
		
		// lo mando a la segunda pantalla.
		$this->modo = 'chau';
		
	}

	function evt__alta()
	{
		$this->modo = 'chau';
	}

	function evt__volver()
	{
		// lo vuelvo a la primer pantalla
		$this->modo = 'hola';
	}
	
	//----------------------------- form -----------------------------
	function reset()
	{
		$this->get_tabla()->resetear();
		unset($this->seleccion);
	}
	
	function evt__form__carga()
	{
		if(isset($this->seleccion)){
			$clave['jurisdiccion'] = $this->seleccion;
			$t = $this->get_tabla();
			$t->cargar($clave);
			return $t->get();
		}
	}

	function evt__form__modificacion($registro)
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->set($registro);
			$t->sincronizar();
			$this->reset();
		}
	}

	function evt__form__grabar($registro)
	{
		$t = $this->get_tabla();
		$t->nueva_fila($registro);
		try{
			$t->sincronizar();
			$this->reset();
		}catch(excepcion_toba $e){
			toba::get_cola_mensajes()->agregar('Error insertando la jurisdicción.');
			toba::get_logger()->error( $e->getMessage() );
		}
	}

	function evt__form__baja()
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->eliminar_filas();
			$t->sincronizar();
			$this->reset();
			
			// lo vuelvo a la primer pantalla
			$this->modo = 'hola';
		}
	}

	//----------------------------- filtro -----------------------------
	function evt__filtro_jurisdiccion__cancelar()
	{
		$this->reset();
	}

	function evt__filtro_jurisdiccion__carga()
	{
		return $this->filtro;
	}

	function evt__filtro_jurisdiccion__filtrar($filtro)
	{
		$this->filtro=$filtro;
	}


}

?>