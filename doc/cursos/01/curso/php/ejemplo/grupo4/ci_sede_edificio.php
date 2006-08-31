<?php
require_once('nucleo/browser/clases/objeto_ci.php'); 
//----------------------------------------------------------------
class ci_sede_edificio extends objeto_ci
{

protected $filtro;
protected $pantalla_actual = "lista_sedes";   
protected $seleccion;
private $relacion;

	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = "filtro";
		$propiedades[] = "pantalla_actual";
		$propiedades[] = "seleccion";
		return $propiedades;
	}

	function get_pantalla_actual()
	{
		return $this->pantalla_actual;
    }

    function evt__volver() {
    	$this->pantalla_actual = "lista_sedes";
    }

    function evt__grabar() 
    {
	try{
			$t = $this->get_relacion();
			$t->sincronizar();
		}catch(toba_excepcion $e){
			toba::get_cola_mensajes()->agregar('Error insertando');
			toba::get_logger()->error( $e->getMessage() );
		}     
    	
    }

	private function get_relacion()
	{
		if(!isset($this->relacion)) {
			$this->cargar_dependencia("datos");
			$this->relacion = $this->dependencias["datos"];			
		}
		return $this->relacion;		
	}

	//-------------  Dependencias -----------------

	//################# Pantalla 1 ######################3

	//-------------  FILTRO -----------------

    function evt__filtro_instituciones__filtrar($filtro) {
    	$this->filtro = $filtro;
    }

    function evt__filtro_instituciones__cancelar() {
      	$this->filtro = null;
    }


    function evt__filtro_instituciones__carga() {
    	if (isset($this->filtro)) {
    	   return $this->filtro;	
    	}
    }

	//-------------  CUADRO -----------------

	function evt__cuadro_sedes__carga()
	{
	if (isset($this->filtro)) 
		return consulta::get_sedes_filtrado($this->filtro);
	}

    function evt__cuadro_sedes__seleccion($seleccion)
    {
    	$this->pantalla_actual = "detalle_sedes";	
   		$this->seleccion = $seleccion;	
   		$this->get_relacion()->cargar($this->seleccion);
    }

	//################# Pantalla 2 ######################3

	//-------------  Form SEDE -----------------
	
	function evt__cabecera_sede__carga() {
	
		if(isset($this->seleccion)){
			$t = $this->get_relacion()->tabla('sede');
			return $t->get();
		}		
	}

    function evt__cabecera_sede__modificacion($datos) {
    	$this->get_relacion()->tabla('sede')->set($datos);
    }

	//-------------  Form EDIFICIOS -----------------

    function evt__lista_edificios__carga() {
    if(isset($this->seleccion)){
		$t = $this->get_relacion()->tabla('edificios');
		return $t->get_filas(null, true);
	}
	}

    function evt__lista_edificios__modificacion($datos) {
    	$this->get_relacion()->tabla('sede')->procesar_filas($datos);
    }

	//-------------------------------
}
?>