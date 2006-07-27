<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

class ci_wizard extends objeto_ci
{
	protected $tipo_instalacion;
	
    function __construct($id) 
    { 
        parent::__construct($id); 
    } 

    function mantener_estado_sesion() 
    { 
        $propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'tipo_instalacion';
        return $propiedades; 
    }
	
	//-------------------------------------------------------------------------------	
	/*
	*  Manejo del formulario de seleccion de tipo de instalación
	*/
	function evt__tipos__carga()
	{
		if (isset($this->tipo_instalacion))
			return $this->tipo_instalacion;
	}
	
	function evt__tipos__modificacion($tipo)
	{
		$this->tipo_instalacion = $tipo;
	}
	
	//-------------------------------------------------------------------------------	
	/*
	*	Este evento permite saltear determinadas etapas en base a selecciones anteriores
	*/
	function evt__puede_mostrar_pantalla($pantalla)
	{
		switch ($pantalla) 
		{
			//La pantalla de componente y configuracion solo se muestra en una instalación personalizada
			case 4:
			case 5:
				if ($this->tipo_instalacion['tipo'] != 'personalizada')
					return false;
		}
		return true;
	}
	
	//-------------------------------------------------------------------------------		
	/*
	*	Una vez que se instalaron los archivos... no puede retroceder
	*/
	function get_lista_eventos()
    {
		$eventos = parent::get_lista_eventos();
		if ($this->get_pantalla_actual() == 7) {
			unset($eventos['cambiar_tab__anterior']);
		}
		return $eventos;
	}
	
	function evt__cargar_zona()
	{
		$editable = array('fecha' => date('D M j G:i:s T Y'), 'otro' => true);
		toba::get_zona()->cargar($editable);	
	}
	
	function evt__descargar_zona()
	{
		toba::get_zona()->resetear();
	}	
}


?>