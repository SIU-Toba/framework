<?php

namespace rest\seguridad\autenticacion;


interface usuarios_usuario_password {


	/**
	 * Retorna si el usuario password es valido
	 */
	function es_valido($user, $pass);

} 