<?php
require_once("solicitud.php");
require_once("nucleo/lib/comunicador.php");		//Comunicacion entre instancias

class solicitud_wddx extends solicitud
{
	var $msg_momento;
	var $msg_instancia;
	var $msg_usuario;
	var $datos_recibidos;

	function solicitud_wddx()
	{
		if(isset($_GET[apex_hilo_qs_item])){
			$item = explode(apex_qs_separador,$_GET[apex_hilo_qs_item]);
		}else{
            $item = explode(apex_qs_separador,apex_pa_item_inicial);		
		}
		//Esto esta bien?
		//Atencion, no se muestran los errores del monitor!!
		$usuario = apex_pa_usuario_anonimo;
		parent::solicitud($item,$usuario);
		$this->registrar_db = true;
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	{
		//Recupero el mensaje enviado
		$this->recibir_paquete();
		//Paso el control al ITEM solicitado
		parent::procesar();
 	}
//--------------------------------------------------------------------------------------------
	
	function recibir_paquete()
	{
		if(isset($_POST[apex_wddx_paquete])){
			$this->datos_recibidos = comunicador::desempaquetar( $_POST[apex_wddx_paquete] );
			//Proceso Campos TOBA
			//Instancia que emitio el paquete
			if(isset($this->datos_recibidos[apex_wddx_instancia])){
				$this->msg_instancia = $this->datos_recibidos[apex_wddx_instancia];
				unset($this->datos_recibidos[apex_wddx_instancia]);
			}else{
				$this->msg_instancia = "X";
			}
			//usuario de la instancia que envio el paquete
			if(isset($this->datos_recibidos[apex_wddx_usuario])){
				$this->msg_usuario = $this->datos_recibidos[apex_wddx_usuario];
				unset($this->datos_recibidos[apex_wddx_usuario]);
			}else{
				$this->msg_usuario = "X";
			}
			//Momento en que se creo el paquete (en el emisor)
			if(isset($this->datos_recibidos[apex_wddx_momento])){
				$this->msg_momento = $this->datos_recibidos[apex_wddx_momento];
				unset($this->datos_recibidos[apex_wddx_momento]);
			}else{
				$this->msg_momento = "";
			}
		}
	}
//--------------------------------------------------------------------------------------------

	function registrar()
	{
		global $db;
		parent::registrar( apex_pa_proyecto );
		
		//ATENCION!!
		return;
		
		if($this->registrar_db){
			$cliente = $_SERVER["REMOTE_ADDR"];
			$sql = "INSERT INTO apex_solicitud_wddx 
					(solicitud_wddx, usuario, ip, instancia, instancia_usuario, paquete)
					VALUES ('$this->id','".apex_pa_usuario_anonimo."','".$cliente."','".$this->msg_instancia .
							"','".$this->msg_usuario."','".serialize($this->datos_recibidos)."');";
			if ($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD WDDX: No se pudo registrar la solicitud: " .
				$db["instancia"][apex_db_con]->ErrorMsg());
			}
		}
	}
}
?>