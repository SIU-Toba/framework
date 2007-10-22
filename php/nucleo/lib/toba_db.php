<?php
/**
 * Este archivo posee shortcuts de acceso a las bases
 * @package Fuentes
 */
	/**
	*	@see toba_db::consultar()
	*/
	function consultar_fuente($sql, $id_fuente=null, $tipo_fetch=null, $obligatorio=false)
	{
		return toba::db($id_fuente)->consultar($sql, $tipo_fetch, $obligatorio);
	}

	/**
	*	@see toba_db::ejecutar()
	*/
	function ejecutar_fuente($sql, $id_fuente=null)
	{
		return toba::db($id_fuente)->ejecutar($sql);
	}

	/**
	*	@see toba_db::ejecutar()
	*/
	function sentencia_fuente($sql, $parametros=null, $id_fuente=null)
	{
		return toba::db($id_fuente)->sentencia($sql, $parametros=null);
	}
	
	/**
	*	@see toba_db::ejecutar_transaccion()
	*/
	function ejecutar_transaccion($sentencias_sql, $id_fuente=null)
	{
		toba::db($id_fuente)->ejecutar_transaccion($sentencias_sql);
	}

	/**
	*	@see toba_db::recuperar_secuencia()
	*/
	function recuperar_secuencia($secuencia, $id_fuente=null)
	{
		return toba::db($id_fuente)->recuperar_secuencia($secuencia);
	}
	
	/**
	*	@see toba_db::abrir_transaccion()
	*/
	function abrir_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->abrir_transaccion();
	}

	/**
	*	@see toba_db::abortar_transaccion()
	*/	
	function abortar_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->abortar_transaccion();
	}

	/**
	*	@see toba_db::cerrar_transaccion()
	*/	
	function cerrar_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->cerrar_transaccion();
	}

	/**
	*	@see toba_db::quote()
	*/	
	function quote($dato, $id_fuente=null)
	{
		return toba::db($id_fuente)->quote($dato);
	}
?>