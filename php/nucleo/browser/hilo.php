<?
//Tamaño de la pila de la memoria sincronizada
define("apex_hilo_tamano","5");
//Tipos de reciclado de la memoria global
define('apex_hilo_reciclado_item',0);
define('apex_hilo_reciclado_acceso',1);

//----------------------------------------------------------------
//-------------------- QUERYSTRING Basico ------------------------
//----------------------------------------------------------------
/*
	ATENCION, estas claves compiten con posibles claves de los proyectos consumidores
*/
//Separador en el caso de que una clave transporte varios valores
define("apex_qs_separador","||");			//Separador utilizado para diferenciar campos de valores compuestos
// Claves de querystring utilizadas por la pareja VINCULADOR - HILO
define("apex_hilo_qs_id","ah");									//ID de Hilo referenciado
define("apex_hilo_qs_item","ai");								//ITEM de catalogo a solicitar
define("apex_hilo_qs_parametros","ap");							//zona en la que se va a cargar al ITEM
//-- Internos
define("apex_hilo_qs_canal_obj","toba-can");					//Prefijo de los CANALES de OBJETOS (Para comunicarse con ellos por GET)
define("apex_hilo_qs_zona","toba-zona");						//CANAL de propagacion de ZONAS
define("apex_hilo_qs_cronometro","toba-cron");					//CANAL gatillo del cronometro
define("apex_hilo_qs_menu","toma-menu");						//Indica que el vinculo proviene del MENU
define("apex_hilo_qs_celda_memoria","toba-celda-memoria");		//Indicador que indica que el vinculo proviene del MENU
//-- WDDX

//*********  FRAMES entorno EDICION ************
//-- FRAME control
define("apex_frame_control","frame_control");
//-- FRAME lista
define("apex_frame_lista","frame_lista");
//-- FRAME central
define("apex_frame_centro","frame_centro");
//-- FRAME comunicaciones
define("apex_frame_com","frame_com");

