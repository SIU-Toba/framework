<?php
class ci_recordatorio_pwd extends toba_ci
{
	protected $s__usuario;
	protected $randr;
	protected $s__email;
	private $pregunta;
	
	function ini()
	{
		//Preguntar en toba::memoria si vienen los parametros
		if (! isset($this->s__usuario)) {
			$this->s__usuario = toba::memoria()->get_parametro('usuario');
			$this->randr = toba::memoria()->get_parametro('randr');        //Esto hara las veces de unique para la renovacion
		}

		//Esto es por si el chango trata de entrar al item directamente
		$item = toba::memoria()->get_item_solicitado();
		$tms = toba_manejador_sesiones::instancia();
		if ($item[0] == 'toba_editor' && !$tms->existe_usuario_activo()) {
			throw new toba_error_ini_sesion('No se puede correr este item fuera del editor');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_usuario(toba_ei_formulario $form)
	{
		//Probablemente esto vaya vacio a excepcion del usuario si es que se pasa
		if (isset($this->s__usuario) && (!is_null($this->s__usuario))) {
			$form->set_datos_defecto(array('usuario' => $this->s__usuario));
			$form->set_solo_lectura(array('usuario'));
		}
	}

	function evt__form_usuario__enviar($datos)
	{
		//Miro que vengan los datos que necesito
		if (! isset($datos['usuario'])) {
			throw new toba_error_autenticacion('No se suministro un usuario válido');
		}

		//Si el usuario existe, entonces disparo el envio de mail 
		if (! $this->verificar_usuario_activo($datos['usuario'])) {
			throw new toba_error_autenticacion('No se suministro un usuario válido');
		} 
		$this->set_pantalla('pant_pregunta');
		$this->s__usuario = $datos['usuario'];
		$this->s__email = $this->recuperar_direccion_mail_usuario($this->s__usuario);
	}

	//-----------------------------------------------------------------------------------
	//---- form_pregunta ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_pregunta(toba_ei_formulario $form)
	{
		//$datos = $this->recuperar_pregunta_secreta($this->s__usuario);
		if (! is_null($this->pregunta)) {
			unset($this->pregunta['respuesta']);
		}
		$form->set_datos($this->pregunta);
	}

	function evt__form_pregunta__modificacion($datos)
	{
		$this->verificar_desafio_secreto($datos);    
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__recordame()
	{
		//Primero verifico que se haya cumplimentado con el periodo minimo de vida de la contraseña
		$dias = toba_parametros::get_clave_validez_minima(toba::proyecto()->get_id());
		if (! is_null($dias)) {
			if (! toba_usuario::verificar_periodo_minimo_cambio($this->s__usuario, $dias)) {
				toba::notificacion()->agregar('No transcurrio el período minimo para poder volver a cambiar su contraseña. Intentelo en otra ocasión');
				return;
			}
		}
		
		//Si llego hasta aca es porque la respuesta funco, sino explota en la modificacion del form        
		$this->enviar_mail_aviso_cambio();
		toba::notificacion()->agregar('Se ha enviado un mail a la cuenta especificada, por favor verifiquela', 'info');
		$this->set_pantalla('pant_inicial');
	}
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		//Si viene con el random seteado es que esta confirmando el cambio de contraseña
		if (isset($this->randr) && ! is_null($this->randr)) {
			$pantalla->eliminar_dep('form_usuario');
			$this->disparar_confirmacion_cambio();
			toba::notificacion()->agregar('La nueva contraseña fue enviada a su cuenta de mail.', 'info');
		}
	}
	
	function conf__pant_pregunta(toba_ei_pantalla $pantalla)
	{
		$this->pregunta = $this->recuperar_pregunta_secreta($this->s__usuario);
		if (is_null($this->pregunta)) {
			$pantalla->eliminar_dep('form_pregunta');
			$pantalla->set_descripcion('Presione el botón para continuar con el proceso');
		} else {
			$pantalla->set_descripcion('Responda la pregunta y presione el botón para continuar con el proceso');
		}
	}
	
	//----------------------------------------------------------------------------------------
	//-------- Procesamiento del pedido ------------------------------------------
	//----------------------------------------------------------------------------------------
	/*
		* Verifico que el usuario existe a traves de la API de toba_usuario
		*/
	function verificar_usuario_activo($usuario)
	{
		try {
			toba::instancia()->get_info_usuario($usuario);        //Tengo que verificar que el negro existe
		} catch (toba_error_db $e) {                        //Ni true ni false... revienta... el mono no existe
			toba::logger()->error('Se intento modificar la clave del usuario:' . $usuario);
			return false;
		}
		return true;
	}

	function verificar_desafio_secreto($datos_usuario)
	{
		$datos = $this->recuperar_pregunta_secreta($this->s__usuario);
		if (! is_null($datos)) {
			$hasher = new toba_hash();
			$clave1 = $hasher->hash(trim($datos['respuesta']));
			$verified = $hasher->verify(trim($datos_usuario['respuesta']), $clave1);
			if (! $verified) {
				toba::logger()->error("Se intento cambiar la clave al usuario: {$this->s__usuario} pero falló la respuesta al desafío");
				throw new toba_error('Respuesta no Válida');
			}
		}
	}
	
	/**
		* Recupera la direccion de mail de usuario
		* @param string $usuario
		* @return string 
		*/
	function recuperar_direccion_mail_usuario($usuario)
	{
		try {
			$datos = toba::instancia()->get_info_usuario($usuario);        //Tengo que verificar que el negro existe
			return $datos['email'];
		} catch (toba_error $e) {                        
			toba::logger()->error('Se intento modificar la clave del usuario:' . $usuario);
			return null;
		}
	}
	
	/**
		* Recupera pregunta/respuesta para el desafio
		* @param string $usuario
		* @return array 
		*/
	function recuperar_pregunta_secreta($usuario)
	{
		try {
			$aux = null;
			$datos = toba::instancia()->get_pregunta_secreta($usuario);
			if (! is_null($datos)) {
				$clave = toba::instalacion()->get_claves_encriptacion();        
				$aux['pregunta'] = mcrypt_decrypt(MCRYPT_BLOWFISH, $clave['get'], base64_decode($datos['pregunta']), MCRYPT_MODE_CBC, substr($clave['db'], 0, 8));
				$aux['respuesta'] = mcrypt_decrypt(MCRYPT_BLOWFISH, $clave['get'], base64_decode($datos['respuesta']), MCRYPT_MODE_CBC, substr($clave['db'], 0, 8));
			}
			return $aux;
		} catch (toba_error $e) {
			toba::logger()->error('Se intento modificar la clave del usuario:' . $usuario);
			return array();
		}        
	}
		
	/*
		* Aca envio un primer mail con un link para confirmar el cambio, si no lo usa... fue
		*/
	function enviar_mail_aviso_cambio()
	{
		//Genero un pseudorandom unico... 
		$tmp_rand = $this->get_random_temporal();
		$link = $this->generar_link_confirmacion($this->s__usuario, $tmp_rand);    //Genero el link para el mail
		 
		//Se envia el mail a la direccion especificada por el usuario.
		$asunto = 'Solicitud de cambio de contraseña';
		$cuerpo_mail = '<p>Este mail fue enviado a esta cuenta porque se <strong>solicito un cambio de contraseña</strong>.'
		. 'Si usted solicito dicho cambio haga click en el siguiente link: </br></br>'
		. $link. '</br> El mismo será válido unicamente por 24hs.</p>';

		//Guardo el random asociado al usuario y envio el mail
		toba::instancia()->get_db()->abrir_transaccion();
		try {
			$this->guardar_datos_solicitud_cambio($tmp_rand, $this->s__email);
			$mail = new toba_mail($this->s__email, $asunto, $cuerpo_mail);
			$mail->set_html(true);
			$mail->enviar();
			toba::instancia()->get_db()->cerrar_transaccion();
		} catch (toba_error $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			toba::logger()->debug('Proceso de envio de random a cuenta: '. $e->getMessage());
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');
		}
	}

	/*
		* Deberia generar un random.. quien sabe que tan bueno o malo sea
		*/
	function get_random_temporal()
	{
		$uuid = uniqid(rand(), true);
		$rnd = sha1(microtime() . $uuid . rand());
		return $rnd;
	}

	/*
		* Obtiene una url con los parametros necesarios para que se haga la confirmacion
		*/
	function generar_link_confirmacion($usuario, $random)
	{
		$proto = toba_http::get_protocolo();
		$servidor = toba_http::get_nombre_servidor();
		$path = toba::proyecto()->get_www();
		$opciones = array('param_html' => array('tipo' => 'normal' , 'texto' => 'Click Aqui'), 'prefijo' => $proto. $servidor. $path['url']);
		$parametros = array('usuario' => $usuario, 'randr' => $random);
		return toba::vinculador()->get_url(null, null, $parametros, $opciones);
	}
	
	/*
	* Impacta en la base para cambiar la contraseña del usuario
	*/
	function disparar_confirmacion_cambio()
	{
		//Recupero mail del usuario junto con el hash de confirmacion
		$datos_rs = $this->recuperar_datos_solicitud_cambio($this->s__usuario, $this->randr);
		if (empty($datos_rs)) {
			toba::logger()->debug('Proceso de cambio de contraseña en base: El usuario o el random no coinciden' );
			toba::logger()->var_dump(array('rnd' => $this->randr));
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');            
		} else {
			$datos_orig = current($datos_rs);
		}
				
		//Aca tengo que generar una clave temporal y enviarsela para que confirme el cambio e ingrese con ella.
		do {			
			try {
				$claveok = true;
				$clave_tmp = toba_usuario::generar_clave_aleatoria('10');
				toba_usuario::verificar_composicion_clave($clave_tmp, 10);
				toba_usuario::verificar_clave_no_utilizada($clave_tmp, $datos_orig['id_usuario']);	
			} catch(toba_error_pwd_conformacion_invalida $e) {
				$claveok = false;
			} catch(toba_error_usuario $e) {
				toba::logger()->error('Se estan generando claves aleatorias repetidas!! '. $clave_tmp);				//Debe aparecer en el log para revisar la generacion de la clave aleatoria
				$claveok = false;
			}
		} while(! $claveok);
		
		//Armo el mail nuevo
		$asunto = 'Nueva contraseña';
		$cuerpo_mail = '<p>Se ha recibido su confirmación exitosamente, su contraseña fue cambiada a: </br>' .
		$clave_tmp . '</br> Por favor en cuanto pueda cambiela a una contraseña más segura. </br> Gracias. </p> ';
		
		//Cambio la clave del flaco, envio el nuevo mail y bloqueo el random
		toba::instancia()->get_db()->abrir_transaccion();
		try {
			//Recupero los dias de validez de la clave, si existe
			$dias = toba_parametros::get_clave_validez_maxima(toba::proyecto()->get_id());
			
			//Seteo la clave para el usuario
			toba_usuario::reemplazar_clave_vencida($clave_tmp, $datos_orig['id_usuario'], $dias);
			toba_usuario::forzar_cambio_clave($datos_orig['id_usuario']);
			
			//Enviar nuevo mail con la clave temporaria
			$mail = new toba_mail($datos_orig['email'], $asunto, $cuerpo_mail);
			$mail->set_html(true);
			$mail->enviar();

			//Bloqueo el pedido para que no pueda ser reutilizado
			$this->bloquear_random_utilizado($this->s__usuario, $this->randr);
			toba::instancia()->get_db()->cerrar_transaccion();
		} catch (toba_error $e) {
			toba::instancia()->get_db()->abortar_transaccion();
			toba::logger()->debug('Proceso de cambio de contraseña en base: ' . $e->getMessage());
			throw new toba_error('Se produjo un error en el proceso de cambio, contactese con un administrador del sistema.');
		}
	}

	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------
	//                                        METODOS PARA SQLs
	//-----------------------------------------------------------------------------------------------------------------------------------------------------------------
	function guardar_datos_solicitud_cambio($random, $mail)
	{
		$sql = 'UPDATE apex_usuario_pwd_reset SET bloqueado = 1 WHERE usuario = :usuario;';
		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$up_sql = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($up_sql, array('usuario'=>$this->s__usuario));

		$sql = 'INSERT INTO apex_usuario_pwd_reset (usuario, random, email) VALUES (:usuario, :random, :mail);';
		//toba::logger()->debug(array('usuario'=>$this->usuario, 'random' => $random, 'mail' => $mail));
		$in_sql = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($in_sql, array('usuario'=>$this->s__usuario, 'random' => $random, 'mail' => $mail));
	}
	
	function recuperar_datos_solicitud_cambio($usuario, $random)
	{
		$sql = "SELECT  usuario as id_usuario,
										email
						FROM apex_usuario_pwd_reset
						WHERE    usuario = :usuario
						AND random = :random
						AND age(now() , validez)  < interval '1 day'
						AND bloqueado = 0;";

		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_consultar($id, array('usuario'=>$usuario, 'random' => $random));
		return $rs;
	}

	function bloquear_random_utilizado($usuario, $random)
	{
		$sql = 'UPDATE apex_usuario_pwd_reset  SET bloqueado = 1
						WHERE     usuario = :usuario
						AND random = :random';
		//toba::instancia()->get_db()->set_modo_debug(true, true);
		$id = toba::instancia()->get_db()->sentencia_preparar($sql);
		$rs = toba::instancia()->get_db()->sentencia_ejecutar($id, array('usuario'=>$usuario, 'random' => $random));
	}
}
?>