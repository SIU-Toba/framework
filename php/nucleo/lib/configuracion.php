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
		return catalogo_de_derechos($id);
	}
	
	function get_valor_parametro($id)
	//Devuelve un parametro de configuracion del sistema
	{
		return catalogo_de_parametros($id);
	}
}
?>