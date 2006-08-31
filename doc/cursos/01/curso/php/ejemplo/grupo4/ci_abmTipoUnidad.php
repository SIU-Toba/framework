<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_abmTipoUnidad extends objeto_ci
{
    protected $tabla;
    protected $seleccion;
    protected $filtro;
    protected $pantalla_actual = "pantallaABM";   	
    
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
		$propiedades[] = "pantalla_actual";
		return $propiedades;
	}

	function reset()
	{
		$this->get_tabla()->resetear();
		unset($this->seleccion);
		$this->pantalla_actual = "pantallaABM";   	
	}	
	
/*
	function destruir()
	{
		parent::destruir();
		ei_arbol( $this->get_estado_sesion() );
	}*/
	

//Cuadro
	function evt__cuadroPantalla__carga()
	{
		//ei_arbol(consulta::get_tipounidad());
		return consulta::get_tipounidad($this->filtro);
	}
	
		
	
     
	
	function get_pantalla_actual()
	{
		return $this->pantalla_actual;
    }

    function evt__cuadroPantalla__seleccion($seleccion)
    {
    	$this->pantalla_actual = "pantallaDetalle";	
   		$this->seleccion = $seleccion;	
    }
    
    function evt__agregar()
    {
    	unset($this->filtro);
   		$this->pantalla_actual = "pantallaDetalle";	
    }

    function evt__volver()
    {
   		$this->reset();
    }

//Formulario

	function evt__ABMFormTipoUA__carga()
	{
		if(isset($this->seleccion)){
			$clave['tipoua'] = $this->seleccion;
			$t = $this->get_tabla();
			$t->cargar($clave);
			return $t->get();
		}
	}

    function evt__ABMFormTipoUA__alta($datos) {
		echo('chau');
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
    
    
	function evt__ABMFormTipoUA__baja()
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->eliminar_filas();
			$t->sincronizar();
			$this->reset();
		}
	}
    
  
    function evt__ABMFormTipoUA__modificacion($datos)
	{
		if(isset($this->seleccion)){
			$t = $this->get_tabla();
			$t->set($datos);
			$t->sincronizar();
			$this->reset();
		}
	}

	function evt__ABMFormTipoUA__cancelar()
	{
		$this->reset();		
	}

//Filtro
    function evt__filtrog4__filtrar($filtro) {
    	$this->filtro = $filtro;
		//return consulta::get_tipounidad($this->filtro);    	
    	//$this->reset();
    }

    function evt__filtrog4__cancelar() {
      	$this->filtro = null;
    }
 
    function evt__filtrog4__carga() {
    	if (isset($this->filtro)) {
    	   return $this->filtro;	
    	}
    }

}

?>