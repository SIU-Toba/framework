<?php
require_once('nucleo/browser/clases/objeto_ci.php');

class ci_mensajes extends objeto_ci
{

	function __construct($id)
	{
		parent::__construct($id);
		
		//Mensaje propio del objeto
		$m_propio = $this->obtener_mensaje('info_local', array('uno', 'dos', 'tres'));
		//Mensaje a nivel global del proyecto
		$m_global = mensaje::get('info_global', array('primer', date('d/M/Y')));
	
		//Notificación de los mensajes al usuario desde el mismo objeto
		$this->informar_msg($m_propio, 'info');
		//Notificación de los mensajes al usuario utilizando un mecanismo global
		toba::get_cola_mensajes()->agregar($m_global, 'info');
	}
}

?>