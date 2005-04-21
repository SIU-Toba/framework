<?php
require_once("nucleo/negocio/objeto_cn.php");	//Ancestro de todos los OE

class objeto_cn_t extends objeto_cn
{
	protected $transaccion_abierta;			// privado | boolean | Indica si la transaccion se encuentra en proceso
	protected $estado_transaccion;			// privado | boolean | Indica el estado de la ultima ejecucio de SQL

	function __construct($id)
	{
		parent::__construct($id);
		$this->transaccion_abierta = false;
		$this->evt__inicializar();		
	}

	function evt__inicializar()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}	

	function evt__limpieza_memoria($no_borrar=null)
	{
		$this->log->debug( $this->get_txt() . "[ evt__limpieza_memoria ]");
		//$this->borrar_memoria();
		$this->eliminar_estado_sesion($no_borrar);
		$this->evt__inicializar();
	}

	//-------------------------------------------------------------------------------
	//------------------  PROCESAMIENTO  --------------------------------------------
	//-------------------------------------------------------------------------------

	function cancelar()
	{
		$this->log->debug( $this->get_txt() . "[ cancelar ]");
		$this->evt__limpieza_memoria();
	}
	//-------------------------------------------------------------------------------

	function procesar($parametros=null)
	//ATENCION: ignore_user_abort() //Esto puede ser importante!!!!
	{
		$this->log->debug( $this->get_txt() . "[ procesar ]");
		try {
			//ignore_user_abort();				//------> ?????
			$this->iniciar_transaccion();
			$this->evt__validar_datos();
			$this->evt__procesar_especifico($parametros);
			$this->finalizar_transaccion();
			$this->evt__limpieza_memoria();
		}
		catch(excepcion_toba $e){
			$this->abortar_transaccion();
			$this->log->debug($e);	
			throw new excepcion_toba( $e->getMessage() );
		}
	}
	//-------------------------------------------------------------------------------

	function evt__validar_datos()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}
	//-------------------------------------------------------------------------------

	function evt__procesar_especifico()
	{
		//Esto hay que redeclararlo en los HIJOS	
	}

	//-------------------------------------------------------------------------------
	//------------------  Manejo de TRANSACCIONES  ----------------------------------
	//-------------------------------------------------------------------------------

	function iniciar_transaccion()
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
}
?>