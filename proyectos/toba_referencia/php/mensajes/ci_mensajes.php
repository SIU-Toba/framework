<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_mensajes extends toba_ci
{
	function ini()
	{
		//Mensaje propio del objeto
		$m_propio = $this->get_mensaje('info_local', array('uno', 'dos', 'tres'));
		//Mensaje a nivel global del proyecto
		$m_global = toba::mensajes()->get('info_global', array('primer', date('d/M/Y')));
	
		//Notificaci�n de los mensajes al usuario desde el mismo objeto
		$this->informar_msg($m_propio, 'info');
		//Notificaci�n de los mensajes al usuario utilizando un mecanismo global
		toba::notificacion()->agregar($m_global, 'info');
	}
}

?>