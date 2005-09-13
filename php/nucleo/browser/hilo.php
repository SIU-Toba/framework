<?
//Tamaño del HILO (Unidades de memoria independientes por solicitud)
//Esto determina la cantidad de "BACK" que se puede hacer el browser sin
//Perder el estado de sesion de cada request.
define("apex_hilo_tamano","3");

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
	var $no_reciclar = false;	//Inhabilita el reciclado de la sesion
	private $celda_memoria_actual = "central";
	
	function hilo()
/*
 	@@acceso: constructor
	@@desc: Inicializa el HILO
*/	{
		//dump_session();
		$this->id = uniqid("");
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
		//Averiguo cual es la CELDA de memoria activa para este REQUEST
		if(isset($this->parametros[apex_hilo_qs_celda_memoria])){
			$this->celda_memoria_actual = $this->parametros[apex_hilo_qs_celda_memoria];
			unset($this->parametros[apex_hilo_qs_celda_memoria]);
		}
		$this->inicializar_memoria();
 	}

	function destruir()
	//Destruyo el HILO
	{
		if(!$this->no_reciclar){
			$this->ejecutar_reciclaje_datos_globales();	
		}
	}

	function inicializar_memoria()
	//Inicializa la memoria
	{
		$celda = $this->get_celda_memoria_actual();
		//Ejecuto la recoleccion de basura de la MEMORIA SINCRONIZADA
		if(isset($_SESSION[$celda]["hilo"])){
			if(count($_SESSION[$celda]["hilo"]) > apex_hilo_tamano ){
				array_shift($_SESSION[$celda]["hilo"]);
			}
		}
		//Vacio los reciclables activos para que se registren ellos.
		$_SESSION[$celda]["reciclables_activos"] = array();
		if(!isset($_SESSION[$celda]["reciclables"])){
			$_SESSION[$celda]["reciclables"] = array();
		}
	}

	function get_celda_memoria_actual()
	//Indica cual es la celda de memoria que se utiliza en este REQUEST
	{
		return $this->celda_memoria_actual;
	}

	function desactivar_reciclado()
	{
		$this->no_reciclar = true;
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
		return $_SESSION["proyecto"]["nombre"];
	}

	function obtener_proyecto_descripcion()
/*
 	@@acceso: actividad
	@@desc: DEvuelve la descripcion del proyecto
*/
	{
		return $_SESSION["proyecto"]["descripcion"];
	}

	function obtener_path()
/*
 	@@acceso: actividad
	@@desc: Devuelve el PATH del toba
*/
	{
		return $_SESSION["path"];
	}

	function obtener_proyecto_path()
/*
 	@@acceso: actividad
	@@desc: Devuelve el PATH del PROYECTO
*/
	{
		if($_SESSION["proyecto"]["nombre"]=="toba"){
			return $_SESSION["path"];
		}else{
			return $_SESSION["path_proyecto"];
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
		if($_SESSION["proyecto"]["nombre"]=="toba"){
			$path_real = $_SESSION["path"];
		}else{
			$path_real = $_SESSION["path_proyecto"];
		}
		$path_real = $path_real . "/www/" . $archivo;
		$path_browser = recurso::preambulo() . "/" .
						$_SESSION["proyecto"]["nombre"] . "/" . $archivo;
		return array(	"real" => $path_real,
						"browser" => $path_browser);
	}
	
	function obtener_usuario()
/*
 	@@acceso: actividad
	@@desc: Devuelve el identificador del USUARIO logueado
*/
	{
		return $_SESSION["usuario"]["id"];
	}
    
	function obtener_usuario_nivel_acceso()
/*
 	@@acceso: actividad
	@@desc: Notifica el nivel de acceso que posee el usuario
*/
	{
		return $_SESSION["usuario"]["nivel_acceso"];
	}

	function obtener_usuario_grupo_acceso()
/*
 	@@acceso: actividad
	@@desc: Notifica el GRUPO de ACCESO del usuario
*/
	{
		return $_SESSION["usuario"]["grupo_acceso"];
	}

	function obtener_usuario_perfil_datos()
/*
 	@@acceso: actividad
	@@desc: Notifica el GRUPO de ACCESO del usuario
*/
	{
		return $_SESSION["usuario"]["perfil_datos"];
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

	function verificar_acceso_menu()
	//Indica si el request se genero desde el menu
	{
		if(isset($_GET[apex_hilo_qs_menu])){
			return true;
		}else{
			return false;
		}
	}

	//----------------------------------------------------------------	
	//----------------------------------------------------------------
	//-------------------- MEMORIA (persistencia) --------------------
	//----------------------------------------------------------------	
	//----------------------------------------------------------------	

	//************************************************************************
	//********  Persistencia EXCLUSIVA para la PROXIMA instanciacion  ********
	//************************************************************************
		
	function persistir_dato($indice, $datos)
/*
 	@@acceso: actividad
	@@desc: Graba un dato en la MEMORIA. El dato solo estara disponible en la solicitud PROXIMA
*/
	{
		$celda = $this->get_celda_memoria_actual();
		$_SESSION[$celda]["hilo"][$this->id][$indice]=$datos;
	}
	//----------------------------------------------------------------	

	function eliminar_dato($indice)
/*
 	@@acceso: actividad
	@@desc: Graba un dato en la MEMORIA. El dato solo estara disponible en la solicitud PROXIMA
*/
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["hilo"][$this->id][$indice])){
			unset($_SESSION[$celda]["hilo"][$this->id][$indice]);
		}
	}
	//----------------------------------------------------------------		

	function recuperar_dato($indice)
