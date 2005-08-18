<?
	require_once("conversiones/conversion_toba.php");
	
	//Registrar parametros
	$this->registrar_parametros();
	
	$mostrar_info = false;
	if (isset($this->parametros['-i'])) {
		$mostrar_info = true;
	}
	$es_prueba = false;
	if (isset($this->parametros['-p'])) {
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
			$conversion->procesar($es_prueba);
		} catch (excepcion_toba $e) {
			fwrite(STDERR, $e->getMessage()."\n");
			exit(4);
		}
	}
	

?>