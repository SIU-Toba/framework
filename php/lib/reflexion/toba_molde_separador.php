<?php
require_once('toba_molde_elemento.php');

class toba_molde_separador extends toba_molde_elemento
{
	protected $nombre;
	protected $descripcion;
	protected $tipo;
	
	function __construct($nombre, $descripcion=null, $tipo='chico')
	{
		$this->nombre = $nombre;
		$this->descripcion = isset($descripcion) ? $descripcion : $this->nombre;
		if( ($tipo != 'corto') || ($tipo != 'largo') ) {
			throw new toba_error('Error en la construccion del molde_separador: los tipos validos son \'corto\' y \'largo\'');
		}
		$this->tipo = $tipo;
	}
	
	function get_tipo()
	{
		return $this->tipo;	
	}

	function get_descripcion()
	{
		return $this->descripcion;
	}
	
	function get_codigo()
	{
		$metodo = 'separador_' . $this->tipo;
		return $this->$metodo();
	}

	static function separador_chico()
	{	
		$salida = $this->identado() . "//---- $nombre -------------------------------------------------------\n\n";	
		return $salida;
	}	
	
	static function separador_grande($nombre)
	{
		$salida = $this->identado() . "//-------------------------------------------------------------------\n";
		$salida .= $this->identado() . "//--- $nombre\n";
		$salida .= $this->identado() . "//-------------------------------------------------------------------\n";
		return $salida;
	}	
}
?>