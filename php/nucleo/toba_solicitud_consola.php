<?php

/**
 * Solicitud pensada para ejecutar items en la consola
 * De esta forma se cuenta con la capacidad de usar las librerias de toba
 * aunque no se tiene acceso al esquema de componentes, pensados para la arquitectura web
 */
class toba_solicitud_consola extends solicitud
{
	protected $estado_proceso = 0;
	
	function __construct($info)
	{
	    $this->info = $info;
		$_SERVER["REMOTE_ADDR"]="localhost";
		$_SERVER["REQUEST_METHOD"] = "GET";
		parent::__construct(toba::hilo()->obtener_item_solicitado(),toba::hilo()->obtener_usuario());
	}
	
	function procesar()
	{
		$accion = $this->info['basica']['item_act_accion_script'];	
		require($accion);
	}

	/**
	 * Muestra un dialogo de ayuda de la operación en base a la descripción
	 */
	function ayuda()
	{
		$rs = toba_proyecto::get_menu_consola($this->info['basica']['item_proyecto'],$this->info['basica']['item']);	
		if ($rs) {
			echo "\n**************** {$this->info['basica']['item_proyecto']} - {$this->info['basica']['item']}  *************\n";
			echo "\n --- Descripcion\n\n";
			echo $rs[0]["descripcion_breve"] ."\n";
			echo "\n --- Parametros\n\n";
			echo $rs[0]["descripcion_larga"] ."\n\n";
		} else {
			echo "No hay ayuda disponible\n";
		}
	}

	/**
	 * Registra los parametros de la llamada en un array asociativo
	 */
	function registrar_parametros()
	{
		global $argv;
		$this->parametros = array();
		for($a=6;$a<count($argv);$a++) {
			if (preg_match("/^-/",$argv[$a])) { //Es un modificador
				$pila_modificadores[$a] = $argv[$a];
				$this->parametros[$pila_modificadores[$a]] = '';
			} else {	//Es la asignacion de un modificador
				if(isset($pila_modificadores[$a-1])) {
					$this->parametros[$pila_modificadores[$a-1]]=$argv[$a];
				} else {
					echo "\n ERROR PARSEANDO PARAMETROS: Los modificadores no pueden contener espacios\n";
					echo " El error ha ocurrido entre las cadenas: \"" . $argv[$a-1] ."\" y \"". $argv[$a] ."\"\n";
					echo " (Si esta definiendo una REGEXP utilice \"/s\")\n";
					exit(222);
				}
			}	
		}
		//Seteo el modo DEBUG
		if (isset($this->parametros["--debug"])) {
			$this->debug = true;
		} else {
			$this->debug = false;
		}
		//Ayuda??
		if (isset($this->parametros["--help"])) {
			$this->ayuda();
			exit();
		}
	}
	
	/**
	 * Retorna el estado actual de la operación
	 * El estado de la operación se retorna al sistema cuando termina la operación
	 */
	function get_estado_proceso()
	{
		return $this->estado_proceso;
	}
	
	/**
	 * Cambia el estado que se retorna al sistema cuando termina la operación
	 * @param int $estado Entero entre 0 y 254
	 */
	function set_estado_proceso($estado)
	{
		$this->estado_proceso = $estado;
	}

	function registrar($llamada=null)
	{
		if(isset($llamada)){
			$str_llamada = addslashes(implode(" ",$llamada));
			echo($str_llamada);
		}else{
			$str_llamada = "";
		}
		parent::registrar();
		if($this->registrar_db){
			toba_instancia::registrar_solicitud_consola($this->id, $this->usuario, $str_llamada);
		}
	}
}
?>