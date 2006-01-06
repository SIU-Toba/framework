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
		$caracteres = 0;
		//Armo los grupos
		while ( count( $palabras ) > 0 ){
			$palabra = array_shift( $palabras );
			$caracteres += ( strlen( $palabra ) + 1 );
			if( $caracteres > $caracteres_linea ) {
				$linea_actual++;
				$caracteres = 0;
			}
			$lineas[ $linea_actual ][] = $palabra;
		}
		//Contateno las palabras
		for( $a=0; $a < count( $lineas ); $a++) {
			$lineas[ $a ] = implode( ' ', $lineas[ $a ] );
		}
		return $lineas;
	}

?>