<?php

//Un script usuario no deberia llamar al monitor!
//Para guardar datos tiene que usar el metodo observar de la solicitud

class monitor
{
//-----------------------------------------------------------------------------
//------------------------------  LOGS de SISTEMA  ----------------------------
//-----------------------------------------------------------------------------

	function log_sistema($tipo,$mensaje,$usuario="")
	{
		//Tipos existentes: bug, seguridad, info, falta
		$mensaje = addslashes($mensaje);
		global $db, $hilo;
		if($usuario==""){
			if(isset($hilo)) $usuario = $hilo->obtener_usuario();
		}
		if($usuario!=""){
			$sql = "INSERT INTO apex_log_sistema(usuario,log_sistema_tipo,observaciones) VALUES ('". $usuario . "','$tipo','$mensaje')";
		}else{
			$sql = "INSERT INTO apex_log_sistema(log_sistema_tipo,observaciones) VALUES ('$tipo','$mensaje')";
		}
		//echo $sql;
		$rs	= $db["instancia"][apex_db_con]->Execute($sql);
	}
//-----------------------------------------------------------------------------

	function evento($tipo,$mensaje,$usuario="",$mostrar=true,$cortar_ejecucion=true,$registrar=true)
	//El registro de BUGs es para los elementos del item, supone la existencia de una 
	//solicitud (no puede llamarse de index.php o logon.php)
	//ATENCION: cuando la solicitud no se puede crear el mensaje se muestra sin el encabezado de HTML!!!
	//--> Para probar esto hay que generar solicitudes incorrectas ( a items inaccesibles)
	{
		if(apex_solicitud_tipo == "browser"){
			if($mostrar){
				global $solicitud;
				if(is_object($solicitud)){
					if($solicitud->en_tramite){
						//Ya se envio contenido al BUFFER de salida, solo escribo un mensaje
						echo ei_mensaje($mensaje,"error");
					}else{
						 echo "<HTML><HEAD><title>" . $solicitud->info["item_nombre"] ."</title>
								<link href=" . recurso::css() ." rel='stylesheet' type='text/css'></head><body>" .
								ei_mensaje($mensaje,"error") . "</body></html>"; 
					}
				}else{
						 echo "<HTML><HEAD><title>Error</title>
								<link href=" . recurso::css() ." rel='stylesheet' type='text/css'></head><body>" .
								ei_mensaje($mensaje,"error") . "</body></html>"; 
				}
			}
		}elseif(apex_solicitud_tipo == "consola"){

			fwrite(STDERR, "\n$mensaje\n\n\n" );
		}
		if($registrar) monitor::log_sistema($tipo,$mensaje,$usuario);
		if($cortar_ejecucion) exit(1);
	}

//-----------------------------------------------------------------------------
//----------------------------  ACCIONES DE SEGURIDAD  ------------------------
//-----------------------------------------------------------------------------
	
	function bloquear_ip($ip)
	{
		global $db;
		$sql = "INSERT INTO apex_log_ip_rechazada(ip) VALUES ('$ip')";
		$db["instancia"][apex_db_con]->execute($sql);
	}
//-----------------------------------------------------------------------------

	function notificar_administrador($mensaje)
	//Enviar un MAIL al administrador
	{
	
	}
//-----------------------------------------------------------------------------

	function confundir()
	//Enviar headers de ERROR 404
	{

	}
//-----------------------------------------------------------------------------
}
?>