/*
 	@@acceso: actividad
	@@desc: Recupera un dato de la MEMORIA solo cuando el dato fue grabado en la solicitud ANTERIOR
*/
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["hilo"][$this->hilo_referencia][$indice])){
			return $_SESSION[$celda]["hilo"][$this->hilo_referencia][$indice];
		}else{
			return null;
		}
	}
	//----------------------------------------------------------------	

	function dump_memoria()
/*
 	@@acceso: actividad
	@@desc: Muestra el contenido de la MEMORIA
*/
	{
		$celda = $this->get_celda_memoria_actual();
		//Invierto el orden (ultimo primero)
		$temp = array_reverse($_SESSION[$celda]["hilo"], true);
		ei_arbol($temp,"MEMORIA");
	}
	//----------------------------------------------------------------	

	function limpiar_memoria()
/*
 	@@acceso: actividad
	@@desc: Limpia la MEMORIA
*/
	{
		$celda = $this->get_celda_memoria_actual();
		unset($_SESSION[$celda]["hilo"]);
	}

	//************************************************************************
	//*********  Persistencia GLOBAL (acceso directo a la sesion)  ***********
	//************************************************************************

	function existe_dato_global($indice)
/*
 	@@acceso: actividad
	@@desc: Graba un dato en la SESSION.
*/
	{
		$celda = $this->get_celda_memoria_actual();
		return isset($_SESSION[$celda]["global"][$indice]);
	}
	//----------------------------------------------------------------	

	function persistir_dato_global($indice, $datos, $reciclable=false)
/*
 	@@acceso: actividad
	@@desc: Graba un dato en la SESSION.
*/
	{
		$celda = $this->get_celda_memoria_actual();
		$_SESSION[$celda]["global"][$indice]=$datos;
		if($reciclable){
			$this->dato_global_reciclable($indice);
		}
	}
	//----------------------------------------------------------------	

	function recuperar_dato_global($indice)
/*
 	@@acceso: actividad
	@@desc: Recupera un dato de la SESSION
*/
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["global"][$indice]))
		{
			//Si accedo a un dato reciclable, lo marco como activo
			if($this->existe_dato_reciclable($indice)){
				$this->dato_global_activo($indice);
			}
			return $_SESSION[$celda]["global"][$indice];
		}else{
			return null;
		}
	}
	//----------------------------------------------------------------	
	
	function eliminar_dato_global($indice)
/*
 	@@acceso: actividad
	@@desc: Recupera un dato de la SESSION
*/
	{
		$celda = $this->get_celda_memoria_actual();
		if(isset($_SESSION[$celda]["global"][$indice])){
			unset($_SESSION[$celda]["global"][$indice]);
		}
		//Si el dato era reciclable, lo saco de las listas de reciclado
		if($this->existe_dato_reciclable($indice)){
			foreach(array_keys($_SESSION[$celda]["reciclables"]) as $reciclable){
				if($_SESSION[$celda]["reciclables"][$reciclable]==$indice){
					unset($_SESSION[$celda]["reciclables"][$reciclable]);
				}
			}
			foreach(array_keys($_SESSION[$celda]["reciclables_activos"]) as $reciclable){
				if($_SESSION[$celda]["reciclables_activos"][$reciclable]==$indice){
					unset($_SESSION[$celda]["reciclables_activos"][$reciclable]);
				}
			}
		}
	}
	//----------------------------------------------------------------	

	function limpiar_memoria_global()
	{
		$celda = $this->get_celda_memoria_actual();
		unset($_SESSION[$celda]["global"]);
	}

	//----------------------------------------------------------------	
	//-------------  RECICLAJE de memoria GLOBAL ---------------------	
	//----------------------------------------------------------------	
	
	function existe_dato_reciclable($indice)
	{
		$celda = $this->get_celda_memoria_actual();
		if(in_array($indice,$_SESSION[$celda]["reciclables"])){
			return true;
		}else{
			return false;
		}
	}
	//----------------------------------------------------------------	

	function dato_global_reciclable($indice)
	//Se reporta que un dato global se va a reciclar
	{
		$celda = $this->get_celda_memoria_actual();
		if( !$this->existe_dato_reciclable($indice) ){
			$_SESSION[$celda]["reciclables"][] = $indice;
			$this->dato_global_activo($indice);
		}
	}
	//----------------------------------------------------------------	
	
	function dato_global_activo($indice)
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

	//----------------------------------------------------------------	

	function ejecutar_reciclaje_datos_globales()
	{
		$celda = $this->get_celda_memoria_actual();
		//dump_session();
		//echo "Finalizando el HILO<br>";
		foreach(array_keys($_SESSION[$celda]["reciclables"]) as $reciclable){
			$dato = $_SESSION[$celda]["reciclables"][$reciclable];
			//Si hay un elemento reciclable que no se activo, lo destruyo
			if(!in_array($dato,$_SESSION[$celda]["reciclables_activos"])){
				//echo "elimino: $dato<br>";
				unset($_SESSION[$celda]["reciclables"][$reciclable]);
				$this->eliminar_dato_global($dato);
			}
		}
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
		if(!in_array("archivos", $_SESSION) || !in_array($archivo, $_SESSION["archivos"])){
			$_SESSION["archivos"][] = $archivo;
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