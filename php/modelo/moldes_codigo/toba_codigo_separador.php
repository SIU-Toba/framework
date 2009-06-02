<?php
/**
 * @ignore
 */
class toba_codigo_separador extends toba_codigo_elemento
{
	protected $descripcion;
	protected $tipo;
	protected $ancho = 90;
	protected $caracter = '-';
	
	function __construct($nombre, $descripcion=null, $tipo='chico')
	{
		$this->nombre = $nombre;
		$this->descripcion = isset($descripcion) ? $descripcion : $this->nombre;
		if( ($tipo != 'chico') && ($tipo != 'grande') ) {
			throw new toba_error_asistentes('Error en la construccion del molde_separador: los tipos validos son \'chico\' y \'grande\'. Tipo solicitado: ' .$tipo . ' - Separador "' . $nombre . '"' );
		}
		$this->tipo = $tipo;
	}
	
	protected function ancho()
	{
		return $this->ancho - $this->get_caracteres_identacion();
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

	function separador_chico()
	{	
		$inicio = $this->identado() . "//" . str_repeat($this->caracter, 4) . ' ' . $this->nombre . ' ';
		return str_pad($inicio, $this->ancho(), $this->caracter) . "\n";
	}	
	
	function separador_grande()
	{
		$linea = str_pad( $this->identado() ."//", $this->ancho(), $this->caracter) . "\n";;
		return $linea . $this->separador_chico() . $linea;
	}	

	
}
?>