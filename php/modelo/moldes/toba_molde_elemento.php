<?php

class toba_molde_elemento
{
	protected $datos;				// Datos relacion que persiste el componente
	protected $extension;			// Molde del codigo de la extension
	protected $carpeta_base;		
	protected $archivo;

	function __construct()
	{
		//Busco el datos relacion correspondiente al componente
		$id = toba_info_editores::get_dr_de_clase($this->clase_dr);
		$componente = array('proyecto' => $id[0], 'componente' => $id[1]);
		$this->datos = toba_constructor::get_runtime($componente);
		//Hay que desactivar la transaccion por DR
		//$this->datos->
	}
	
	function set_carpeta_archivos($carpeta)
	{
		$this->carpeta_base = $carpeta;
	}

	function extender_clase($archivo)
	{
		
	}

	//---------------------------------------------------

	function generar()
	{
		$this->generar_archivo();
		$this->asociar_archivo_a_componente();
		$this->generar_metadatos();
	}
	
	protected function generar_archivo()
	{
		
	}
	
	protected function asociar_archivo_a_componente()
	{
		
	}
	
	protected function generar_metadatos()
	{
		$this->datos->sincronizar();
	}
}
?>