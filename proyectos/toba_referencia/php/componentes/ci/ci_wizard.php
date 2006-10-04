<?php
require_once('nucleo/componentes/interface/toba_ci.php');

class ci_wizard extends toba_ci
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
	function conf__tipos()
	{
		if (isset($this->tipo_instalacion))
			return $this->tipo_instalacion;
	}
	
	function evt__tipos__modificacion($tipo)
	{
		$this->tipo_instalacion = $tipo;
	}
	
	/*
	*	Durante la configuracion se quieren saltear dos etapas si la instalacion no es personalizada
	*/	
	function conf()
	{
		switch ($this->get_id_pantalla()) {
			case 4:
			case 5:
				if ($this->tipo_instalacion['tipo'] != 'personalizada') {
					$pantalla = ($this->wizard_avanza()) ? 6 : 3;
					$this->set_pantalla($pantalla);
				}
				break;
		}
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
		$editable = array('fecha' => time(), 'otro' => true);
		toba::zona()->cargar($editable);	
	}
	
	function evt__descargar_zona()
	{
		toba::zona()->resetear();
	}	
	
	static function get_info_zona($id)
	{
		return date('D M j G:i:s T Y', $id['fecha']);
	}
}
?>