class hilo
/* Todas las preguntas sobre el ESTADO de la aplicacion deberian caer en esta clase:
* 	- El GET a travez del array de PARAMETROS
* 	- La sesion a travez de la MEMORIA
*/
{
	var $id;
	var $url_actual;
	var $item_solicitado;
	var $hilo_referencia;
	var $parametros;
	var $reciclar_memoria = true;	//Habilita el reciclado de la memoria en la sesion
	private $celda_memoria_actual = "central";
	private $acceso_menu;
	
	function hilo()
/*
 	@@acceso: constructor
	@@desc: Inicializa el HILO
*/	{
		//dump_session();
		$this->id = uniqid('');
		$this->url_actual = $_SERVER["PHP_SELF"];
        //-[1]- Busco el ID de referencia de la instanciacion anterior del HILO
		//		Este ID me permite ubicar la memoria correcta para el request ACTUAL
		if(isset($_GET[apex_hilo_qs_id])){
			$this->hilo_referencia=$_GET[apex_hilo_qs_id];
		}else{
            //Atencion, no hay hilo de referencia. CONTROLAR!!
            //Esto tiene sentido solo para la pagina de logon (?) para el resto 
            //del sistema implica que las cosas funcionen mal!
        }
		//-[2]- Que ITEM se solicito?
		if(isset($_GET[apex_hilo_qs_item])){
			$item = explode(apex_qs_separador,$_GET[apex_hilo_qs_item]);
            if(count($item)==0){
				$this->item_solicitado = null;
			}elseif(count($item)==2){
				//Dos parametros es OK!
				$this->item_solicitado = $item;
			}else{
				//Errores de formateo llevan al ITEM vacio
				$this->item_solicitado = array("toba","/basicos/vacio");
			}
		}else{
            $this->item_solicitado = null;//No hay parametro
        }
		//-[3]- Recupero los parametros
		$this->parametros = array();
		if(apex_pa_encriptar_qs){
			if(isset($_GET[apex_hilo_qs_parametros])){
				$encriptador = toba::get_encriptador();
				parse_str($encriptador->descifrar($_GET[apex_hilo_qs_parametros]),$this->parametros);
				unset($this->parametros["jmb76"]);//Clave agregada para complicar la encriptacion
			}
		}else{
			$this->parametros = $_GET;
			unset($this->parametros[apex_hilo_qs_id]);
			unset($this->parametros[apex_hilo_qs_item]);
			//FALTA hacer un URL decode!!!
		}
		//MEMORIA: Averiguo cual es la CELDA de memoria activa para este REQUEST
		if(isset($this->parametros[apex_hilo_qs_celda_memoria])){
			$this->celda_memoria_actual = $this->parametros[apex_hilo_qs_celda_memoria];
			unset($this->parametros[apex_hilo_qs_celda_memoria]);
			if($this->celda_memoria_actual == 'toba'){
				throw new excepcion_toba_def("No puede utilizarse la palabra 'toba' como nombre de celda");
			}
		}
		//Guardo el FLAG que indica si se accedio por el menu
		if(isset($_GET[apex_hilo_qs_menu])){
			$this->acceso_menu = true;
		}else{
			$this->acceso_menu = false;
		}
		$this->inicializar_memoria();
 	}

	function destruir()
	{
		//Disparo el proceso de reciclaje
		if($this->reciclar_memoria){
			$this->reciclar_datos_globales_acceso();	
			$this->reciclar_datos_sincronizados();
		}
		//Mantengo guardado cual es el item anterior de la celda
		$celda = $this->get_celda_memoria_actual();
		$_SESSION[$celda]['item_anterior'] = $_SESSION[$celda]['item'];
	}

	function info()
/*
 	@@acceso: actividad
	@@desc: Muestra el estado del HILO
*/	{
		$dump["item_solicitado"]=$this->item_solicitado;
		$dump["hilo_referencia"]=$this->hilo_referencia;
		$dump["parametros"]=$this->parametros;
		ei_arbol($dump,"HILO");
	}

	function prefijo_vinculo()
/*
 	@@acceso: interno
	@@desc: Genera la primera porcion de las URLs
	@@retorno: Prefijo de las URLs
*/
	{
		return $this->url_actual . "?" . apex_hilo_qs_id  . "=" . $this->id;
	}

	function obtener_id()
/*
 	@@acceso: interno
	@@desc: Devuelve el ID del hilo
	@@retorno: string | identificador del hilo
*/
	{
		return $this->id;
	}

	//----------------------------------------------------------------	
	//----------------------------------------------------------------
	//-----------------  ACCESO al ESTADO GENERAL  -------------------
	//----------------------------------------------------------------
	//----------------------------------------------------------------	
	
	function obtener_parametro($canal)
/*
 	@@acceso: actividad
	@@desc: Recupera un parametro enviado por el VINCULADOR
	@@param: string | identificador que se utilizo para pasar el parametro
	@@retorno: string/null | Valor pasado como parametro, null en el caso de que no se haya pasado un parametro
*/
	{
		if(isset($this->parametros[$canal])){
			return $this->parametros[$canal];			
		}else{
			return null;
		}
	}

	function obtener_parametros()
/*
 	@@acceso: actividad
	@@desc: Recupera un parametro enviado por el VINCULADOR
	@@retorno: array | Lista completa de parametros
*/
	{
		$temp = $this->parametros;
		unset($temp[apex_hilo_qs_zona]);
		return $temp;			
	}

	function obtener_item_solicitado()
/*
 	@@acceso: nucleo
	@@desc: Notifica el ITEM solicitado
	@@retorno: Item solicitado
*/
	{
		return $this->item_solicitado;
	}

	function obtener_proyecto()
/*
 	@@acceso: actividad
	@@desc: Devuelve el identificador del PROYECTO ACTUAL
*/
	{
		return $_SESSION['toba']["proyecto"]["nombre"];
	}

	function obtener_proyecto_descripcion()
/*
 	@@acceso: actividad
	@@desc: DEvuelve la descripcion del proyecto
*/
	{
		return $_SESSION['toba']["proyecto"]["descripcion"];
	}
	
	/**
	*	Retorna el conjunto de propiedades básicas del proycto actual
	*/
	function obtener_proyecto_datos()
	{
		return $_SESSION['toba']["proyecto"];
	}

	function obtener_path()
/*
 	@@acceso: actividad
	@@desc: Devuelve el PATH del toba
*/
	{
		return $_SESSION['toba']["path"];
	}

	function obtener_proyecto_path()
/*
 	@@acceso: actividad
	@@desc: Devuelve el PATH del PROYECTO
*/
	{
		if($_SESSION['toba']["proyecto"]["nombre"]=="toba"){
			return $_SESSION['toba']["path"];
		}else{
			return $_SESSION['toba']["path_proyecto"];
		}
	}
	
	function obtener_proyecto_path_www($archivo="")
/*
 	@@acceso: actividad
	@@desc: Devuelve el PATH del PROYECTO
	@@param: string | Subcarpeta que desea ubicarse
	@@retorno: array | Path real y relativo al browser
*/
	{
		if($_SESSION['toba']["proyecto"]["nombre"]=="toba"){
			$path_real = $_SESSION['toba']["path"];
		}else{
			$path_real = $_SESSION['toba']["path_proyecto"];
		}
		$path_real = $path_real . "/www/" . $archivo;
		$path_browser = recurso::preambulo() . "/" .
						$_SESSION['toba']["proyecto"]["nombre"] . "/" . $archivo;
		return array(	"real" => $path_real,
						"browser" => $path_browser);
	}
	
	function obtener_usuario()
/*
 	@@acceso: actividad
	@@desc: Devuelve el identificador del USUARIO logueado
*/
	{
		return $_SESSION['toba']["usuario"]["id"];
	}
    
	function obtener_usuario_nivel_acceso()
/*
 	@@acceso: actividad
	@@desc: Notifica el nivel de acceso que posee el usuario
*/
	{
		return $_SESSION['toba']["usuario"]["nivel_acceso"];
	}

	function obtener_usuario_grupo_acceso()
/*
 	@@acceso: actividad
	@@desc: Notifica el GRUPO de ACCESO del usuario
*/
	{
		return $_SESSION['toba']["usuario"]["grupo_acceso"];
	}

	function obtener_usuario_perfil_datos()
/*
 	@@acceso: actividad
	@@desc: Notifica el GRUPO de ACCESO del usuario
*/
	{
		return $_SESSION['toba']["usuario"]["perfil_datos"];
	}
	
	function usuario_solicita_cronometrar()
/*
 	@@acceso: nucleo
	@@desc: Notifica si se solicito registrar la cronometracion del ITEM que se esta ejecutando
*/
	{
		if(isset($this->parametros[apex_hilo_qs_cronometro])){
			return true;
		}else{
			return false;
		}
	}

	function entorno_instanciador()
/*
 	@@acceso: nucleo
	@@desc: Notifica si el ITEM que se esta ejecutando es el INSTANCIADOR de un objeto de la libreria
*/
	{
		global $solicitud;
		if(strpos($solicitud->info["item"],"admin/objetos/instanciadores")){
			return true;
		}else{
			return false;
		}
	}
	
	function obtener_item_inicial()
/*
 	@@acceso: actividad
	@@desc: Devuelve el identificador del PROYECTO ACTUAL
*/
	{
		//explode(apex_qs_separador,apex_pa_item_inicial_contenido);
		//Esto lo tiene que saber $_SESSION en base a las preferencias de usuario
		return array("toba","/trabajo/resumen",array(apex_hilo_qs_zona=>$this->obtener_usuario()));

	}

	//----------------------------------------------------------------	
	//----------------------------------------------------------------
	//------------ MEMORIA (persistencia en $_SESSION) ---------------
	//----------------------------------------------------------------	
	//----------------------------------------------------------------	

	/**
		Inicializa la memoria
	*/
	private function inicializar_memoria()
	{
		if( $this->verificar_acceso_menu() ){
			/*
				El flag de acceso por el menu desencadena el borrado de toda la memoria pensada para
				utilizarse dentro de una operacion: los elementos de la memoria GLOBAL marcados 
				como "reciclables" y la memoria sincronizada (la alienada al request anterior).
			*/
			toba::get_logger()->debug('HILO: REINICIO MEMORIA (Se limpio la memoria sincroniza y global reciclable: acceso menu)');
			$this->limpiar_memoria_sincronizada();
			$this->limpiar_memoria_global_reciclable();
		}
		$this->inicializar_reciclaje_global();
	}

	public function limpiar_memoria()
	{
		$this->limpiar_memoria_sincronizada();
		$this->limpiar_memoria_global();
	}

	public function dump_memoria()
	{
		$celda = $this->get_celda_memoria_actual();
		ei_arbol($_SESSION,"MEMORIA Completa");
	}

	public function dump_celda_memoria()
	{
		$celda = $this->get_celda_memoria_actual();
		ei_arbol($_SESSION[$celda],"CELDA de MEMORIA: $celda");
	}

	/**
		Indica cual es la celda actual
	*/
	public function get_celda_memoria_actual()
	//Indica cual es la celda de memoria que se utiliza en este REQUEST
	{
		return $this->celda_memoria_actual;
	}

	/**
		Indica si se accedio por el menu
	*/
	public function verificar_acceso_menu()
	//Indica si el request se genero desde el menu
	{
		return $this->acceso_menu;
	}

	/**
		Desactiva el reciclado
	*/
	public function desactivar_reciclado()
	{
		$this->reciclar_memoria = false;
	}

	//************************************************************************
	//*********  Persistencia GLOBAL (acceso directo a la sesion)  ***********
	//************************************************************************

	/**
		Persiste un dato global
	*/
	public function persistir_dato_global($indice, $datos, $reciclable=false, $tipo_reciclado=null)
	{
		$celda = $this->get_celda_memoria_actual();
		$_SESSION[$celda]['global'][$indice]=$datos;
		if($reciclable){
			//Defino el tipo de reciclado (por defecto se utiliza el de cambio de item)
			if(!isset($tipo_reciclado)) $tipo_reciclado = apex_hilo_reciclado_item;
			$this->agregar_dato_global_reciclable($indice, $tipo_reciclado);
		}
	}

	/**
		Recupera un dato global
	*/
	public function recuperar_dato_global($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		if($this->existe_dato_global($indice))
		{
			//Se avisa que se accedio a un dato global al sistema de reciclado
			$this->acceso_a_dato_global($indice);
			return $_SESSION[$celda]['global'][$indice];
		}else{
			return null;
		}
	}
	
	/**
		Elimina un dato global
	*/
	public function eliminar_dato_global($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]['global'][$indice])){
			unset($_SESSION[$celda]['global'][$indice]);
		}
		$this->eliminar_informacion_reciclado($indice);
	}

	/**
		Chequea si un dato global existe
	*/
	public function existe_dato_global($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		return isset($_SESSION[$celda]['global'][$indice]);
	}

	/**
		Elimina TODA la informacion global
	*/
	public function limpiar_memoria_global()
	{
		$celda = $this->get_celda_memoria_actual();
		unset($_SESSION[$celda]['global']);
	}

	//----------------------------------------------------------------	
	//-------------  RECICLAJE de memoria GLOBAL ---------------------	
	//----------------------------------------------------------------	
	
	/**
		Inicializa el esquema de reciclado global
	*/
	private function inicializar_reciclaje_global()
	{
		$celda = $this->get_celda_memoria_actual();
		//-- Inicializo reciclaje por cambio de item.
		if(isset($this->item_solicitado)){
			$_SESSION[$celda]['item'] = implode('|',$this->item_solicitado);
		}else{
			$_SESSION[$celda]['item'] = 'inicio';
		}
		//-- Inicializo reciclaje por acceso.
		//		Vacio los reciclables activos para que se registren ellos.
		$_SESSION[$celda]["reciclables_activos"] = array();
		if(!isset($_SESSION[$celda]["reciclables"])){
			$_SESSION[$celda]["reciclables"] = array();
		}
		//Disparo el reciclaje por cambio de item
		$this->reciclar_datos_globales_item();
	}

	/**
		Se marca un dato global como reciclable
	*/
	private function agregar_dato_global_reciclable($indice, $tipo_reciclado)
	//Se reporta que un dato global se va a reciclar
	{
		if($tipo_reciclado != apex_hilo_reciclado_item && $tipo_reciclado != apex_hilo_reciclado_acceso ){
			//El tipo de reciclado es invalido!
			throw new excepcion_toba('El tipo de reciclado solicitado es invalido');
		}
		if( !$this->existe_dato_reciclable($indice) ){
			$celda = $this->get_celda_memoria_actual();
			$_SESSION[$celda]["reciclables"][$indice] = $tipo_reciclado;
		}
		if($tipo_reciclado == apex_hilo_reciclado_acceso){
			//Si el tipo de reciclado es por conteo de accesos, marco al dato como activo
			$this->dato_global_activo($indice);
		}
	}
	
	/**
		Registra el acceso a datos globales para el esquema de reciclado por conteo de accesos
	*/
	private function acceso_a_dato_global($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		//Si el tipo de reciclado es por acceso, marco que el elemento fue accedido
		if( isset($_SESSION[$celda]["reciclables"][$indice]) ){
			if($_SESSION[$celda]["reciclables"][$indice]== apex_hilo_reciclado_acceso){
				$this->dato_global_activo();	
			}
		}
	}

	/**
		Setea un dato reciclable como activo en el esquema de reciclado por conteo de accesos
	*/
	private function dato_global_activo($indice)
	//Indica que el dato reciclable fue activado
	{
		$celda = $this->get_celda_memoria_actual();
		//Puede ser llamado por fuera
		if( $this->existe_dato_reciclable($indice) ){
			if(!in_array($indice,$_SESSION[$celda]["reciclables_activos"])){
				$_SESSION[$celda]["reciclables_activos"][] = $indice;
			}
		}
	}

	/**
		Reciclado de datos globales por cambio de item.
		Se debe ejecutar cuando se inicia el request
			(Si el item de la celda actual cambio, eliminar el contenido)
	*/
	private function reciclar_datos_globales_item()
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]['item_anterior'])){
			if($_SESSION[$celda]['item_anterior'] != $_SESSION[$celda]['item']){
				toba::get_logger()->debug("HILO: Se limpio de la memoria con reciclaje por cambio de ITEM");
				foreach( $_SESSION[$celda]["reciclables"] as $reciclable => $tipo){	
					if($tipo == apex_hilo_reciclado_item){
						$this->eliminar_dato_global($reciclable);
					}
				}
			}
		}
	}

	/**
		Reciclado por control de acceso a los datos guardados
		Se debe ejecutar cuando termina el request
			(Si un dato no fue accedido, borrarlo)
	*/
	private function reciclar_datos_globales_acceso()
	{
		$celda = $this->get_celda_memoria_actual();
		foreach( $_SESSION[$celda]["reciclables"] as $reciclable => $tipo){	
			if($tipo == apex_hilo_reciclado_acceso){
				//Si hay un elemento reciclable que no se activo, lo destruyo
				if(!in_array($reciclable,$_SESSION[$celda]["reciclables_activos"])){
					toba::get_logger()->debug("HILO: Se limpio de la memoria el elemento '$reciclable' porque no fue accedido");
					$this->eliminar_dato_global($reciclable);
				}
			}
		}
	}

	/**
		Controla si existe un dato reciclabe
	*/
	public function existe_dato_reciclable($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		return (isset($_SESSION[$celda]["reciclables"][$indice]));
	}

	/**
		Limpia toda la memoria reciclable
	*/
	private function limpiar_memoria_global_reciclable()
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["reciclables"])){
			foreach($_SESSION[$celda]["reciclables"] as $reciclable => $tipo){
				$this->eliminar_dato_global($reciclable);
			}
		}
		//Esto no deberia ser necesario.
		unset($_SESSION[$celda]["reciclables"]);
		unset($_SESSION[$celda]["reciclables_activos"]);
	}

	/**
		Elimina la informacion asociada al reciclado de un dato
	*/
	private function eliminar_informacion_reciclado($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		//Si el dato era reciclable, lo saco de las listas de reciclado
		if($this->existe_dato_reciclable($indice)){
			$tipo = $_SESSION[$celda]["reciclables"][$indice];
			//Si el reciclado es de tipo 'acceso', tengo que sacarlo de la lista de reciclables activos
			if($tipo == apex_hilo_reciclado_acceso){
				foreach(array_keys($_SESSION[$celda]["reciclables_activos"]) as $reciclable){
					if($_SESSION[$celda]["reciclables_activos"][$reciclable]==$indice){
						unset($_SESSION[$celda]["reciclables_activos"][$reciclable]);
					}
				}
			}
			unset($_SESSION[$celda]["reciclables"][$indice]);
		}
	}

	//*******************************************************************************
	//********  Persistencia SINCRONIZADA (Exclusiva para el PROXIMO request ********
	//*******************************************************************************
		
	public function persistir_dato_sincronizado($indice, $datos)
	{
		$celda = $this->get_celda_memoria_actual();
		$_SESSION[$celda]["hilo"][$this->id][$indice]=$datos;
	}

	public function recuperar_dato_sincronizado($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["hilo"][$this->hilo_referencia][$indice])){
			return $_SESSION[$celda]["hilo"][$this->hilo_referencia][$indice];
		}else{
			return null;
		}
	}

	public function eliminar_dato_sincronizado($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["hilo"][$this->id][$indice])){
			unset($_SESSION[$celda]["hilo"][$this->id][$indice]);
		}
	}

	public function limpiar_memoria_sincronizada()
	{
		$celda = $this->get_celda_memoria_actual();
		unset($_SESSION[$celda]["hilo"]);
	}

	private function reciclar_datos_sincronizados()
	//Ejecuto la recoleccion de basura de la MEMORIA SINCRONIZADA
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["hilo"])){
			if(count($_SESSION[$celda]["hilo"]) > apex_hilo_tamano ){
				array_shift($_SESSION[$celda]["hilo"]);
			}
		}
	}

	//*******************************************************************************
	//** Compatibilidad inversa con la version anterior
	//*******************************************************************************

	public function persistir_dato($indice, $datos)
	{
		toba::get_logger()->obsoleto("", __FUNCTION__, '0.8.3');
		$this->persistir_dato_sincronizado($indice, $datos);
	}

	public function eliminar_dato($indice)
	{
		toba::get_logger()->obsoleto("", __FUNCTION__, '0.8.3');
		$this->eliminar_dato_sincronizado($indice);
	}

	public function recuperar_dato($indice)
	{
		toba::get_logger()->obsoleto("", __FUNCTION__, '0.8.3');
		return $this->recuperar_dato_sincronizado($indice);
	}

	//----------------------------------------------------------------	
	//----------------------------------------------------------------
	//---------------- MANEJO de ARCHIVOS de SESION ------------------
	//----------------------------------------------------------------	
	//----------------------------------------------------------------	
