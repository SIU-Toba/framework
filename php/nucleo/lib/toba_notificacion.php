<?php

/**
 * Clase que mantiene notificaciones al usuario a mostrarse en el página actual
 * 
 * @package SalidaGrafica
 * @jsdoc notificacion notificacion 
 */
class toba_notificacion
{
	private $mensajes = array();
	static private $instancia;
	protected $titulo;
	
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
	 * @param string $mensaje Mensaje completo a mostrar
	 * @param string $nivel Determina el estilo del mensaje, 'error' o 'info' 
	 */
	function agregar($mensaje, $nivel='error',$extras=null)
	{
		if (! is_null($mensaje) && trim($mensaje) != '') {
			$this->mensajes[] = array($mensaje, $nivel, $extras);
			toba::logger()->debug("Mensaje a usuario: ".$mensaje, 'toba');
		}
	}
	
	/**
	 * Agrega un mensaje de error para mostrar al usuario
	 * @param string $mensaje Mensaje completo a mostrar
	 */
	function error($mensaje, $extras=null)
	{
		$this->agregar($mensaje, 'error', $extras);
	}
	
	/**
	 * Agrega un mensaje de advertencia para mostrar al usuario
	 * @param string $mensaje Mensaje completo a mostrar
	 */
	function warning($mensaje, $extras=null)
	{
		$this->agregar($mensaje, 'warning', $extras);
	}	
	
	/**
	 * Agrega un mensaje informativo para mostrar al usuario
	 * @param string $mensaje Mensaje completo a mostrar
	 */	
	function info($mensaje, $extras=null)
	{
		$this->agregar($mensaje, 'info', $extras);
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

	function set_titulo($titulo)
	{
		if (! is_null($titulo)) {
			$this->titulo = $titulo;
		}
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
	function mostrar($incluir_comsumos=true)
	{
		if ($incluir_comsumos) {
			toba_js::cargar_consumos_basicos(); //Por si no se cargaron antes
			toba_js::cargar_consumos_globales(array("basicos/notificacion"));
			echo toba_js::abrir();
		}
		foreach($this->mensajes as $mensaje){
			$texto = toba_parser_ayuda::parsear($mensaje[0]);
			$texto = str_replace("'", '"', $texto);
			$texto = toba_js::string($texto);
			//Mensaje para debug
			if (isset($mensaje[2]) && trim($mensaje[2]) != '') {
				$texto_debug = toba_parser_ayuda::parsear($mensaje[2]);
				$texto_debug = str_replace("'", '"', $texto_debug);
				$texto_debug = toba_js::string($texto_debug);	
				echo "notificacion.agregar('$texto' + '\\n', '{$mensaje[1]}', undefined, '$texto_debug');\n";
			}else{
				echo "notificacion.agregar('$texto' + '\\n', '{$mensaje[1]}');\n";	
			}			
		}
		if (isset($this->titulo)) {
			echo "notificacion.set_titulo_ventana('{$this->titulo}');\n";
		}
		echo "notificacion.mostrar();\n";
		if ($incluir_comsumos) {
			echo toba_js::cerrar();
		}
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
