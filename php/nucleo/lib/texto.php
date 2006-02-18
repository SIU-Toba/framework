<?

	/*
	*	Devuelve un array de lineas
	*	Atencion: 	1) Algo anda mal.
	*				2) No respeta los saltos de linea
	*				
	*/
	function separar_texto_lineas( $texto, $caracteres_linea )
	{
		$texto = str_replace("\n", ' ', $texto);
		$palabras = explode(' ', $texto );
		$lineas = array();
		$linea_actual = 0;
		$caracteres_acum = 0;
		//Armo los grupos
		while ( count( $palabras ) > 0 ){
			$palabra = array_shift( $palabras );
			$caracteres_actual = ( strlen( $palabra ) + 1 );
			$caracteres_acum += $caracteres_actual;
			//Si la palabra no entra en la linea actual, hay que ponerla en una linea nueva
			if( $caracteres_acum > $caracteres_linea ) {
				//Excepto que ya se este en una linea nueva!
				if ($caracteres_acum != $caracteres_actual) {
					$linea_actual++;
					$caracteres_acum = 0;
				}
			}
			$lineas[ $linea_actual ][] = $palabra;
		}
		//Contateno las palabras
		$salida = array();
		for( $a=0; $a < count( $lineas ); $a++) {
			$salida[ $a ] = implode( ' ', $lineas[ $a ] );
		}
		return $salida;
	}

?>