/*
	Estas funciones pertinen manejar el ciclo de vida de archivos
	cargados con UPLOADS que son solo validos durante la sesion.
	La eliminacion de estos archivos esta en el evento cerrar de la sesion
*/
	function registrar_archivo($archivo){
		if(	!isset($_SESSION['toba']['archivos']) 
			|| !in_array($archivo, $_SESSION['toba']['archivos'])){
			$_SESSION['toba']["archivos"][] = $archivo;
		}
	}
	
	//----------------------------------------------------------------	
	//----------------------------------------------------------------
	//-------------------- EVENTOS de SESION TOBA --------------------
	//----------------------------------------------------------------
	//----------------------------------------------------------------	
/*
* Las funciones que siguen generan dos links ESPECIALES que se atrapan antes que cualquier 
* proxima ACTIVIDAD (Antes de que exista la proxima solicitud). 
* Estos links implican EVENTOS de SESION. Estan metidos en el hilo porque
* Hacen referencia al estado actual (especificamente a la eliminacion del estado actual)
* Hasta ahora estas funciones solo se llaman desde el CONTROL.
*/

	function finalizar()
/*
 	@@acceso: nucleo
	@@desc: Crea el vinculo que hay que llamar para eliminar la SESION...
*/
	{
		return $this->prefijo_vinculo() ."&". apex_sesion_qs_finalizar . "=1";
	}	

	function cambiar_proyecto()
/*
 	@@acceso: nucleo
	@@desc: Crea el vinculo que hay que llamar para cambiar de proyecto.
*/
	//
	//Es importante que esto este en un FORM que posea un campo
	// apex_sesion_post_proyecto, que es la clave que espera la sesion para saber
	// cual es el proximo proyecto a cargar
	{
		return $this->prefijo_vinculo() ."&". apex_sesion_qs_cambio_proyecto . "=1";
	}	
	//----------------------------------------------------------------
}
?>
