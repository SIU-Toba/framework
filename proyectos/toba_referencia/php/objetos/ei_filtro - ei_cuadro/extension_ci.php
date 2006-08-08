<?php
require_once('nucleo/componentes/interface/objeto_ci.php');

class extension_ci extends objeto_ci
{
	protected $datos_filtro;
	protected $datos_estaticos =  array(
			array( 'fecha' => '2004-05-20', 'importe' => 12500), 
			array( 'fecha' => '2004-05-21', 'importe' => 22200), 
			array( 'fecha' => '2004-05-22', 'importe' => 4500), 		
			array( 'fecha' => '2005-05-20', 'importe' => 12500), 
			array( 'fecha' => '2005-05-21', 'importe' => 22200), 
			array( 'fecha' => '2005-05-22', 'importe' => 4500)	
		);
	
    function mantener_estado_sesion() 
    { 
        $propiedades = parent::mantener_estado_sesion(); 
		$propiedades[] = "datos_filtro"; 
        return $propiedades; 
    } 

	//Se le agrega un evento borrar al cuadro	
	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		//Se agrega un evento 'mi_evento' global al cuadro
		$mi_evento = eventos::evento_estandar('mi_evento',  'Evento global agregado en el CI');
		$eventos += $mi_evento;
		return $eventos;
	}	
	
	function evt__filtro__filtrar($datos)
	{
		$this->datos_filtro = $datos;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->datos_filtro);
	}
	
	function conf__filtro()
	{
		if (isset($this->datos_filtro))
			return $this->datos_filtro;
	}
	

	function conf__cuadro()
	{
		//El usuario decidió cargarlo a partir de este método?
		if (isset($this->datos_filtro) && $this->datos_filtro['metodo'] == 'CI') {
			return $this->filtrar_importes();
		}
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		toba::get_cola_mensajes()->agregar("Se seleccionó la fecha {$seleccion['fecha']}", "info");
	}
	
	function evt__cuadro__baja($seleccion)
	{
		toba::get_cola_mensajes()->agregar("Se quiere borrar la fecha {$seleccion['fecha']}", "info");	
	}
	
    function evt__cuadro__ordenar($param) 
    { 
        $columna = $param['columna']; 
        $sentido = $param['sentido']; 
        toba::get_cola_mensajes()->agregar("Evento escuchado en php: Se quiere ordenar la columna $columna en orden $sentido", 'info'); 
    } 	
	
	function evt__mi_evento()
	{
		$this->dependencia('cuadro')->deseleccionar();
		toba::get_cola_mensajes()->agregar("Este evento borra la selección del cuadro (si es que la hay)", 'info');	
	}

	function filtrar_importes()
	{
		//Esto normalmente se haría utilizando SQL...
		$retorno = array();
		foreach ($this->datos_estaticos as $dato) {
			if ($this->datos_filtro['importe'] == '' || $this->datos_filtro['importe'] < $dato['importe'])
				$retorno[] = $dato;
		}
		return $retorno;
	}

}

?>