<?php
require_once('modelo/dba.php');
require_once("fuente.php");
/*
	Notas de ADOdb
	--------------
	
	- La funcion NConnect de ADODB es interesante.
	- Usar la extension en C?
*/

//Separador de campos en sentecias extraidas con SQL
define("apex_sql_separador","%%");			//Separador utilizado para diferenciar campos de valores compuestos
//Comodines concatenadores de SQL
define("apex_sql_where","%w%");
define("apex_sql_from","%f%");
//Entradas del array de conexiones.
define("apex_db_motor",0);
define("apex_db_profile",1);// host-dsn
define("apex_db_usuario",2);
define("apex_db_clave",3);
define("apex_db_base",4);
define("apex_db_con",5);//Conexion concreta ( Indice a la variable de tipo RECURSO referencia a la conexion propiamente dicha)
define("apex_db_link",6);
define("apex_db",7);
define("apex_db_link_id",8);
//--------------------------------

	/**
	*	@deprecated Desde 0.8.3, Usar dba::get_db();
	*/
	function abrir_base($id,$parm)
	{
		dba::get_db($id);
		return true;		
	}
//-------------------------------------------------------------------------------------

	/**
	*	@deprecated Desde 0.8.3, Usar dba::get_db();
	*/
	function abrir_fuente_datos($id, $proyecto=null)
	{
		$db = dba::get_db($id);
		return $db;
	}
//-------------------------------------------------------------------------------------

	/**
	*	@deprecated Desde 0.8.3, Usar dba::existe_conexion();
	*/
	function existe_conexion($id)
	{
		return dba::existe_conexion($id);
	}
//-------------------------------------------------------------------------------------
	
	/**
	*	@deprecated Desde 0.8.3, Usar dba::get_db();
	*/
	function obtener_fuente($id, $ado=null)
	//Devuelve una referencia a una fuente de datos
	{
		if(isset($ado)){
			global $ADODB_FETCH_MODE;	
			$ADODB_FETCH_MODE = $ado;
		}
		if(existe_conexion($id)){
			global $db;
			return $db[$fuente][apex_db_con];
		}else{
			return null;
		}
	}
//-------------------------------------------------------------------------------------
	/**
	*	@see db::ejecutar_transaccion()
	*/
	function ejecutar_transaccion($sentencias_sql, $fuente="instancia")
	{
		dba::get_db($fuente)->ejecutar_transaccion($sentencias_sql);
		$mensaje = "La transaccion se ha realizado satisfactoriamente";
		return array(1, $mensaje);
	}
//-------------------------------------------------------------------------------------
	
	/**
	*	@see db::abrir_transaccion()
	*/
	function abrir_transaccion($fuente=null)
	{
		dba::get_db($fuente)->abrir_transaccion();
	}
	
	/**
	*	@see db::abortar_transaccion()
	*/	
	function abortar_transaccion($fuente=null)
	{
		dba::get_db($fuente)->abortar_transaccion();
	}
	
	/**
	*	@see db::cerrar_transaccion()
	*/	
	function cerrar_transaccion($fuente=null)
	{
		dba::get_db($fuente)->cerrar_transaccion();
	}

//-------------------------------------------------------------------------------------

	/**
	*	@deprecated Desde 0.8.3, usar consultar_fuente() o db::consultar()
	*/
	function recuperar_datos($sql, $fuente="instancia", $ado=null)
/*

	ATENCION: Esta funcion hay que dejar de usarla... reemplazarla por la de abajo!

 	@@acceso: actividad
	@@desc: Recupera un ARRAY con datos a partir de un SQL
	@@param: string | Sentencia SQL
	@@param: string | Fuente de datos sobre la que se ejecuta el SQL | 'instancia'
	@@param: string | Modo de generacion de claves del array en ADODB (ADODB_FETCH_ASSOC o ADODB_FETCH_NUM) | ADODB_FETCH_ASSOC
	@@retorno: array | Estado en la posicion '0' y descripcion en la posicion '1' si ocurrio un error o los datos si la operacion fue exitosa
*/
	{
		global $db, $ADODB_FETCH_MODE;	
		//Seteo el modo de recuperar registros
		if(isset($ado)){
			$ADODB_FETCH_MODE = $ado;
		}else{
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		$rs = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$rs){
			return array("lib","No se genero el Recordset. SQL: $sql . " . $db[$fuente][apex_db_con]->ErrorMsg());
		}elseif($rs->EOF){
			return array("ok", null);
		}else{
			return array("ok", $rs->getArray());
		}
	}

//-------------------------------------------------------------------------------------
	/**
	*	@see db::consultar()
	*/
	function consultar_fuente($sql, $fuente=null, $ado=null, $obligatorio=false)
	//Dispara una execpcion si algo salio mal
	{
		return dba::get_db($fuente)->consultar($sql, $ado, $obligatorio);
	}
//-------------------------------------------------------------------------------------
	/**
	*	@see db::ejecutar_sql()
	*/
	function ejecutar_sql($sql, $fuente=null)
	//Dispara una execpcion si algo salio mal
	//El codigo de la excepsion deberia ser el SQLSTATE
	//Deberia buscarla en la FUENTE para descubrir el SQLSTATE
	{
		return dba::get_db($fuente)->ejecutar_sql($sql);
	}
//-------------------------------------------------------------------------------------
	/**
	*	@see db::recuperar_secuencia()
	*/
	function recuperar_secuencia($secuencia, $fuente="instancia")
	{
		return dba::get_db($fuente)->recuperar_secuencia($secuencia);
	}
//-------------------------------------------------------------------------------------
?>