<?
	require_once("conversiones/conversion_toba.php");
	
	//Registrar parametros
	$this->registrar_parametros();
	
	$mostrar_info = false;
	if (isset($this->parametros['-i'])) {
		$mostrar_info = true;
	}
	$es_prueba = false;
	if (isset($this->parametros['-t'])) {
		$es_prueba = true;
	}
	if (isset($this->parametros['-l'])) {
		$versiones = conversion_toba::conversiones_posibles();
		foreach ($versiones as $version) {
			$version = str_replace("_", ".", $version);
			echo $version."\n";
		}
		exit();
	}
	
	if (! isset($this->parametros['-v'])) {
		fwrite(STDERR, "ERROR: Se debe especificar el número de version destino.\n");
		exit(4);
	}
	if (!$mostrar_info) {
		if (! isset($this->parametros['-p'])) {
			fwrite(STDERR, "ERROR: Se debe especificar el proyecto al cual aplicar la conversion.\n");
			exit(4);
		}
		$proyecto = $this->parametros['-p'];
	}

	//La convension es reemplazar . por _ en los nombres de version
	$version_sin_parsear = $this->parametros['-v'];
	$version = str_replace(".", "_", $version_sin_parsear);
	
	if (! conversion_toba::existe_conversion($version)) {
		fwrite(STDERR, "ERROR: No hay una conversion a la version $version_sin_parsear.\n");
		exit(4);
	}
	
	//Se incluye la conversion
	require_once("conversiones/conversion_$version.php");
	$clase = "conversion_$version";
	$conversion = new $clase();
	
	if ($mostrar_info)
		$conversion->info();
	else {
		try {
			$anterior = $conversion->ejecutada_anteriormente($proyecto);
			if ($anterior) {
				echo "La conversion ya fue ejecutada en fecha $anterior\n";
			} else {
				$conversion->procesar($proyecto, $es_prueba);
			}
		} catch (excepcion_toba $e) {
			fwrite(STDERR, $e->getMessage()."\n");
			exit(4);
		}
	}
	

?>