<?php

	function salto_linea()
	//Salto de linea dependiente de la plataforma
	{
		if (substr(PHP_OS, 0, 3) == 'WIN'){
			return "\r\n";
		}else{
			return "\n";
		}	
	}
	//-----------------------------------------------------------------
	function sl(){ return salto_linea(); }
	//-----------------------------------------------------------------

	function dump_array_php($array, $nombre="array",$html=false)
	//Dumpea un array como sintaxis de definicion de array de PHP
	{
		if(is_array($array)){
			$temp = dump_array_nivel($array);
		}else{
			return;
		}
		//concateno el nombre de la variable
		$linea = explode("\n",$temp);
		$php = "";
		for($a=0;$a<count($linea)-1;$a++){
			$php .=	$nombre . $linea[$a] . "\n";
		}
		if($html){
			return "<pre>$php</pre>";	
		}else{
			return $php;
		}
	}
	
	function dump_array_nivel($array)
	{
		$php ="";
		static $prefijo = "";
		foreach($array as $clave => $valor){
			if(is_numeric($clave)){
				$php_x = "[$clave]";
			}else{
				$php_x = "['$clave']";
			}
			$php_l = strlen($php_x);
			//Agrando el prefijo
			$prefijo .= $php_x;
			if(is_array($valor)){
				$php .= dump_array_nivel($valor);
			}else{
				$php .= "$prefijo='$valor';\n";
			}
			//Achico el prefijo
			$prefijo = substr($prefijo,0,strlen($prefijo)-$php_l);
		}
		return $php;
	}
	//-----------------------------------------------------------------

	function dump_array_javascript($array, $nombre, $es_objeto=false)
	{
		$js = "";
		if(is_array($array)){
			$js .= " $nombre = new Object();\n";
			foreach($array as $id => $valor)
			{
				if ($es_objeto)
					$valor_js = $valor;
				else
					$valor_js = "'$valor'";
				$js .= "$nombre"."['$id'] = $valor_js;\n";	
			}
		}
		return $js;
	}
	//-----------------------------------------------------------------

	function revision_svn($dir)
	//Busca la revision de a la que corresponde el TOBA
	{
		$archivo = "$dir/.svn/entries";
		if(file_exists($archivo))
		{
			//$fd = fopen($archivo, "r");
	   		//$contenido = fread($fd, filesize($archivo));
			$contenido = file_get_contents ( $archivo );
			$captura = array();
			if(preg_match("/revision=\"(.*)\"/", $contenido, $captura)){
				//ei_arbol($captura);	
				return $captura[1];
			}else{
				return "DESCONOCIDA";	
			}
		}else{
			return "DESCONOCIDA";	
		}
	}
	//-----------------------------------------------------------------


?>