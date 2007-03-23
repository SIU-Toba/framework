<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_ci extends toba_ci
{
	protected $s__filtro;
	protected $datos_estaticos =  array(
			array( 'fecha' => '2004-05-20', 'importe' => 12500), 
			array( 'fecha' => '2004-05-21', 'importe' => 22200), 
			array( 'fecha' => '2004-05-22', 'importe' => 4500), 		
			array( 'fecha' => '2005-05-20', 'importe' => 12500), 
			array( 'fecha' => '2005-05-21', 'importe' => 22200), 
			array( 'fecha' => '2005-05-22', 'importe' => 4500)	
		);
	
	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro()
	{
		if (isset($this->s__filtro))
			return $this->s__filtro;
	}
	

	function conf__cuadro()
	{
		if (!isset($this->s__filtro) || $this->s__filtro['metodo'] == 'estatica') {
			return $this->datos_estaticos;
		} else {
			return $this->filtrar_importes();
		}
	}
	
	function evt__cuadro__seleccion($seleccion)
	{
		toba::notificacion()->agregar("Se seleccionó la fecha {$seleccion['fecha']}", "info");
	}
	
	function evt__cuadro__baja($seleccion)
	{
		toba::notificacion()->agregar("Se quiere borrar la fecha {$seleccion['fecha']}", "info");	
	}
	
    function evt__cuadro__ordenar($param) 
    { 
        $columna = $param['columna']; 
        $sentido = $param['sentido']; 
        toba::notificacion()->agregar("Evento escuchado en php: Se quiere ordenar la columna $columna en orden $sentido", 'info'); 
        return true;
    } 	
	
	function evt__mi_evento()
	{
		$this->dependencia('cuadro')->deseleccionar();
		toba::notificacion()->agregar("Este evento borra la selección del cuadro (si es que la hay)", 'info');	
	}

	function filtrar_importes()
	{
		//Esto normalmente se haría utilizando SQL...
		$retorno = array();
		foreach ($this->datos_estaticos as $dato) {
			if ($this->s__filtro['importe'] == '' || $this->s__filtro['importe'] < $dato['importe'])
				$retorno[] = $dato;
		}
		return $retorno;
	}

}

?>