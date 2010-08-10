<?php

    function parsear_propiedades( $texto, $extra='' )
    //Toma como entrada un texto tipo CSS (propiedad: valor;)
    //y devuelve un array asociativo $resultado[propiedad] = valor
    {
		$prop = array();
		//FALTA hacer que todas las claves queden en minuscula...
		$linea = explode($extra.";".$extra,$texto);
		//array_pop($linea);
		foreach($linea as $sentencia){
			if(trim($sentencia) != ""){
				$propiedad = explode($extra.":".$extra, trim($sentencia));
				if(count($propiedad)==2){
					$nombre = trim($propiedad[0]);
    	    	    $valor = trim($propiedad[1]);
				    $prop[$nombre] = $valor;
				}
			}
		}
		if(is_array($prop)){
			return $prop;		
		}else{
			return null;
		}
    }

	function empaquetar_propiedades( $array, $extra='' )
	{
		$filas = array();
		foreach($array as $clave => $valor){
			$filas[] = $clave.$extra.':'.$extra." $valor";
		}
		$texto = implode($extra.';'.$extra."\n",$filas) . $extra.";$extra\n";
		return $texto;
	}

	
	function parsear_doc_comment( $com, $sin_tags=true )
	{
	    $com = preg_replace("/(^[\\s]*\\/\\*\\*)
	                                 |(^[\\s]\\*\\/)
	                                 |(^[\\s]*\\*?\\s)
	                                 |(^[\\s]*)
	                                 |(^[\\t]*)/ixm", "", $com);
	    $com = str_replace("\r", "", $com);
	    $com = trim(preg_replace("/([\\t])+/", "\t", $com));
		$com = trim(preg_replace("/\\*\\//", "", $com));
		if ($sin_tags) {
			$com = trim(preg_replace("/@.*/", "", $com));
		}
		return $com;
	}
	
	/**
	 * Retorna un arreglo de custom tags dentro de un phpdoc 
	 */
	function parsear_doc_tags( $com )
	{
		$tags = array();
		$salida = array();
		preg_match_all("/@([\w]+)(.*)/", $com, $salida, PREG_SET_ORDER);
		foreach ($salida as $tag) {
			$tags[trim($tag[1])] = trim($tag[2]);
		}
		return $tags;
	}
?>