<?php
require_once('nucleo/lib/toba_admin_fuentes.php');
/*
*	Este archivo posee shortcuts de acceso a las bases
*/
	/**
	*	@see db::consultar()
	*/
	function consultar_fuente($sql, $id_fuente=null, $tipo_fetch=null, $obligatorio=false)
	{
		return toba::db($id_fuente)->consultar($sql, $tipo_fetch, $obligatorio);
	}

	/**
	*	@see db::ejecutar()
	*/
	function ejecutar_fuente($sql, $id_fuente=null)
	{
		return toba::db($id_fuente)->ejecutar($sql);
	}
	
	/**
	*	@see db::ejecutar_transaccion()
	*/
	function ejecutar_transaccion($sentencias_sql, $id_fuente=null)
	{
		toba::db($id_fuente)->ejecutar_transaccion($sentencias_sql);
	}

	/**
	*	@see db::recuperar_secuencia()
	*/
	function recuperar_secuencia($sql, $id_fuente=null)
	{
		return toba::db($id_fuente)->recuperar_secuencia($sql);
	}
	
	/**
	*	@see db::abrir_transaccion()
	*/
	function abrir_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->abrir_transaccion();
	}

	/**
	*	@see db::abortar_transaccion()
	*/	
	function abortar_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->abortar_transaccion();
	}

	/**
	*	@see db::cerrar_transaccion()
	*/	
	function cerrar_transaccion($id_fuente=null)
	{
		toba::db($id_fuente)->cerrar_transaccion();
	}
?>