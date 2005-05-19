<?php
require_once("nucleo/browser/clases/objeto_ci_me_tab.php");

class ci extends objeto_ci_me_tab 
{ 
	protected $datos_ml;
	protected $datos_formulario;
	protected $datos_formulario_abm = array();	
	protected $registro_actual;
	protected $datos_filtro;

    function __construct($id) 
    { 
        parent::__construct($id); 
    } 

    function mantener_estado_sesion() 
    { 
        $propiedades = parent::mantener_estado_sesion(); 
        $propiedades[] = "datos_ml"; 
        $propiedades[] = "datos_formulario";
        $propiedades[] = "datos_formulario_abm";
        $propiedades[] = "registro_actual";		
        $propiedades[] = "datos_filtro";
        return $propiedades; 
    } 	

	function obtener_html_contenido__30()
	{
		ei_arbol($this->datos_ml);
		ei_arbol($this->datos_formulario);
		ei_arbol($this->datos_formulario_abm);
	}	
	
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		$eventos['reiniciar']['etiqueta'] = "Reiniciar";
		$eventos['reiniciar']['imagen'] = "";
		$eventos['reiniciar']['confirmacion'] = "";
		$eventos['reiniciar']['estilo']="";
		$eventos['reiniciar']['tip']="Retorna la operación a su estado inicial";		
		return $eventos;
	}	
	
	function evt__post_cargar_datos_dependencias()
	{
		if (isset($this->dependencias['formulario'])) {
			$eventos = $this->dependencias['formulario']->get_lista_eventos();
			$eventos += eventos::evento_estandar('otro_evento', 'Otro Evento');
			$this->dependencias['formulario']->set_eventos($eventos);
		}
		if (isset($this->dependencias['cuadro_abm'])) {
			$eventos = $this->dependencias['cuadro_abm']->get_lista_eventos();
			$eventos += eventos::cancelar();
			$this->dependencias['cuadro_abm']->set_eventos($eventos);
		}		
	}	
	
	function evt__reiniciar()
	{
		$this->disparar_limpieza_memoria();
	}

	//------------------------------------
	//				ML
	//------------------------------------
	function evt__ml__carga()
	{
		return $this->datos_ml;
	}	

	function evt__ml__modificacion($datos)
	{
		$this->datos_ml = $datos;
	}	
	
	//------------------------------------
	//			FORMULARIO
	//------------------------------------
	function evt__formulario__modificacion($datos)
	{
		$this->datos_formulario = $datos;
	}

	function evt__formulario__carga(){ 
		return $this->datos_formulario;
	}	
	
	//------------------------------------
	//		FORMULARIO en ABM
	//------------------------------------
	function evt__formulario_abm__carga()
	{
		$this->dependencias['formulario_abm']->set_colapsable(false);
		if (isset($this->registro_actual)) {
			foreach ($this->datos_formulario_abm as $registro) {
				if ($this->registro_actual == $registro['editable']) {
				   return $registro;
				}
			}
		}
		return null;
	}	
	
	function evt__formulario_abm__alta($registro)
	{
		$this->datos_formulario_abm[$registro['editable']] = $registro;
	}
	
	function evt__formulario_abm__modificacion($registro_mod)
	{
		$clave = $registro_mod['editable'];
		if (isset($this->datos_formulario_abm[$clave]))
			$this->datos_formulario_abm[$clave] = $registro_mod;
		else
			throw new excepcion_toba('EL ABM no contiene un registro en edición');
	}	

	function evt__formulario_abm__cancelar()
	{
		unset($this->registro_actual);
		$this->dependencias['cuadro_abm']->deseleccionar();	
	}	


	function evt__formulario_abm__baja()
	{
		if (isset($this->registro_actual))
			unset($this->datos_formulario_abm[$this->registro_actual]);
		else
			throw new excepcion_toba('EL ABM no contiene un registro en edición');	
	}	
	
	//------------------------------------
	//		CUADRO en ABM
	//------------------------------------
	function evt__cuadro_abm__carga()
	{
		//Filtra los elementos
		$candidatos = array_values($this->datos_formulario_abm);
		if (! isset($this->datos_filtro['editable'])) {
			return $candidatos;
		}
		$cuadro = array();
		foreach ($candidatos as $i => $candidato) {
			if (stripos($candidato['editable'], $this->datos_filtro['editable']) !== false) {	//Esta filtrado
				$cuadro[] = $candidato;
			}
		}
		return $cuadro;
	}

	function evt__cuadro_abm__seleccion($seleccion)
	{
		$this->registro_actual = $seleccion;
	}

	function evt__cuadro_abm__cancelar()
	{
		$this->evt__formulario_abm__cancelar();
	}	

	//------------------------------------
	//		FILTRO en ABM
	//------------------------------------
	function evt__filtro_abm__carga()
	{
		$this->dependencias['filtro_abm']->colapsar();
		if (isset($this->datos_filtro))
			return $this->datos_filtro;
		else
			return array();
	}
	
	function evt__filtro_abm__filtrar($datos)
	{
		$this->datos_filtro = $datos;
	}
	
	function evt__filtro_abm__cancelar()
	{
		unset($this->datos_filtro);
	}	

	//------------------------------------
	//		PROCESO
	//------------------------------------
	function get_info_post_proceso()
	//Mostrar una pantalla cuando se termino el proceso OK.
	{
		return "Mensaje a mostrar despues del procesamiento";	
	}

	
} 

?>