<?php
/*
*	Compara dos instancias
*		Como es imposible actualmente instanciar dos INSTANCIAS toba, este
*		script llama dos veces al comando 'toba instancia'
*/
class comparador_instancias
{
	private $i1;
	private $i2;
	private $datos1;
	private $datos2;
	private $diferencia;

	function __construct( $i1, $i2 )
	{
		$this->i1 = $i1;
		$this->i2 = $i2;
		$this->clase1 = 'registros_' . $this->i1;
		$this->clase2 = 'registros_' . $this->i2;
		$this->archivo1 = toba_dir() . '/temp/' . $this->clase1 . '.php';
		$this->archivo2 = toba_dir() . '/temp/' . $this->clase2 . '.php';
		//Llamo a los comandos que recuperan la informacion
		$x = exec( 'toba instancia dump_info_tablas ' . $this->i1 );
		$x = exec( 'toba instancia dump_info_tablas ' . $this->i2 );
		require_once( $this->archivo1 ); 
		require_once( $this->archivo2 ); 
		$this->datos1 = call_user_func( array( $this->clase1, 'get_datos' ) );
		$this->datos2 = call_user_func( array( $this->clase2, 'get_datos' ) );
	}
	
	function procesar()
	{
		return $this->comparar_registros();
	}
	
	function finalizar()
	{
		unlink( $this->archivo1 ); 
		unlink( $this->archivo2 ); 
	}
	
	/*
	*	Compara que las dos instancias tengan las mismas tablas
	*/
	function comparar_composicion()
	{
		if( count( $this->datos1 ) <> count( $this->datos2 ) ) {
			$t1 = array_keys( $this->datos1 );
			$t2 = array_keys( $this->datos2 );
			$solo_1 = array_diff( $t1, $t2 );
			$solo_2 = array_diff( $t2, $t1 );
			return array( $this->i1 => $solo_1, $this->i2 => $solo_2 );
		}
	}
	
	/*
	*	Compara los registros entre dos tablas
	*/
	function comparar_registros()
	{
		$tit0 = "TABLA";
		$tit1 = "Instancia: " . $this->i1;
		$tit2 = "Instancia: " . $this->i2;
		$tit3 = "Diferencia";
		$diff = array();
		$datos2 = $this->datos2;
		$pos = 0;
		foreach( $this->datos1 as $tabla => $reg1 )
		{
			if( isset( $datos2[$tabla]) ) {
				$reg2 = $datos2[$tabla];
				if( $reg1 !== $reg2 ) {
					$diff[ $pos ][ $tit0 ] = $tabla;
					$diff[ $pos ][ $tit1 ] = $reg1;
					$diff[ $pos ][ $tit2 ] = $reg2;
					$diff[ $pos ][ $tit3 ] = $reg1 - $reg2;
					$pos++;
				}	
			} else {
				$diff[ $pos ][ $tit0 ] = $tabla;
				$diff[ $pos ][ $tit1 ] = $reg1;
				$diff[ $pos ][ $tit2 ] = 'No existe';
				$diff[ $pos ][ $tit3 ] = '-';
				$pos++;
			}
			unset( $datos2[$tabla] );

		}
		if( count( $datos2 ) > 0 ) {
			foreach( $datos2 as $tabla => $reg ) {
				$diff[ $pos ][ $tit0 ] = $tabla;
				$diff[ $pos ][ $tit1 ] = 'No existe';
				$diff[ $pos ][ $tit2 ] = $reg;
				$diff[ $pos ][ $tit3 ] = '-';
				$pos++;
			}	
		}
		return $diff;
	}
}
?>