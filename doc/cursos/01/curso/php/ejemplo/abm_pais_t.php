<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class abm_pais_t extends objeto_ci
{
	protected $seleccion;
	private $tabla;
	
	private function get_tabla() 
	{
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("datos");
			$this->tabla = $this->dependencias["datos"];			
		}
		return $this->tabla;
	}

	function evt__inicializar()
	{
		$t = $this->get_tabla();
		$t->cargar();
	}

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "seleccion";
		return $propiedades;
	}

	//-------------------------------------------------------------------
	//--- Eventos
	//-------------------------------------------------------------------

	//----------------------------- cuadro -----------------------------

	function evt__cuadro__carga()
	{
		return $this->get_tabla()->get_filas();
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->seleccion = $seleccion;
	}

	//----------------------------- formulario -----------------------------

	function reset()
	{
		$this->get_tabla()->resetear();
		unset($this->seleccion);
	}

	function evt__formulario__carga()
	{
		if(isset($this->seleccion)){
			$clave['idpais'] = $this->seleccion;
			return $t->get();
		}
	}

	function evt__formulario__modificacion($datos)
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->set($datos);
			$t->sincronizar();
			$this->reset();
		}
	}

	function evt__formulario__alta($datos)
	{
		$t = $this->get_tabla();
		$t->nueva_fila($datos);
		try{
			$t->sincronizar();
			$this->reset();
		}catch(toba_excepcion $e){
			toba::get_cola_mensajes()->agregar('Error insertando');
			toba::get_logger()->error( $e->getMessage() );
		}
	}

	function evt__formulario__baja()
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->eliminar_filas();
			$t->sincronizar();
			$this->reset();
		}
	}

	function evt__formulario__cancelar()
	{
		$this->reset();		
	}
}
?>