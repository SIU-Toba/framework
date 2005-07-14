<?php
define('apex_wiki_url', 'http://toba.siu.edu.ar/trac/wiki/');

class motor_wiki
/*
*	Parser de sintaxis wiki 
*/
{
	function tiene_wiki($texto)
	//Analiza si una cadena contiene algo formateado como wiki
	{
		if (is_array(motor_wiki::link_wiki($texto)))
			return true;
		else
			return false;
	}
	
	function formato_texto($texto)
	//Retorna la representación de texto plano de una cadena
	{
		$exp_reg = '(.+)\[wiki:[^\ ]+[\ ](.+)\](.+)';
		if (ereg($exp_reg, $texto, $resultado)) {
			if (count($resultado) == 4) {
				return $resultado[1].$resultado[2].$resultado[3];
			}
		}
	}

	function link_wiki($texto)
	//Retorna el link y la descripción a la pagina wiki referencia en la cadena 
	{
		$exp_reg = '\[wiki:([^\ ]+)[\ ](.+)\]';
		if (ereg($exp_reg, $texto, $resultado)) {
			if (count($resultado) == 3) {
				return array(apex_wiki_url.$resultado[1], $resultado[2]);
			}
		}
		return null;
	}
}


?>