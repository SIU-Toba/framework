<?php
require_once("nucleo/negocio/objeto_cn.php");	//Ancestro de todos los OE

class objeto_cn_t extends objeto_cn
{
	var $buffer;
	var $transaccion_abierta;	// privado | boolean | Indica si la transaccion se encuentra en proceso
	var $estado_transaccion;	// privado | boolean | Indica el estado de la ultima ejecucio de SQL
	var $posicion_finalizador;		//Posicion del objeto en el array de finalizacion

	function __construct($id, $resetear=false)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::__construct($id, $resetear);
		$this->transaccion_abierta = false;
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//------------------  Manejo de TRANSACCIONES  ----------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function iniciar_transaccion()
/*
 	@@acceso: interno
	@@desc: Inicia una TRANSACCION
*/
	{
		global $db;
		$this->transaccion_abierta = true;
		$sql = "BEGIN TRANSACTION";
		$status = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$status){
			$this->registrar_info_proceso("No es posible iniciar la TRANSACCION ( " 
										.$db[$this->info["fuente"]][apex_db_con]->ErrorMsg()." )","error");
		}
		return $status;
	}
	//-------------------------------------------------------------------------------
	
	function finalizar_transaccion($mensaje=null)
/*
 	@@acceso: interno
	@@desc: Finaliza una TRANSACCION
*/
	{
		global $db;
		$sql = "COMMIT TRANSACTION";
		$status = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$status){
			$this->registrar_info_proceso("No es posible finalizar la TRANSACCION ( " 
										.$db[$this->info["fuente"]][apex_db_con]->ErrorMsg()." )","error");
		}else{
			$this->transaccion_abierta = false;
			//Mensaje de TODO OK
			if(isset($mensaje)){
				$this->registrar_info_proceso($mensaje);
			}
		}
		return $status;
	}
	//-------------------------------------------------------------------------------
	
	function abortar_transaccion($mensaje=null)
/*
 	@@acceso: interno
	@@desc: Aborta una TRANSACCION
*/
	{
		global $db;
		$this->transaccion_abierta = false;
		$sql = "ROLLBACK TRANSACTION";
		$status = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$status){
			$this->registrar_info_proceso("No es posible abortar la TRANSACCION ( " 
										.$db[$this->info["fuente"]][apex_db_con]->ErrorMsg()." )","error");
		}
		if(isset($mensaje)){
			$this->registrar_info_proceso($mensaje,"error");
		}
		return $status;
	}
	//-------------------------------------------------------------------------------

	function ejecutar_sql($sentencias_sql, $registrar_error=true)
/*
 	@@acceso: interno
	@@desc: Ejecuta un conjunto de sentencias SQL. Corta la ejecucion con el primer error
	@@param: mixed | Array con sentencias SQL a procesar o String con un sola sentencia
	@@retorno: boolean | Estado de la ejecucion
*/
	{
		if($this->transaccion_abierta){
			global $db;
			$this->estado_transaccion = true;
			if(!is_array($sentencias_sql)){
				$sentencias_sql = array($sentencias_sql);
			}
			foreach($sentencias_sql as $sql){
				//echo $sql;
				$status_temp =  $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
				if(!$status_temp){
					$this->estado_transaccion = false;
					if($registrar_error){
						$this->registrar_info_proceso("Error ejecutando SQL (".
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg()." ) $sql","error");
//TEST
							$db[$this->info["fuente"]][apex_db]->obtener_error_toba(
							$db[$this->info["fuente"]][apex_db_con]->ErrorNo(),
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg());
//*/
					}
					break;
				}
			}
			return $this->estado_transaccion;
		}else{
			$this->registrar_info_proceso("No es posible ejecutar SQL si no se abrio una TRANSACCION","error");
			return false;
		}
	}
	//-------------------------------------------------------------------------------

	function procesar()
	//Esto hay que redeclararlo en los HIJOS
	{
		try
		{
			$this->iniciar_transaccion();
			$this->procesar_especifico();
			$this->finalizar_transaccion();
		}catch(excepcion_toba $e){
			$this->abortar_transaccion();			
			$this->solicitud->log->registrar_excepcion($e);
		}
	}

	//-------------------------------------------------------------------------------
}
?>
