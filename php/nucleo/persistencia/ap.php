<?php

/**
 * 	Administrador de persistencia generico
 * 	@package Objetos
 *  @subpackage Persistencia
 */
abstract class ap
{
	/**
	 * Se obtienen los datos del medio de persistencia
	 */
	function cargar(){}

	/**
	 * Se sincronizan los datos con el medio de persistencia
	 */
	function sincronizar(){}
}
?>