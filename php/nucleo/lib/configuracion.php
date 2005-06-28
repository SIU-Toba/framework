<?
/*
	Gestor de configuraciones
	-------------------------
	
		- falta definir la operacion dentro del configurador del toba que se encargue de admistrar
			las configuraciones
*/
class configuracion
{
	function get_valor_derecho($id)
	//Devuelve el valor del derecho que corresponde al usuario
	{
		if($valor = catalogo_de_derechos($id)){
			return $valor;
		}else{
			throw new excepcion_toba("Se solicito un DERECHO inexistente: '$id'");
		}
	}
	
	function get_valor_parametro($id)
	//Devuelve un parametro de configuracion del sistema
	{
		if($valor = catalogo_de_parametros($id)){
			return $valor;
		}else{
			throw new excepcion_toba("Se solicito un PARAMETRO inexistente: '$id'");
		}
	}
}
?>