<?php
require_once('nucleo/componentes/runtime/objeto.php');
require_once("nucleo/browser/interface/form.php");

/*
	Que relacion hay entre hacer todo por defecto y el ABMS?
	Forma de diferencias las dependencias UT...
	JAVASCRIPT especifico = javascript
*/

class objeto_mt extends objeto
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	var $nombre_formulario;		// privado | string | Nombre del <form> del MT
	var $transaccion_abierta;	// privado | boolean | Indica si la transaccion se encuentra en proceso
	var $estado_transaccion;	// privado | boolean | Indica el estado de la ultima ejecucio de SQL
	
	function objeto_mt($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto($id);
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		//Inicializo VARIOS
		$this->nombre_formulario = "MT_" . $this->id[1] . "_";//Cargo el nombre del <form>
		$this->submit = $this->nombre_formulario . "_submit";
		$this->transaccion_abierta = false;
		//Cargo la MEMORIA
  		$this->cargar_memoria();
		$this->flag_no_propagacion = "no_prop" . $this->id[1];
		//Cargo las DEPENDENCIAS
		$this->cargar_info_dependencias();

	}
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//----------------------------  Soporte al PROCESO   ----------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function controlar_activacion()
/*
 	@@acceso: interno
	@@desc: Determina si se activo este marco transaccional (si el submit se disparo desde el formulario HTML del mismo)
*/
	{
		if(isset($_POST[$this->submit])){
			//Apretaron el SUBMIT de este FORM
			return true;		
		}else{
			//El submit no es de este formulario, la atencion esta en otro lugar...
			return false;	
		}
	}
	//-------------------------------------------------------------------------------
	
	function establecer_tiempo_maximo($tiempo="30")
/*
 	@@acceso: interno
	@@desc: Establece el tiempo maximo de ejecucion de la SOLICITUD
	@@param: string | tiempo en segundo (0=indeterminado) | 30
*/
	{
		ini_set("max_execution_time",$tiempo);
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------  Manejo de TRANSACCIONES   --------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function procesar()
/*
 	@@acceso: actividad
	@@desc: Dispara el procesamiento de la transanccion
*/
	{
		if($this->controlar_activacion())
		{
			$this->cargar_post();
			echo ei_mensaje("Transaccion ACTIVADA!");
		}
	}
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
                  $mensaje_original = $db[$this->info["fuente"]][apex_db_con]->ErrorMsg();
                  $mensaje = $db[$this->info["fuente"]][apex_db_con]->obtener_error_toba(
         							    $db[$this->info["fuente"]][apex_db_con]->ErrorNo(),
         							    $db[$this->info["fuente"]][apex_db_con]->ErrorMsg());
                  //Si el query-string no está encriptado, entonces consideramos que
                  //en proyecto está en modo debug y muestra la sql que falló y el
                  //mensaje de error original.
                  //Si el proyecto está en producción se verán los mensajes "lindos".
                  if (apex_pa_encriptar_qs == 0)
                  {
                     $mensaje = $mensaje . '<div style="color: red;">' . 
                                '<br><br><strong>MODO DEBUG</strong><br><br>SQL: (' . $sql . ')' .
                                '<br><br>Mensaje original: ' . $mensaje_original .
                                '</div>';
                  }
						$this->registrar_info_proceso($mensaje, "error");
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
}
?>
