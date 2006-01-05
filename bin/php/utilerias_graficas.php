<?
	//===============================================
	// Utilerias graficas
	//===============================================

	function separador($texto)
	{
		if($texto!="") $texto = "   $texto   ";
		echo "\n\n===$texto============================================================\n\n";
	}

	function paso( $texto )
	{
		static $n = 1;
		echo "\n";
		echo "------------------------------------------------------------------------\n";
		echo " PASO: $n . $texto \n";
		echo "------------------------------------------------------------------------\n";
		$n++;
	}

	function alerta($texto)
	{
		echo "*** ATENCION ***  $texto \n";
	}

	function mensaje($texto)
	{
		echo "$texto \n";
	}

	function dialogo_simple($texto)
	{
		echo "$texto (Si o No)\n";
		do {
			echo "(s/n):";
			$respuesta = trim( fgets( STDIN ) );
			$ok = ($respuesta == 's') || ( $respuesta == 'n');
		} while ( ! $ok );
		if( $respuesta == 's') return true;
		return false;
	}
?>