<?

	//Averiguo la revision con la que se esta trabajando
	$dir = $argv[1];
	$archivo = "$dir/.svn/entries";
	if(file_exists($archivo))
	{
		$contenido = file_get_contents ( $archivo );
		$captura = array();
		if(preg_match("/revision=\"(.*)\"/", $contenido, $captura))
		{
			$revision = $captura[1];
			
		}else{
			$revision = "XXX";
		}
	}else{
		$revision = "Error";
	}
	
	//Averiguo el nombre de la tabla en la que hay que hacer el insert
	$tabla = isset($arg[2]) ? $arg[2] : "apex_revision";
	echo "INSERT INTO $tabla (revision) VALUES ('$revision');"


?>