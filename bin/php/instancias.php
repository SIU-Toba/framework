<?
	function validar_instancia($archivo, $instancia)
	//Controla que la instancia se encuentre definida en instancias.php
	{
		if(file_exists($archivo))
		{
			$contenido = file_get_contents ( $archivo );
			$captura = array();
			//$instancia["desarrollo"][apex_db_base]
			//if(preg_match("/\$instancia\[\"(.*)\"\].*base/", $contenido, $captura))
			
			if( preg_match_all("/instancia.*?[\"\']([^\"\']*)[\'\"].*/", $contenido, $captura) )
			{
				//print_r($captura);
				$temp = array_unique($captura[1]); //print_r($temp);
				return (in_array($instancia, $temp));
			}else{
				fwrite(STDERR, "[buscar_instancia] ATENCION, error interno");
				return false;
			}
		}else{
			fwrite(STDERR, "[buscar_instancia] ATENCION, no exite el archivo \"php/instancias.php\"\n");
			return false;
		}
	}

	//Acceso al archivo de instancias
	$dir = $argv[1];
	$archivo = "$dir/php/instancias.php";

	//Instancia por defecto
	if(!isset($argv[2])){
		fwrite(STDERR, "ATENCION, no esta definida la variable de entorno \"toba_instancia\".
La misma tiene que apuntar a una instancia valida definida en php/instancias.php\n");
		exit(9);
	}else{
		if(trim($argv[2])==""){
			fwrite(STDERR, "ATENCION, no esta definida la variable de entorno \"toba_instancia\".
La misma tiene que apuntar a una instancia valida definida en php/instancias.php\n");
		}
		$instancia_defecto = $argv[2];
	}
	
	//Instancia explicita en el comando
	if(isset($argv[3])){
		$instancia = $argv[3];
		if(validar_instancia($archivo, $instancia)){
			echo $instancia;
			exit(100);
		}
	}
	
	if(validar_instancia($archivo, $instancia_defecto)){
		echo $instancia_defecto;
		exit(200);
	}else{
		exit(1);
	}
	

?>