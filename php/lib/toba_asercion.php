<?php

/**
 * Conjunto de aserciones básicas, en caso de fallo lanzan una excepción toba_error
 * @see toba_error
 * @package Debug
 */
class toba_asercion
{
	static function no_es_nulo($variable, $texto="ATENCION, el elemento es NULO")
	{
		if(!isset($variable)){
			throw new toba_error($texto);
		}
	}

	static function es_objeto($instancia, $texto="ATENCION, el elemento no es un OBJETO")
	{
		if(!is_object($instancia)){
			throw new toba_error($texto);
		}
	}

	static function es_array($array, $texto="ATENCION, el elemento no es un ARRAY", $en_lugar_de=false)
	{
		if(!is_array($array)){
			if ($en_lugar_de) {
				$recibido = var_export($array, true);
				$texto .= "\nEn su lugar se recibio el valor: <pre>$recibido</pre>";
			}
			throw new toba_error($texto);
		}
	}

	static function arrays_igual_largo($array_1, $array_2, $texto="ATENCION, los arrays no tienen el mismo tamaño")
	{
		self::es_array($array_1,$texto . "- El parametro 1 no es un array");
		self::es_array($array_2,$texto . "- El parametro 2 no es un array");
		if( count($array_1) != count($array_2) ){
			throw new toba_error($texto);
		}		
	}

	static function es_array_dimension($array, $dimension, $texto="ERROR: ")
	{
		if(!is_array($array)){
			throw new toba_error($texto . "el elemento no es un ARRAY");
		}else{
			$dim_real = count($array);
			if($dim_real!=$dimension){
				throw new toba_error($texto . "El tamaño del array es incorrecto: $dim_real (Esperado: $dimension).");
			}
		}
	}

	static function es_array_o_null($array, $texto="ATENCION, el elemento no es un ARRAY y no es NULO")
	{
		if(is_null($array)){
			return;
		}
		if(!is_array($array)){
			throw new toba_error($texto);
		}
	}

	static function error($texto)
	{
		throw new toba_error($texto);
	}
}		
?>