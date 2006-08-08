<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

/**
*	Esta clase es un ejemplo de navegación particular. Al no tener un tipo de navegación predeterminado, esta debe
*   hacerse manualmente, en este caso dependiendo de dos eventos pasar_pantalla_1 y pasar_pantalla_2.
*/
class ci_pantallas_logicas extends objeto_ci
{
	protected $pantalla_actual;

	function __construct($id)
	{
		$this->pantalla_actual = 1;	//Por defecto se comienza en la primera pantalla
		parent::__construct($id);
	}
	
	/**
	*	Las propiedades retornadas por este método serán persistidas en la sesión 
	*/
    function mantener_estado_sesion()
    { 
        $propiedades = parent::mantener_estado_sesion();
        $propiedades[] = "pantalla_actual";
        return $propiedades; 
    }

	/**
	*	Se muestran dos eventos particulares, dependiendo de la pantalla actual
	*/
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		if ($this->pantalla_actual == 1)
			$eventos += eventos::evento_estandar('pasar_pantalla_2', 'Pasar a la pantalla 2', true, 
													recurso::imagen_apl('paginacion/si_siguiente.gif'));
		else
			$eventos += eventos::evento_estandar('pasar_pantalla_1', 'Volver a la pantalla 1', true, 
													recurso::imagen_apl('paginacion/si_anterior.gif'));		
		return $eventos;
	}	
	
	/**
	* 	Al no tener una navegación predeterminada, siempre esta subclase debe desidir 
	* 	en qué pantalla se está posicionado
	*/
	function get_etapa_actual()
	{
		return $this->pantalla_actual;
	}	
	
	/**
	*	Estos dos eventos son la base de desición para el cambio de pantalla
	*/
	function evt__pasar_pantalla_1()
	{
		$this->pantalla_actual = 1;
	}	
	
	function evt__pasar_pantalla_2()
	{
		$this->pantalla_actual = 2;
	}	
	
	/**
	*	En la pantalla 1 el HTML se muestra en forma ad_hoc
	*/
	function obtener_html_contenido__1()
	{
		echo recurso::imagen_pro("presentaciones/operacion.gif",true);	
	}

	/**
	*	Control particular para no permitir salidas si no se visitaron todas las etapas
	*/
	function evt__2__salida()
	{
		//$this->informar_msg("Sali de la segunda pantalla");
	}

}
?>
