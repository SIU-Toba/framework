<?
class asercion
{
	function no_es_nulo($variable, $texto="ATENCION, el elemento es NULO")
	{
		if(!isset($variable)){
			throw new excepcion_toba($texto);
		}
	}

	function es_objeto($instancia, $texto="ATENCION, el elemento no es un OBJETO")
	{
		if(!is_object($instancia)){
			throw new excepcion_toba($texto);
		}
	}

	function es_array($array, $texto="ATENCION, el elemento no es un ARRAY")
	{
		if(!is_array($array)){
			throw new excepcion_toba($texto);
		}
	}

	function error($texto="Estado de error")
	{
		
	}
}		
/*		
		else{
			if(count($array)==0){
				throw new excepcion_toba($texto);
			}
		}
*/
?>