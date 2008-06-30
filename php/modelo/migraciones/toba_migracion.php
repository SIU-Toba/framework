<?php

class toba_migracion
{	
	protected $elemento;	//Elemento del modelo que se esta migrando: instalacion, instancia o proyecto
	protected $manejador_interface;
	
	function __construct(toba_modelo_elemento $elemento_modelo)
	{
		$this->elemento = $elemento_modelo;
		$this->manejador_interface = $elemento_modelo->get_manejador_interface();
		$this->ini();
	}
	
	function ini()
	{
		
	}

}
?>