<?php
require_once("solicitud.php");
//############################################################################################
//############################################################################################
//##############   Solicitudes ESPECIALIZADAS a partir del MEDIO DE ACCESO   #################
//############################################################################################
//############################################################################################
/*
* pueden registrar diferentes contenidos para las GLOBALES
* 
*/
class solicitud_browser extends solicitud
{
	var $vinculador;	//Objeto encargado de generar nuevas solicitudes
	var $zona;			//Objeto que representa una zona que vincula varios items
	var $zona_cargada;
	var $cola_mensajes;
	
	function solicitud_browser()
	{
		global $cronometro;
		$cronometro->marcar('basura',apex_nivel_nucleo);
		//$cronometro->marcar('SOLICITUD BROWSER: Listo para cargar el ITEM',"nucleo");
		$this->hilo =& new hilo();
		$item = $this->hilo->obtener_item_solicitado();
		//Por defecto lo mando al.
		//ATENCION: esto esjecuta un LOOP recursivo cuando un la pagina inicial es un FRAMSET
		//que tiene una direccion mal!
		if (!isset($item)){//-- No se solicito NINGUN ITEM, determino el item por DEFECTO
            $item = explode(apex_qs_separador,apex_pa_item_inicial);
        }
		parent::solicitud($item,$this->hilo->obtener_usuario());
		//El elemento de item tiene que ser de tipo browser!
		if(apex_solicitud_tipo!=$this->info['item_solic_tipo']) {
			monitor::evento("falta","SOLICITUD BROWSER: El ITEM de item no es de tipo: BROWSER.");
		}
		//Creo la ZONA
		if(trim($this->info['item_zona'])!=""){
			//Hay una zona, tengo que crearla...
			require_once($this->info['item_zona_archivo']);
			//Creo la clase
			$sentencia_creacion = "\$this->zona =& new {$this->info['item_zona']}('{$this->info['item_zona']}','{$this->info['item_zona_proyecto']}',\$this);";
			//echo($sentencia_creacion);
			eval($sentencia_creacion);//Creo la ZONA
		}
        //Creo el vinculador
		$this->vinculador = new vinculador($this);
		//Creo la cola de mensajes
		$this->cola_mensajes = new cola_mensajes($this);
		//Le pregunto al HILO si se solicito cronometrar la PAGINA
		if($this->hilo->usuario_solicita_cronometrar()){
			$this->registrar_db = true;
			$this->cronometrar = true;		
		}
		$cronometro->marcar('SOLICITUD BROWSER: Inicializacion (ZONA, VINCULADOR)',"nucleo");
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	{
		global $db,$ADODB_FETCH_MODE,$cronometro;
    	$cronometro->marcar('basura',apex_nivel_nucleo);
		$this->en_tramite=true;
		//--- HTML automatico ---
		if(trim($this->info["item_include_arriba"]!= "")){
			include_once($this->info["item_include_arriba"]);
        	$cronometro->marcar('SOLICITUD BROWSER: Pagina TIPO (cabecera) ',apex_nivel_nucleo);
		}
		parent::procesar();
		//--- HTML automatico ---
		if(trim($this->info["item_include_abajo"]!= "")){
			include_once($this->info["item_include_abajo"]);
        	$cronometro->marcar('SOLICITUD BROWSER: Pagina TIPO (pie) ',apex_nivel_nucleo);
		}
	}

//--------------------------------------------------------------------------------------------

	function registrar( )
	{
		global $db;
		parent::registrar( $this->hilo->obtener_proyecto() );
		if($this->registrar_db){
			$sql = "INSERT INTO apex_solicitud_browser (solicitud_browser, sesion_browser, ip)
					VALUES ('$this->id','".$_SESSION["id"]."','".$_SERVER["REMOTE_ADDR"]."');";
			if ($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD BROWSER: No se pudo registrar la solicitud: " .$db["instancia"][apex_db_con]->ErrorMsg());
			}
		}
 	}
}
//############################################################################################
//############################################################################################

class solicitud_consola extends solicitud
{
	var $debug;	//Modo debug activado
	var $estado_proceso;

	function solicitud_consola($item, $usuario)
	{
		parent::solicitud($item, $usuario);
		$this->estado_proceso = 0;
	}
//--------------------------------------------------------------------------------------------

