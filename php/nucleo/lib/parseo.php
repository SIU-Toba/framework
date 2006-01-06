<?php

    function parsear_propiedades( $texto )
    //Toma como entrada un texto tipo CSS (propiedad: valor;)
    //y devuelve un array asociativo $resultado[propiedad] = valor
    {
		$prop = array();
		//FALTA hacer que todas las claves queden en minuscula...
		$linea = explode(";",$texto);
		//array_pop($linea);
		foreach($linea as $sentencia){
			if(trim($sentencia) != ""){
				$propiedad = explode(":",trim($sentencia));
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

	function empaquetar_propiedades( $array )
	{
		foreach($array as $clave => $valor){
			$filas[] = "$clave: $valor";
		}
		$texto = implode(";\n",$filas) . ";\n";
		return $texto;
	}

	function parsear_propiedades_array( $texto )
	//Parsea cadenas que poseen el nombre del atributo, ":" y depues
	//una lista de elementos separados por comas que conforman un array
	{
		$prop = array();
		$linea = explode(";",$texto);
		if(is_array($linea)){
			foreach($linea as $sentencia){
				if(trim($sentencia) != ""){
					$propiedad = explode(":",trim($sentencia));
					if(count($propiedad)==2){
						$nombre = trim($propiedad[0]);
	    	    	    $valor = explode(",",trim($propiedad[1]));
					    $prop[$nombre] = $valor;
					}
				}
			}
			return $prop;
		}else{
			echo "ace";
			return null;
		}
	}
	
	function parsear_doc_comment( $com )
	{
	    $com = preg_replace("/(^[\\s]*\\/\\*\\*)
	                                 |(^[\\s]\\*\\/)
	                                 |(^[\\s]*\\*?\\s)
	                                 |(^[\\s]*)
	                                 |(^[\\t]*)/ixm", "", $com);
	    $com = str_replace("\r", "", $com);
	    $com = trim(preg_replace("/([\\t])+/", "\t", $com));
		$com = trim(preg_replace("/\\*\\//", "", $com));	    
		return $com;
	}
?>