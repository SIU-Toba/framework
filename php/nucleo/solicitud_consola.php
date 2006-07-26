<?php
require_once("solicitud.php");

class solicitud_consola extends solicitud
{
	var $debug;	//Modo debug activado
	var $estado_proceso;

	function solicitud_consola($info)
	{
	    $this->info = $info;
		$_SERVER["REMOTE_ADDR"]="localhost";
		$_SERVER["REQUEST_METHOD"] = "GET";
		parent::__construct(toba::get_hilo()->obtener_item_solicitado(),toba::get_hilo()->obtener_usuario());
		$this->tipo_actividad	= "accion";	
		$this->estado_proceso = 0;
	}
//--------------------------------------------------------------------------------------------

	function controlar_permisos()
	{
		return array(true, "No se chequean permisos en el acceso por consola");			
	}
//--------------------------------------------------------------------------------------------

	function ayuda()
	//Atrapo las llamadas a la ayuda... sino proceso
	{
		$rs = info_proyecto::get_menu_consola($this->info['basica']['item_proyecto'],$this->info['basica']['item']);	
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
//--------------------------------------------------------------------------------------------

	function registrar_parametros()
	//Registra los parametros de la llamada en un array asociativo
	{
		global $argv;
		$this->parametros = array();
		for($a=6;$a<count($argv);$a++)
		{
			if(preg_match("/^-/",$argv[$a]))//Es un modificador
			{
				$pila_modificadores[$a] = $argv[$a];
				$this->parametros[$pila_modificadores[$a]] = '';
			}else{	//Es la asignacion de un modificador
				if(isset($pila_modificadores[$a-1]))
				{
					$this->parametros[$pila_modificadores[$a-1]]=$argv[$a];
				}else{
					echo "\n ERROR PARSEANDO PARAMETROS: Los modificadores no pueden contener espacios\n";
					echo " El error ha ocurrido entre las cadenas: \"" . $argv[$a-1] ."\" y \"". $argv[$a] ."\"\n";
					echo " (Si esta definiendo una REGEXP utilice \"/s\")\n";
					exit(222);
				}
			}		
		}
		//Seteo el modo DEBUG
		if(isset($this->parametros["--debug"])){
			$this->debug = true;
		}else{
			$this->debug = false;
		}
		//Ayuda??
		if(isset($this->parametros["--help"])){
			$this->ayuda();
			exit();
		}		
	}
//--------------------------------------------------------------------------------------------
	
	function obtener_estado_proceso()
	{
		return $this->estado_proceso;
	}

//--------------------------------------------------------------------------------------------

	function depurar($variable, $nota, $recuadro=true)
	//Mostrar variables si estoy ejecutando en modo DEBUG
	{
		$separador = "---------------------------------------------------------------------------------";
		if($this->debug){
			if($recuadro){
				fwrite(STDERR, "\n$separador\n" );
				fwrite(STDERR, "---|   " . $nota ."\n" );
				fwrite(STDERR, "$separador\n\n" );
				fwrite(STDERR, $this->imprimir_variable($variable) ."\n") ;
				fwrite(STDERR, "$separador\n\n" );
			}else{
				fwrite(STDERR, $this->imprimir_variable($variable) ."\n");
			}
		}
	}
//--------------------------------------------------------------------------------------------

    function imprimir_variable($variable)
    {
        if(is_array($variable)){
            return var_dump($variable);
        }else{
            return $variable;
        }
    }
//--------------------------------------------------------------------------------------------

	function registrar($llamada=null)
	{
		global $db;
		if(isset($llamada)){
			$str_llamada = addslashes(implode(" ",$llamada));
			echo($str_llamada);
		}else{
			$str_llamada = "";
		}
		parent::registrar();
		if($this->registrar_db){
			info_instancia::registrar_solicitud_consola($this->id, $this->usuario, $str_llamada);
		}
	}
}
?>