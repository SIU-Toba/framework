<?php
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
require("3ros/adodb340/adodb.inc.php");
//--------------------------------

	function abrir_base($id,$parm)
/*
 	@@acceso: actividad
	@@desc: Agrega una conexion al array de bases GLOBAL $db
	@@param: string | ID de la nueva conexion (referencia asociativa del array global '$db')
	@@param: array | array con la informacion necesaria para crear la conexion (apex_db_motor, apex_db_profile, apex_db_usuario, apex_db_clave, apex_db_base, apex_db_con, apex_db_link )
	@@retorno: boolean | true si la conexion pudo abrirse, false en el caso contrario
*/
	{
		//echo "ID: $id <br>"; echo "PARAMETROS: "; print_r($parm); echo "<br>";
		global $db;
		if(!isset($db[$id])){//Existe una conexion con ese ID?
			//La conexion es un LINK a la conexion primaria de la INSTANCIA?
			if(isset($parm[apex_db_link])){
				if($parm[apex_db_link]==1){
					//La fuente solicita un LINK a un elemento del archivo de INSTANCIAS
					if(isset($parm[apex_db_link_id])){
						if(trim($parm[apex_db_link_id])!=""){
							global $instancia;
							//Existe una descripcion de esa instancia?
							if(isset($instancia[$parm[apex_db_link_id]])){
		    					return abrir_base($id,$instancia[$parm[apex_db_link_id]]);
							}else{
								throw new excepcion_toba("ABRIR_BASE: no existe el indice en 'instancias.php'");
							}
						}
					}
					$db[$id] =& $db["instancia"];
				}
			//Creo la conexion solicitada
			}else{
				if( $db[$id][apex_db_con] =& ADONewConnection($parm[apex_db_motor]) ){
					$ok = $db[$id][apex_db_con]->NConnect($parm[apex_db_profile],$parm[apex_db_usuario],$parm[apex_db_clave],$parm[apex_db_base]);
					if( $ok ){
						//Dejo guardados los parametros de conexion
						$db[$id][apex_db_motor] = $parm[apex_db_motor];
						$db[$id][apex_db_profile] = $parm[apex_db_profile];
						$db[$id][apex_db_usuario] = $parm[apex_db_usuario];
						$db[$id][apex_db_clave] = $parm[apex_db_clave];
						$db[$id][apex_db_base] = $parm[apex_db_base];
						$sentencia = "\$db[\$id][apex_db] = new fuente_datos_".$db[$id][apex_db_motor]."( \$db[\$id][apex_db_con] );";
						//echo $sentencia;
						eval($sentencia);
					}else{
						return false;
					}
				}else{		//Se creo la conexion?
					return false;
				}
			}
		}
		return true;
	}
//-------------------------------------------------------------------------------------
	
	function abrir_fuente_datos($id, $proyecto=null)
/*
 	@@acceso: actividad
	@@desc: Abre una conexion con una fuente de datos especifica
	@@param: string | Identificador de la fuente de datos
*/
	{
		global $db, $ADODB_FETCH_MODE, $solicitud;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if(isset($proyecto)){
			$proyecto_fuente = $proyecto;
		}else{
			$proyecto_fuente = $solicitud->hilo->obtener_proyecto();
		}
		$sql = "SELECT 	*
				FROM 	apex_fuente_datos
				WHERE	fuente_datos = '$id'
				AND		proyecto = '$proyecto_fuente';";
		//echo $sql;
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","ABRIR FUENTE: No se genero el recordset. " . $db["instancia"][apex_db_con]->ErrorMsg(). " </b> -- SQL: $sql --");
		}
		if($rs->EOF){
			monitor::evento("bug","ABRIR FUENTE: No hay informacion sobre la FUENTE seleccionada.");
		}
		$parm[apex_db_motor] = $rs->fields["fuente_datos_motor"];
		$parm[apex_db_profile] = $rs->fields["host"];
		$parm[apex_db_usuario] = $rs->fields["usuario"];
		$parm[apex_db_clave] = $rs->fields["clave"];
		$parm[apex_db_base] = $rs->fields["base"];
		$parm[apex_db_link] = $rs->fields["link_instancia"];
		$parm[apex_db_link_id] = $rs->fields["instancia_id"];
		abrir_base($id, $parm);
	}
//-------------------------------------------------------------------------------------

	function existe_conexion($id)
/*
 	@@acceso: actividad
	@@desc: Responde si exite una conexion con un ID especifico
	@@retorno: boolean | true si la conexion existe
*/
	{
		return isset($db[$id]);
	}
//-------------------------------------------------------------------------------------
	
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
	
	function ejecutar_transaccion($sentencias_sql, $fuente="instancia")
/*
 	@@acceso: actividad
	@@desc: Ejecuta un ARRAY de sentencias SQL como una transaccion.
*/
	{
		global $db;
		$sentencia_actual = 1;
		$db[$fuente][apex_db_con]->Execute("BEGIN TRANSACTION");
		foreach( $sentencias_sql as $sql )
		{
			if( $db[$fuente][apex_db_con]->Execute($sql) === false){
				$mensaje = "Ha ocurrido un error en la sentencia: $sentencia_actual -- ( " . 
							$db[$fuente][apex_db_con]->ErrorMsg() . " )";
				$db[$fuente][apex_db_con]->Execute("ROLLBACK TRANSACTION");
				return array(0, $mensaje);
			}
			$sentencia_actual++;
		}
		$db[$fuente][apex_db_con]->Execute("COMMIT TRANSACTION");
		$mensaje = "La transaccion se ha realizado satisfactoriamente";
		return array(1, $mensaje);
	}
//-------------------------------------------------------------------------------------
	
	function abrir_transaccion($db){}
	function abortar_transaccion($db){}
	function cerrar_transaccion($db){}

//-------------------------------------------------------------------------------------

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

	function consultar_fuente($sql, $fuente="instancia", $ado=null, $obligatorio=false)
	//Dispara una execpcion si algo salio mal
	{
		global $db, $ADODB_FETCH_MODE;	
		if(isset($ado)){
			$ADODB_FETCH_MODE = $ado;
		}else{
			$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		}
		if(!isset($db[$fuente])){
			throw new excepcion_toba("La fuente de datos no se encuentra disponible. " );
		}
		$rs = $db[$fuente][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("No se genero el Recordset. " . $db[$fuente][apex_db_con]->ErrorMsg() );
		}elseif($rs->EOF){
			if($obligatorio){
				throw new excepcion_toba("La consulta no devolvio datos. " );
			}else{
				return null;
			}
		}else{
			return $rs->getArray();
		}
	}
//-------------------------------------------------------------------------------------
?>