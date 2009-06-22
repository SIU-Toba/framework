<?php

class toba_referencia_fuente_datos extends toba_fuente_datos
{
	/**
	*	Una vez conectado a la base se crea una tabla temporal conteniendo el usuario actual 
	*/
	function post_conectar()
	{
		//En este metodo antiguamente se incluia codigo para asegurarse que el esquema de auditoria
		//guardara el usuario conectado.
	}
	
}


?>