<?php

class toba_molde_separador extends implements elemento_molde
{
	protected $tipo;
	
	function __construct($tipo)
	{
		if( ($tipo != 'corto') || ($tipo != 'largo') ) {
			throw new toba_error('Error en la construccion del molde_separador: los tipos validos son \'corto'\ y \'largo\'');
		}
		$this->tipo = $tipo;
	}
	
	function generar_codigo()
	{
		
	}

	static function separador_seccion_chica($nombre='')
	{	
		$salida = $this->get_identado() . "//---- $nombre -------------------------------------------------------\n\n";	
		return $salida;
	}	
	
	static function separador_seccion_grande($nombre)
	{
		$salida = $this->get_identado() . "//-------------------------------------------------------------------\n";
		$salida .= $this->get_identado() . "//--- $nombre\n";
		$salida .= $this->get_identado() . "//-------------------------------------------------------------------\n";
		return $salida;
	}	
}

?>