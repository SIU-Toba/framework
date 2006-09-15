<?php

/**
 * Clase que mantiene notificaciones al usuario a mostrarse en el página actual
 * 
 * @package SalidaGrafica
 */
class toba_notificacion
{
	private $mensajes = array();
	static private $instancia;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_notificacion();
		}
		return self::$instancia;		
	}
	
	private function __construct()
	{	
	}
	
	/**
	 * Agrega un mensaje a mostrar al usuario
	 *
	 * @param string $mensaje Mensaje completo a mostrar
	 * @param string $nivel Determina el estilo del mensaje, 'error' o 'info' 
	 */
	function agregar($mensaje, $nivel='error')
	{
		$this->mensajes[] = array($mensaje, $nivel);
		toba::logger()->debug("Mensaje a usuario: ".$mensaje, 'toba');
	}

	/**
	 * Agrega un mensaje a mostrar al usuario, el mensaje se obtiene con 
	 * toba::mensajes()->get($indice, $parametros)
	 *
	 * @param string $nivel Determina el estilo del mensaje, 'error' o 'info' 
	 * @see toba_mensajes
	 */	
	function agregar_id($indice, $parametros=null, $nivel='error')
	{
		$this->agregar(toba::mensajes()->get($indice, $parametros), $nivel);
	}

	/**
	 * Reporta la existencia de mensajes
	 * @return boolean 
	 */
	function verificar_mensajes()
	{
		if(count($this->mensajes)>0) return true;
	}


	/**
	 * Muestra toda la lista de notificaciones almacenadas
	 * Esto tiene que hacerse una única vez por página, y por lo generar el framework 
	 * es el encargado de hacerlo
	 */
	function mostrar()
	{
		toba_js::cargar_consumos_basicos(); //Por si no se cargaron antes
		toba_js::cargar_consumos_globales(array("basicos/notificacion"));
		echo toba_js::abrir();
		foreach($this->mensajes as $mensaje){
			$texto = str_replace("'", "¨", $mensaje[0]);
			$texto = toba_js::string($texto);
			echo "notificacion.agregar('$texto' + '\\n', '{$mensaje[1]}');\n";
		}
		echo "notificacion.mostrar()\n";
		echo toba_js::cerrar();
	}
	
	/**
	 * Borra todas las notificaciones existentes
	 */
	function vaciar()
	{
		$this->mensajes = array();
	}
}
?>