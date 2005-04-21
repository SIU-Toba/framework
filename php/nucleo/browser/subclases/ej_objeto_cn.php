<?php

class objeto_cn_test extends objeto_cn_t
{
	protected $nombre_de_la_propiedad_a_persistir;

	function __construct($id)
	{
		parent::__construct($id);
	}

	function destruir()
	{
		parent::destruir();
	}

	function mantener_estado_sesion()
	{
		$estado = parent::mantener_estado_sesion();
		$estado[] = "nombre_de_la_propiedad_a_persistir";
		return $estado;
	}

	function evt__inicializar()
	//Se llama en el constructor y despues de resetear
	{
		
	}

	function evt__validar_datos()
	//Validar los datos
	{
		
	}
	
	function evt__procesar_especifico()
	//Proceso especifico de la operacion
	{
		
	}

	//--- GET ci
	
	//--- SET ci

}
?>