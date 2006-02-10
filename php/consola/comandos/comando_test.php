<?
require_once('comando_toba.php');
require_once('modelo/lib/comparador_instancias.php');

class comando_test extends comando_toba
{
	static function get_info()
	{
		return 'Baterias de test';
	}

	/**
	*	<I_1> <I_2> Compara dos Instancias
	*/
	function opcion__ci()
	{
		if ( !isset( $this->argumentos[1] ) || !isset( $this->argumentos[2] ) ) {
			throw new excepcion_toba("Es necesario indicar el nombre de las dos instancias");
		}
		$ci = new comparador_instancias( $this->argumentos[1], $this->argumentos[2] );
		$datos = $ci->procesar();
		$titulos = array( 'TABLA', $this->argumentos[1], $this->argumentos[2], 'diff');
		$tabla = Console_Table::fromArray( $titulos, $datos );
		echo( $tabla );
		$ci->finalizar();
	}
	
}
?>