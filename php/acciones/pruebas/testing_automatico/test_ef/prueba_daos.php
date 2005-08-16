<?php

class prueba_daos
{
	function get_valor()
	{
		//Se hace global para evitar incluir el archivo antes que lo haga el EF
		global $retorno_dao;
		return $retorno_dao;
	}
	
	
	function dao_multi_seleccion() { return prueba_daos::get_valor(); }
	
	function dao_editable() { return prueba_daos::get_valor(); }

}


?>