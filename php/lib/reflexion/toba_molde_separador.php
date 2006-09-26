<?php

class toba_molde_separador extends implements elemento_molde
{
	protected $nombre;
	protected $tipo;
	
	function __construct($nombre, $descripcion='', $tipo='chico')
	{
		$this->nombre = $nombre;
		if( ($tipo != 'corto') || ($tipo != 'largo') ) {
			throw new toba_error('Error en la construccion del molde_separador: los tipos validos son \'corto'\ y \'largo\'');
		}
		$this->tipo = $tipo;
	}
	
	function generar_codigo()
	{
		$metodo = 'separador_' . $this->tipo;
		return $this->$metodo();
	}

	static function separador_chico()
	{	
		$salida = $this->identar() . "//---- $nombre -------------------------------------------------------\n\n";	
		return $salida;
	}	
	
	static function separador_grande($nombre)
	{
		$salida = $this->identar() . "//-------------------------------------------------------------------\n";
		$salida .= $this->identar() . "//--- $nombre\n";
		$salida .= $this->identar() . "//-------------------------------------------------------------------\n";
		return $salida;
	}	
}

?>