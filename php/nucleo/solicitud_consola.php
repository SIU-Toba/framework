<?php
require_once("solicitud.php");

class solicitud_consola extends solicitud
{
	var $debug;	//Modo debug activado
	var $estado_proceso;

	function solicitud_consola($proyecto, $item, $usuario)
	{
	    $this->info = self::get_definicion_item($proyecto, $item);
	    
		//--[ 2 ]-- Si el tipo de solicitud es WEB, emulo el ambiente
		//if( $this->tipo_solicitud() == "browser" )
		//{
			//El item solicitado es de tipo BROWSER.
			//Emulo el ambiente WEB.
			//Seria interesante tener un ITEM serializador de sesiones y un 
			//mecanismo para levantar de esta forma una sesion especifica
			$_SERVER["REMOTE_ADDR"]="localhost";
			$_SERVER["REQUEST_METHOD"] = "GET";
			require_once("nucleo/consola/emular_web_pa.php");
			require_once("nucleo/consola/emular_web_inc.php");
			sesion::abrir($usuario, $proyecto);
			require_once("nucleo/browser/hilo.php");
			$this->hilo = toba::get_hilo();
		//}
		parent::__construct($item, $usuario);
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
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;		
		$sql ="	SELECT descripcion_breve, descripcion_larga
				FROM apex_item_info
				WHERE item_proyecto = '".$this->info["item_proyecto"]."'
				AND item = '".$this->info["item"]."';";
		$rs = $db["instancia"][apex_db_con]->Execute($sql);	
		if( ($rs) && ( !($rs->EOF) ) ){
			echo "\n**************** {$this->info["item_proyecto"]} - {$this->info["item"]}  *************\n";
			echo "\n --- Descripcion\n\n";
			echo $rs->fields["descripcion_breve"] ."\n";
			echo "\n --- Parametros\n\n";
			echo $rs->fields["descripcion_larga"] ."\n\n";
		}else{
			echo "No hay ayuda disponible\n";
		}
	}
//--------------------------------------------------------------------------------------------

	function registrar_parametros()
	//Registra los parametros de la llamada en un array asociativo
	{
		global $argv;
		$this->parametros = array();
		for($a=5;$a<count($argv);$a++)
		{
			if(preg_match("/^-/",$argv[$a]))//Es un modificador
			{
				$pila_modificadores[$a] = $argv[$a];
				$this->parametros[$pila_modificadores[$a]] = "VACIO";
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
		parent::registrar( $this->item[0] );
		if($this->registrar_db){
			$sql = "INSERT INTO apex_solicitud_consola (solicitud_consola, usuario, llamada)
					VALUES ('$this->id','".$this->usuario."','$str_llamada');";
			if ($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD CONSOLA: No se pudo registrar la solicitud: " .$db["instancia"][apex_db_con]->ErrorMsg());
			}
		}
	}
}
?>