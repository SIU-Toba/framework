<?php

//Atencion, no se consideran las condiciones de sesion del grupo de acceso...

class usuario_http
{
	var $usuario;
	var $clave;
	var $proyecto;
	
	function usuario_http($usuario, $clave, $proyecto)
	{
		$this->usuario = $usuario;
		$this->clave = $clave;
		$this->proyecto = $proyecto;
	}

	function info()
	{
		$dump[] = $this->usuario;
		$dump[] = $this->clave;
		$dump[] = $this->proyecto;
		ei_arbol($dump);
	}

//----------------------------------------------------------------------------------------	
	
	function autorizar()
	//Ejecuta los controles y responde con un array(status,descripcion)
	{
		//---------------------------- Controlo que la IP no este bloqueada...
		$sql = "SELECT '1' FROM apex_log_ip_rechazada WHERE ip='{$_SERVER["REMOTE_ADDR"]}'";
		$rs = toba::get_db('instancia')->consultar($sql);
		if (! empty($rs)) {
			return array(0,"Ha sido bloqueado el acceso desde la maquina '{$_SERVER["REMOTE_ADDR"]}'. Por favor contáctese con el <a href='mailto:".apex_pa_administrador."'>Administrador</a>.");
		}

		$sql = "SELECT * FROM apex_usuario WHERE usuario='{$this->usuario}'";				//1a: usuario/clave 
		$rs = toba::get_db('instancia')->consultar($sql);
		if (empty($rs)){
			return $this->error_login(1,"La combinación usuario/clave es incorrecta.");
		}else{
			if ($this->clave==$rs[0]["clave"]){										//1b: usuario/clave
				$estado = $this->validar_proyecto(); 		//1c: acceso al PROYECTO
				if(!$estado[0])return $estado;
				$estado = $this->validar_vencimiento($rs[0]["vencimiento"]); 		//2: fecha de vencimiento
				if(!$estado[0])return $estado;
				$estado = $this->validar_dia($rs[0]["dias"]);			//3: dia de la semana
				if(!$estado[0])return $estado;
				$estado = $this->validar_horario($rs[0]["hora_entrada"],$rs[0]["hora_salida"]); //4: horario de ingreso
				if(!$estado[0])return $estado;
				$estado = $this->validar_ip($rs[0]["ip_permitida"]);//5: ip desde donde se accede	
				if(!$estado[0])return $estado;
				return array(true,"Validacion OK");
			}else{
				return $this->error_login(1,"La combinación usuario/clave es incorrecta.");
			}
		}	 
	}
//----------------------------------------------------------------------------------------	

	function error_login($gravedad,$texto="")
	{
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if($gravedad>0){
			$sql = "INSERT INTO apex_log_error_login(usuario,clave,ip,gravedad,mensaje) 
					VALUES ('".$this->usuario."','".$this->clave."','".$_SERVER["REMOTE_ADDR"]."','$gravedad','$texto')";
			$rs	= $db["instancia"][apex_db_con]->Execute($sql);
		}
		$sql = "SELECT count(*) as total FROM apex_log_error_login WHERE ip='{$_SERVER["REMOTE_ADDR"]}' AND (gravedad > 0) AND ((now()-momento) < '" . apex_pa_validacion_ventana_intentos . " min')";
		$rs	  = $db["instancia"][apex_db_con]->Execute($sql);
		if ($rs->fields[0] < apex_pa_validacion_intentos){
			return array (false,$texto." Quedan " . (apex_pa_validacion_intentos - $rs->fields[0]) . " intentos antes de bloquear la IP.");
		}else{//Se supero la cantidad de intentos
			monitor::bloquear_ip($_SERVER["REMOTE_ADDR"]);
			return array (false,$texto. " La IP {$_SERVER["REMOTE_ADDR"]} ha sido bloqueada. Por favor contáctese con el Administrador.");
		}	
	}

	
	
//----------------------------------------------------------------------------------------	
//--------------  CONTROLES  -------------------------------------------------------------	
//----------------------------------------------------------------------------------------	

	function validar_vencimiento($vencimiento)
	{
		if ($vencimiento != ""){
			if (date("Y-m-d") <= $vencimiento){	
				return array(true,"Validacion vencimiento OK!");
			}else{
				return $this->error_login(0,"El fecha de vigencia del usuario ha caducado.");
			}	
		}else{
			return array(true,"No hay fecha de vencimiento");
		}
	}
//----------------------------------------------------------------------------------------

	function validar_dia($dias)
	{
		if ($dias != ""){
			$dias = decbin($dias);
			if ($dias[date("w")] == 1){
				return array(true,"Validacion dia OK!");
			}else{
				return $this->error_login(0,"No posee permisos para ingresar al sistema los dias '" . date("l") . "'.");
			}
		}else{
			return array(true,"No hay restricciones de dia");
		}
	}
//----------------------------------------------------------------------------------------

	function validar_horario($hora_entrada,$hora_salida)
	{
		if (($hora_entrada != "") && ($hora_salida != "")){
			if (($hora_entrada < date("H:i")) && ($hora_salida > date("H:i"))){
				return array(true,"Validacion Franja horaria OK!");
			}else{
				return $this->error_login(0,"No posee autorización para ingresar a las ".date("H:i")."hs . Su franja horaria es : ".$hora_entrada."-".$hora_salida.".");
			}	
		}else{
			return array(true,"No hay restricciones de horario");
		}
	}
//----------------------------------------------------------------------------------------

	function validar_ip($ip)
	{		
		if ($ip!=""){
			if ($ip == $_SERVER["REMOTE_ADDR"]){
				return array(true,"Validacion IP OK!");
			}else{
				return $this->error_login(0,"No posee autorización para ingresar desde la IP : ".$_SERVER["REMOTE_ADDR"].".");
			}	
		}else{
			return array(true,"No hay restricciones de IP");
		}
	}
//----------------------------------------------------------------------------------------

    function validar_proyecto()
    {
		global $db;
		$sql = "SELECT 1 FROM apex_usuario_proyecto WHERE usuario='{$this->usuario}' AND proyecto='{$this->proyecto}'";
		$rs	  = $db["instancia"][apex_db_con]->Execute($sql);
		if (($rs->EOF)||(!$rs)){
			return $this->error_login(0,"El usuario no posee permisos para acceder al PROYECTO ". $this->proyecto);
		}else{
			return array(true,"El usuario puede entrar en el proyecto");
		}
    }

}	
?>