	function controlar_permisos()
	{
		return array(true, "No se chequean permisos en el acceso por consola");			
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	//Atrapo las llamadas a la ayuda... sino proceso
	{
		parent::procesar();
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

	function registrar($llamada)
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
//############################################################################################
//############################################################################################

class solicitud_wddx extends solicitud
{
	var $msg_momento;
	var $msg_instancia;
	var $msg_usuario;
	var $datos_recibidos;

	function solicitud_wddx()
	{
		if(isset($_GET[apex_hilo_qs_item])){
			$item = explode(apex_qs_separador,$_GET[apex_hilo_qs_item]);
		}else{
            $item = explode(apex_qs_separador,apex_pa_item_inicial);		
		}
		//Esto esta bien?
		//Atencion, no se muestran los errores del monitor!!
		$usuario = apex_pa_usuario_anonimo;
		parent::solicitud($item,$usuario);
		$this->registrar_db = true;
	}
//--------------------------------------------------------------------------------------------

	function procesar()
	{
		//Recupero el mensaje enviado
		$this->recibir_paquete();
		//Paso el control al ITEM solicitado
		parent::procesar();
 	}
//--------------------------------------------------------------------------------------------
	
	function recibir_paquete()
	{
		if(isset($_POST[apex_wddx_paquete])){
			$this->datos_recibidos = comunicador::desempaquetar( $_POST[apex_wddx_paquete] );
			//Proceso Campos TOBA
			//Instancia que emitio el paquete
			if(isset($this->datos_recibidos[apex_wddx_instancia])){
				$this->msg_instancia = $this->datos_recibidos[apex_wddx_instancia];
				unset($this->datos_recibidos[apex_wddx_instancia]);
			}else{
				$this->msg_instancia = "X";
			}
			//usuario de la instancia que envio el paquete
			if(isset($this->datos_recibidos[apex_wddx_usuario])){
				$this->msg_usuario = $this->datos_recibidos[apex_wddx_usuario];
				unset($this->datos_recibidos[apex_wddx_usuario]);
			}else{
				$this->msg_usuario = "X";
			}
			//Momento en que se creo el paquete (en el emisor)
			if(isset($this->datos_recibidos[apex_wddx_momento])){
				$this->msg_momento = $this->datos_recibidos[apex_wddx_momento];
				unset($this->datos_recibidos[apex_wddx_momento]);
			}else{
				$this->msg_momento = "";
			}
		}
	}
//--------------------------------------------------------------------------------------------

	function registrar()
	{
		global $db;
		parent::registrar( apex_pa_proyecto );
		
		//ATENCION!!
		return;
		
		if($this->registrar_db){
			$cliente = $_SERVER["REMOTE_ADDR"];
			$sql = "INSERT INTO apex_solicitud_wddx 
					(solicitud_wddx, usuario, ip, instancia, instancia_usuario, paquete)
					VALUES ('$this->id','".apex_pa_usuario_anonimo."','".$cliente."','".$this->msg_instancia .
							"','".$this->msg_usuario."','".serialize($this->datos_recibidos)."');";
			if ($db["instancia"][apex_db_con]->Execute($sql) === false){
				monitor::evento("bug","SOLICITUD WDDX: No se pudo registrar la solicitud: " .
				$db["instancia"][apex_db_con]->ErrorMsg());
			}
		}
	}
}

//############################################################################################
//############################################################################################
// Sin terminar!!!!!!!!!!
class solicitud_soap extends solicitud
{

	function solicitud_soap()
	{
		if(isset($_GET[apex_hilo_qs_item])){
			$item = explode(apex_qs_separador,$_GET[apex_hilo_qs_item]);
		}else{
            		$item = explode(apex_qs_separador,apex_pa_item_inicial);		
		}
		//print_r($item);
		//Esto esta bien?
		//Atencion, no se muestran los errores del monitor!!
		$usuario = apex_pa_usuario_anonimo;
		parent::solicitud($item, $usuario);
		$this->registrar_db = false;
	}

//--------------------------------------------------------------------------------------------
	function procesar()
	{
		//Paso el control al ITEM solicitado
		parent::procesar();
 	}
}

//############################################################################################
//############################################################################################

?>
