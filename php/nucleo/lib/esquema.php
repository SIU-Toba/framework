<?

class esquema
{
	function generar_di($dot, $base_archivo, $salida="gif", $parser="dot")
	//Genera el archivo .dot y el .gif
	{
		$fdot = $base_archivo . ".dot";
		$fimg = $base_archivo . "." . $salida;
		//Guardo el archivo .dot
		$fp = fopen ($fdot, "w");
		fwrite($fp, $dot);
		fclose($fp);
		//Genero el archivo .gif
		exec("$parser -T$salida $fdot -o $fimg ",$devolucion,$estado);
		if($estado == 0){
			return array(1,"OK!");
		}else{
			//En windows el error no se muestra...
			return array(0,"Error: " . implode("\n",$devolucion));
		}
	}
//-------------------------------------------------------------

	function generar_i($dot, $base_archivo, $salida="gif", $parser="dot")
	//Genera la imagen sin grabar un .DOT
	{
		$fimg = $base_archivo . "." . $salida;
		//Habro un proceso, obtengo handler a stdin y stderr
		$descriptorspec = array(
		   0 => array("pipe", "r"),  	// stdin
//		   1 => array("pipe", "w"),  	// stdout
		   2 => array("pipe", "w"));	// stderr
		//Habro el proceso hijo
		$sentencia = "$parser -T$salida -o $fimg ";
		$proceso_hijo = proc_open($sentencia, $descriptorspec, $pipes);
		if (is_resource($proceso_hijo))
		{
		    //Cargo el archivo DOT en STDIN
		    fwrite($pipes[0], $dot );
		    fclose($pipes[0]);
		
			//Busco ERRORES en STDERR
			$error = "";
		    while(!feof($pipes[2])) {
		        $error .= fgets($pipes[2], 1024);
		    }
		    fclose($pipes[2]);

		    $estado = proc_close($proceso_hijo);
		    if(trim($error)==""){
				return array(1,"OK!");
		    }else{
				return array(0,"Error: $error");
			}
		}
	}
}

?>