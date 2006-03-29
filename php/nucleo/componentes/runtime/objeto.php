<?php
/**
 * Padre de todas las clases que definen objetos standart de la aplicacion
 * @package Objetos
 */
class objeto
{
	protected $solicitud;
	protected $id;
	protected $info;
	protected $info_dependencias;						//Definicion de las dependencias
	protected $indice_dependencias;					//Indice que mapea las definiciones de las dependencias con su
	protected $dependencias_indice_actual = 0;	
	protected $lista_dependencias;					//Lista de dependencias disponibles
	protected $dependencias = array();							//Array de sub-OBJETOS
	protected $memoria;
	protected $memoria_existencia_previa = false;
	protected $interface_existencia_previa = false;
	protected $observaciones;
	protected $canal;										// Canal por el que recibe datos 
	protected $canal_recibido;							// Datos recibidos por el canal
	protected $info_proceso = null;					// Estado interno relacionado con el procesamiento llevado a cabo por el objeto
	protected $info_proceso_gravedad = null;		// Array donde se apilan los niveles de gravedad, pada definir que tipo de mensaje se muestra
	protected $info_proceso_indice = 0;
	protected $estado_proceso;							// interno | string | "OK","ERROR","INFRACCION"
	protected $id_ses_g;									//ID global para la sesion
	protected $definicion_partes;						//indica el nombre de los arrays de metadatos que posee el objeto
	protected $exportacion_archivo;
	protected $exportacion_path;
	
	function objeto( $definicion )
	{
		// Compatibilidad hacia atras en el ID
		$this->id[0] = $definicion['info']['proyecto'];
		$this->id[1] = $definicion['info']['objeto'];
		//Cargo las variables internas que forman la definicion
		foreach (array_keys($definicion) as $parte) {
			$this->definicion_partes[] = $parte;
			$this->$parte = $definicion[$parte];
		}
		$this->solicitud = toba::get_solicitud();
		$this->log = toba::get_logger();
		//Recibi datos por el CANAL?
		$this->canal = apex_hilo_qs_canal_obj . $this->id[1];
		$this->canal_recibidos = toba::get_hilo()->obtener_parametro($this->canal);
		$this->id_ses_g = "obj_" . $this->id[1];
		$this->id_ses_grec = "obj_" . $this->id[1] . "_rec";
		//Manejo transparente de memoria
		$this->cargar_memoria();			//RECUPERO Memoria sincronizada
		//$this->recuperar_estado_sesion();	//RECUPERO Memoria dessincronizada
		$this->evaluar_existencia_interface_anterior();
		$this->conectar_fuente();
		$this->log->debug("CONSTRUCCION: {$this->info['clase']}({$this->id[1]}): {$this->get_nombre()}.", 'toba');
		$this->configuracion();
	}

	function configuracion()
	{
	}

	function destruir()
	{
		//echo "Me estoy destruyendo " . $this->id[1] . "<br>";
		//Persisto informacion
		$this->memorizar();						//GUARDO Memoria sincronizada
		//$this->guardar_estado_sesion();		//GUARDO Memoria dessincronizada
		//Llamo a los destructores de los OBJETOS anidados
		foreach(array_keys($this->dependencias) as $dependencia){
			$this->dependencias[$dependencia]->destruir();
		}
	}

	function get_clave_memoria_global()
	{
		return $this->id_ses_grec;
	}

	function info_estado()
/*
 	@@acceso: actividad
	@@desc: Da informacion sobre el ESTADO del objeto
*/
	{
		//Reemplazar por un iterador de las propiedades
		return get_object_vars($this);
	}
//--------------------------------------------------------------------------------------------

	function info_definicion()
/*
 	@@acceso: actividad
	@@desc: Da informacion sobre la DEFINICION objeto
*/
	{
		foreach($this->definicion_partes as $parte){
			$definicion[$parte] = $this->$parte;
		}
		return $definicion;
	}
//--------------------------------------------------------------------------------------------

	function exportar_definicion_php()
/*
 	@@acceso:
	@@desc: 
*/
	{
		//Atencion, por ahora solo para el proyecto TOBA
		$archivo = fopen($this->exportacion_path,"w"); 
		fwrite($archivo, "<?\n//Generacion: " .date("j-m-Y H:i:s") ."\n" );
		fwrite($archivo, "\$definicion_objeto = unserialize(stripslashes(\"".
								addslashes(serialize( $this->info_definicion() ))."\"));\n");
		fwrite($archivo, "\n?>\n" );
		fclose($archivo);
	}
	//-------------------------------------------------------------------------------

	function exportar_definicion_sql()
/*
	Esto es viejo, no funciona segun el modelo actual
 	@@acceso:
	@@desc: 
*/
	{
		$sql = db_dump_tabla("apex_objeto","WHERE objeto = '".$this->id."'");
		foreach(explode("\n",$this->info["clase_dump"]) as $plan){
			$plan_array = explode(":",$plan);
			$tabla = trim($plan_array[0]);
			$where = ereg_replace("##",$this->id,trim($plan_array[1]));
			//echo "<TABLA> $tabla <WHERE> $where <br>";
			$sql .= db_dump_tabla($tabla,$where);
		}
		return $sql;
	}
	//-------------------------------------------------------------------------------

	function info()
/*
	@@acceso: actividad
	@@desc: Imprime la informacion COMPLETA en la PANTALLA
*/
	{
		ei_arbol($this->info_estado());
		//ei_arbol($this->info_definicion());
	}	

	function get_txt()
	{
		return "objeto(".$this->id[1]."): ";	
	}

	function get_nombre()
	{
		return $this->info['nombre'];
	}

	function get_titulo()
	{
		return $this->info['titulo'];
	}

//*******************************************************************************************
//****************************************<  SOPORTE   >*************************************
//*******************************************************************************************	

	function consulta_datos_recibidos()
/*
 	@@acceso: objeto
	@@desc: Responde si el OBJETO recibio datos por su CANAL
*/
	{
		if(isset($this->canal_recibidos)){
			return true;
		}else{
			return false;
		}
	}
//--------------------------------------------------------------------

	function conectar_fuente()
/*
 	@@acceso:
	@@desc: Crea la conexion que el objeto necesita para trabajar
*/
	{
		dba::get_db($this->info["fuente"]);
	}
	//-------------------------------------------------------------------------------

	function existe_ayuda()
	{
		return (trim($this->info['objeto_existe_ayuda'])!="");
	}
	//-------------------------------------------------------------------------------

	function autovinculacion($parametro, $texto="Autovinculo")
/*
 	@@acceso: objeto
	@@desc: Genera un vinculo al mismo objeto
*/
	{
		$html = "<a href='". toba::get_vinculador()->generar_solicitud(null,null,$parametro,true) ."'>";
		$html .= $texto;
		$html .="</a>";
		return $html;
	}

//*******************************************************************************************
//**********************<  Comunicacion de informacion al USUARIO   >************************
//*******************************************************************************************	
/*
	Falta pensar el tema de las transacciones necesitan una reafirmacion despues
	de mostrar la cola de mensajes
*/
	function obtener_mensaje($indice, $parametros=null)
	//Obtiene un mensaje del repositorio de mensajes
	{
		//Busco el mensaje del OBJETO
		if($mensaje = mensaje::get_objeto($this->id[1], $indice, $parametros)){
			return $mensaje;	
		}else{
			//El objeto no tiene un mensaje con el indice solicitado,
			//Busco el INDICE global
			return mensaje::get($indice, $parametros);
		}
	}

	function informar_msg($mensaje, $nivel=null)
	//Guarda un  mensaje en la cola de mensajes
	{
		toba::get_cola_mensajes()->agregar($mensaje,$nivel);	
	}
	
	function informar($indice, $parametros=null,$nivel=null)
	//Obtiene un mensaje del repositorio y lo guarda en la cola de mensajes
	{
		$mensaje = $this->obtener_mensaje($indice, $parametros);
		$this->informar_msg($mensaje,$nivel);
	}

//*******************************************************************************************
//****************************<  Informacion sobre el proceso   >****************************
//*******************************************************************************************	

	function obtener_estado_proceso()
/*
 	@@acceso: actividad
	@@desc: Indica el estado del proceso: ( OK | ERROR | INFRACCION )
*/
	{
		return $this->estado_proceso;
	}
	//-------------------------------------------------------------------------------

	function mostrar_info_proceso()
/*
 	@@acceso: objeto
	@@desc: Muestra el estado del proceso que se esta ejecutando
*/
	{
		if(is_array($this->info_proceso)){
			$mensaje = "";
			foreach($this->info_proceso as $nota){
				$mensaje .= $nota . "<br>";
			}
			if($mensaje!=""){
				if(is_array($this->info_proceso_gravedad)){
					if(in_array("error",$this->info_proceso_gravedad)){
						$tipo = "error";
						$subtitulo = null;
/*	
					$subtitulo = "CLASE <b>" . get_class($this) . "</b><br>".
									"OBJETO <b>" . $this->id[0] . " - " . $this->id[1] . "</b>".
									" ( " . $this->info["nombre"] . " ) ";
*/
					}else{
						$tipo = "info";
						$subtitulo = null;
					}
					echo ei_mensaje($mensaje, $tipo, $subtitulo);
				}
			}
		}
	}
	//-------------------------------------------------------------------------------

	function registrar_info_proceso($mensaje, $gravedad="info")
/*
 	@@acceso: objeto 
	@@desc: Registra un mensaje en la cola de mensajes del proceso
*/
	{
		$this->info_proceso[$this->info_proceso_indice] = $mensaje;
		$this->info_proceso_gravedad[$this->info_proceso_indice] = $gravedad;
		$this->info_proceso_indice++;
	}
	
//*******************************************************************************************
//********************************<  AUDITORIA Y LOG   >*************************************
//*******************************************************************************************	

	function observar($tipo,$observacion,$forzar_registro=false,$mostrar=false,$cortar_ejecucion=false)
/*
 	@@acceso: objeto
	@@desc: Deja guardada una observacion en la solicitud
	@@param: string | Tipo de error (info, error)
	@@param: string | Texto de la observacion
	@@param: boolean | forzar el registro el en LOG | false
	@@param: boolean | Mostrar el mensaje de error al usuario | false
	@@param: boolean | cortar la ejecucion | false
*/
	{
		
		$this->observaciones[]="[$tipo] ".$observacion;	//El objeto acumula sus propias observaciones
		toba::get_solicitud()->observar_objeto($this->id,$tipo,$observacion,$forzar_registro,$mostrar,$cortar_ejecucion);
	}

	function mostrar_observaciones()
	{
		ei_arbol($this->observaciones);
	}
	
//*******************************************************************************************
//**************************************<  MEMORIA   >***************************************
//*******************************************************************************************	
//La memoria es una array que se hace perdurable a travez del HILO
//Las clases que lo usen solo tienen generar las claves que necesiten dentro de este (ej: $this->memoria["una_cosa"])
//y despues llamar a los metodos "memorizar" para guardarla en el HILO y "cargar_memoria" para recuperarlo
//Preg: Por que no se usa el indice 0 en la clave del OBJETO?
//Res: proque no se pueden cargar objetos de dos proyectos en la misma solicitud

	function memorizar()
/*
 	@@acceso: objeto
	@@desc: Persiste el array '$this->memoria' para utilizarlo en la proxima invocacion del objeto
*/
	{
		if(isset($this->memoria)){
			toba::get_hilo()->persistir_dato_sincronizado("obj_".$this->id[1],$this->memoria);
		}else{

		}
	}
	
	function cargar_memoria()
/*
 	@@acceso: objeto
	@@desc: Recupera la memoria que dejo una instancia anterior del objeto. (Setea $this->memoria)
*/
	{
		if($this->memoria = toba::get_hilo()->recuperar_dato_sincronizado("obj_".$this->id[1])){
			$this->memoria_existencia_previa = true;
		}
	}

	function controlar_memoria()
/*
 	@@acceso: objeto
	@@desc: Controla la existencia de la memoria
*/
	//SI la memoria no se cargo se corta la ejecucion y despliega un mensaje
	{
		if ((!isset($this->memoria)) || (is_null($this->memoria))){
			$this->observar("error","Error cargando la MEMORIA del OBJETO. abms[". ($this->id[1]) ."]",false,true,true);
		}
	}

	function borrar_memoria()
/*
 	@@acceso: objeto
	@@desc: Dumpea la memoria
*/
	{
		unset($this->memoria);
		toba::get_hilo()->persistir_dato_sincronizado("obj_".$this->id[1],null);
	}

	function existio_memoria_previa()
	//Atencion, para que esto funcione antes hay que cargar la memoria
	{
		return $this->memoria_existencia_previa;
	}
	
//*******************************************************************************************
//****************************<  Memorizacion de PROPIEDADES   >*****************************
//*******************************************************************************************

	function mantener_estado_sesion()
	//Esta funcion retorna las propiedades que se desea persistir
	{
		return array();
	}

	function recuperar_estado_sesion()
	//Recupera las propiedades guardadas en la sesion
	{
		if(toba::get_hilo()->existe_dato_global($this->id_ses_grec)){
			//Recupero las propiedades de la sesion
			$temp = toba::get_hilo()->recuperar_dato_global($this->id_ses_grec);
			if(isset($temp["toba__indice_objetos_serializados"]))	//El objeto persistio otros objetos
			{
				/*
					PERSISTENCIA de OBJETOS 
					-----------------------
					Hay una forma de no hacer este IF: 
						Que en el consumo de "mantener_estado_sesion" se indique que propiedades son objetos.
						Hay comprobar si la burocracia justifica el tiempo extra que implica este mecanismo o no.
				*/
				$objetos = $temp["toba__indice_objetos_serializados"];
				unset($temp["toba__indice_objetos_serializados"]);
				foreach(array_keys($temp) as $propiedad)
				{
					if(in_array($propiedad,$objetos)){
						//La propiedad es un OBJETO!
						$this->$propiedad = unserialize($temp[$propiedad]);
					}else{
						$this->$propiedad = $temp[$propiedad];
					}
				}
			}
			else //El objeto solo persistio variables
			{
				foreach(array_keys($temp) as $propiedad)
				{
					$this->$propiedad = $temp[$propiedad];
				}
			}
		}
	}
	
	function guardar_estado_sesion()
	//Guardo propiedades en la sesion
	{
		//Busco las propiedades que se desea persistir entre las sesiones
		$propiedades_a_persistir = $this->mantener_estado_sesion();
		if(count($propiedades_a_persistir)>0){
			for($a=0;$a<count($propiedades_a_persistir);$a++){
				//Existe la propiedad
				if(isset($this->$propiedades_a_persistir[$a])) {
					if(is_object($this->$propiedades_a_persistir[$a])){
						/*
							PERSISTENCIA de OBJETOS 
							-----------------------
							Esta es la forma mas sencilla de implementar esto para el caso en el que
							el elemento persistidor permanece inactivo durante n request y luego vuelve
							a la actividad. Lo malo es que que hay que saber que propiedades son objetos 
							y cuales no.
							ATENCION: 
								Hay que tener mucho cuidado con las referencias circulares:
								ej: 	un db_tablas posee un por composicion db_registros y
										el db_registros posee una referencia a su controlador 	
										que es el mismo el db_tablas...
								En casos como este es necesario definir __sleep en el objeto hijo, para
									anular el controlador y __wakeup en el padre para restablecerlo
						*/
						$temp[$propiedades_a_persistir[$a]] = serialize($this->$propiedades_a_persistir[$a]);
						//Dejo la marca de que serialize un OBJETO.
						$temp["toba__indice_objetos_serializados"][] = $propiedades_a_persistir[$a];
					}else{
						$temp[$propiedades_a_persistir[$a]] = $this->$propiedades_a_persistir[$a];
					}
				} else {
					//$this->log->error($this->get_txt() . " Se solocito mantener el estado de una propiedad inexistente: '{$propiedades_a_persistir[$a]}' ");
					//echo $this->get_txt() . " guardar_estado_sesion '{$propiedades_a_persistir[$a]}' == NULL <br>";
				}
			}
			if(isset($temp)){
				//ei_arbol($temp,"Persistencia PROPIEDADES " . $this->id[1]);
				$temp['toba__descripcion_objeto'] = '['. get_class($this). '] ' . $this->info['nombre'];
				toba::get_hilo()->persistir_dato_global($this->id_ses_grec, $temp, true);
			}else{
				//Si existia y las propiedades pasaron a null, hay que borrarlo
				toba::get_hilo()->eliminar_dato_global($this->id_ses_grec);
			}
		}
	}

	function eliminar_estado_sesion($no_eliminar=null)
	{
		if(!isset($no_eliminar))$no_eliminar=array();
		$propiedades_a_persistir = $this->mantener_estado_sesion();
		for($a=0;$a<count($propiedades_a_persistir);$a++){
			if(!in_array($propiedades_a_persistir[$a], $no_eliminar)){
				unset($this->$propiedades_a_persistir[$a]);			
			}
		}
		toba::get_hilo()->eliminar_dato_global($this->id_ses_grec);
	}
	
	function get_estado_sesion()
	{
		$propiedades_a_persistir = $this->mantener_estado_sesion();
		if(count($propiedades_a_persistir)>0){
			$propiedades = get_object_vars($this);
			for($a=0;$a<count($propiedades_a_persistir);$a++){
				//Existe la propiedad
				if(in_array($propiedades_a_persistir[$a],$propiedades)){
					//Si la propiedad no es NULL
					if(isset( $this->$propiedades_a_persistir[$a]) ){
						$temp[$propiedades_a_persistir[$a]] = $this->$propiedades_a_persistir[$a];
					}
				}
			}
			if(isset($temp)){
				return $temp;
			}
		}
	}

//*******************************************************************************************
//*******************************************************************************************

	function mostrar_memoria()
/*
 	@@acceso: objeto
	@@desc: Dumpea la memoria
*/
	{
		if(isset($this->memoria)){
			ei_arbol($this->memoria,"MEMORIA Sincronizada del OBJETO [". $this->id[1] ."]");
		}
		if(isset($_SESSION["global"][$this->id_ses_g])){
			ei_arbol($_SESSION["global"][$this->id_ses_g],"MEMORIA GLOBAL del OBJETO [". $this->id[1] ."]");
		}
		//ATENCION, emprolijar esto un toque
		if(isset($_SESSION["global"][$this->id_ses_grec])){
			ei_arbol($_SESSION["global"][$this->id_ses_grec],"MEMORIA RECICLABLE del OBJETO [". $this->id[1] ."]");
		}
	}

//*******************************************************************************************
//*************************************<  DEPENDENCIAS  >************************************
//*******************************************************************************************

	function cargar_info_dependencias()
	{
		for($a=0;$a<count($this->info_dependencias);$a++){
			$this->indice_dependencias[$this->info_dependencias[$a]["identificador"]] = $a;//Columna de informacion donde esta la definicion
			$this->lista_dependencias[] = $this->info_dependencias[$a]["identificador"];
		}
	}

	
	/**
	 * Accede a una dependencia del objeto, opcionalmente si la dependencia no esta cargada, la carga
	 *
	 * @param string $id Identificador de la dependencia dentro del objeto actual
	 * @param boolean $cargar_en_demanda En caso de que el objeto no se encuentre cargado en memoria, lo carga
	 * @return Objeto
	 */
	function dependencia($id, $carga_en_demanda = true)
	{
		if (! $this->dependencia_cargada($id) && $carga_en_demanda) {
			$this->cargar_dependencia($id);
		}
		return $this->dependencias[$id];
	}	
	
	/**
	*	Agregar dinámicamente una dependencia
	*/
	function agregar_dependencia( $identificador, $proyecto, $objeto )
	{
		$sig = count($this->info_dependencias);
		$sql = "SELECT 
					'$identificador' 	as identificador,
					o.proyecto 			as proyecto,
					o.objeto 			as objeto,
					o.fuente_datos		as fuente,
					o.clase				as clase,
					o.subclase			as subclase,
					o.subclase_archivo	as subclase_archivo,
					c.archivo			as clase_archivo
				FROM
					apex_objeto o,
					apex_clase c
				WHERE
					o.objeto = '$objeto' AND
					o.proyecto = '$proyecto' AND
					o.clase = c.clase AND
					o.clase_proyecto = c.proyecto
		";
		$res = consultar_fuente($sql, "instancia");
		$this->info_dependencias[$sig] = $res[0];
		$this->indice_dependencias[$identificador] = $sig;
		$this->lista_dependencias[] = $identificador;	
	}

	function cargar_dependencia($identificador, $parametros=null)
 	{
		//-[0]- La dependencia ya esta cargada?
		if(isset($this->dependencias[$identificador])){
			return 999;
		}
		//-[1]- El indice es valido?
		if(!isset($this->indice_dependencias[$identificador])){
			throw new excepcion_toba("OBJETO [cargar_dependencia]: No EXISTE una dependencia asociada al indice [$identificador].");
			$this->observar("error","OBJETO [cargar_dependencia]: No EXISTE una dependencia asociada al indice [$identificador].",false,true,true);
			$this->obtener_info_dependencias();
			return -1;
		}
		$posicion = $this->indice_dependencias[$identificador];
		$clase = $this->info_dependencias[$posicion]['clase'];
		$clave['proyecto'] = $this->info_dependencias[$posicion]['proyecto'];
		$clave['componente'] = $this->info_dependencias[$posicion]['objeto'];
		$this->dependencias[$identificador] = constructor_toba::get_runtime( $clave, $clase );
		return true;
	}

	/**
	 * Retorna verdadero si la dependencia fue cargada en este pedido de página
	 */
	function dependencia_cargada($id)
	{
		return isset($this->dependencias[$id]);
	}
	
	/**
	 * @deprecated Desde 0.8.4, usar dependencia_cargada
	 */
	function existe_dependencia($id)
	{
		toba::get_logger()->obsoleto(__CLASS__, __METHOD__, "0.8.4", "Usar dependencia_cargada");
		return $this->dependencia_cargada($id);
	}

	function consultar_info_dependencia($dep,$dato=null)
	{
		if(isset($dato)){
			if(isset($this->info_dependencias[$this->indice_dependencias[$dep]][$dato])){
				return $this->info_dependencias[$this->indice_dependencias[$dep]][$dato];	
			}else{
				return null;
			}
		}else{
			if(isset($this->info_dependencias[$this->indice_dependencias[$dep]])){
				return $this->info_dependencias[$this->indice_dependencias[$dep]];	
			}else{
				return null;
			}
		}
	}

	function info_definicion_dependencias()
	{
		return $this->info_dependencias;
	}

	function get_dependencias_clase($ereg_busqueda)
	//Devuelve las dependencias cuya clase coincide con la expresion regular pasada como parametro
	{
		$ok = array();
		for($a=0;$a<count($this->info_dependencias);$a++){
			if( preg_match("/".$ereg_busqueda."/", $this->info_dependencias[$a]['clase']) ){
				$ok[] = $this->info_dependencias[$a]["identificador"];
			}
		}
		return $ok;
	}
	
	//-----------------------------------------------------------------------
	//  INTERFACE GRAFICA
	//-----------------------------------------------------------------------

	protected function evaluar_existencia_interface_anterior()
	{
		if(isset($this->memoria["generacion_interface"]))
		{
			if( $this->memoria["generacion_interface"] == 1 ){
				$this->interface_existencia_previa = true;
			}
		}
		$this->memoria["generacion_interface"] = 0;		
	}

	protected function existio_interface_previa()
	{
		return $this->interface_existencia_previa;
	}

	protected function registrar_generacion_interface()
	{
		$this->memoria["generacion_interface"] = 1;		
	}

//*******************************************************************************************

	function barra_superior_especifica(){}

	function barra_superior($titulo=null, $control_titulo_vacio=false, $estilo="objeto-barra-superior")
	{
		//Marco la existencia de una interface previa
		$this->registrar_generacion_interface();
		if($control_titulo_vacio){
			if(trim($this->info["titulo"])==""){
				return;	
			}
		}
		if(!isset($titulo)){
			$titulo = $this->info["titulo"];	
		}
		echo "<table class='tabla-0' width='100%'><tr>\n";
		//Vinculo a los EDITORES	
		if(apex_pa_acceso_directo_editor){ 
			if( ($this->id[0]) == toba::get_hilo()->obtener_proyecto() ) {
				echo "<td class='$estilo'>";
				$this->vinculo_editor();
				echo "</td>\n";
			}
		}
		//Barra de colapsado
		$colapsado = "";
		if ($this->info['colapsable'] && isset($this->objeto_js)) {
		
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"{$this->objeto_js}.cambiar_colapsado();\" title='Mostrar / Ocultar'";
			echo "<td class='$estilo'>";
			$img_min = recurso::imagen_apl('sentido_asc_sel.gif', false);
			echo "<img id='colapsar_boton_{$this->objeto_js}' src='$img_min' $colapsado>";
			echo "</td>\n";
		}
		//Titulo
		echo "<td class='$estilo' width='99%'><span $colapsado>$titulo</span></td>\n";
		if(trim($this->info["descripcion"])!=""){
			echo "<td class='$estilo'>\n";
			echo recurso::imagen_apl("descripcion.gif",true,null,null,$this->info["descripcion"]);
			echo "</td>\n";
		}
		if($this->existe_ayuda()){
			$parametros = array("objeto"=>$this->info["objeto"],"proyecto"=>$this->info["proyecto"]);
			echo "<td class='$estilo'>\n";
			echo toba::get_vinculador()->obtener_vinculo_a_item("toba","/basicos/ayuda_obj",$parametros,true);
			echo "</td>\n";
		}
		//Barra especifica dependiente de la clase
		echo "<td class='$estilo'>";
		echo $this->barra_superior_especifica();
		echo "</td>\n";
		if (isset($this->objeto_js)) {
			//Barra de mensajeria
			echo "<td class='$estilo' id='barra_{$this->objeto_js}' style='display:none'>";
			echo "<a href='javascript: cola_mensajes.mostrar({$this->objeto_js})'>";
			echo recurso::imagen_apl('warning.gif', true, null, null, 'Muestra las notificaciones encontradas durante la última operación.');
			echo "</a>";
			echo "</td>\n";
		}
		echo "</tr></table>";
	}
//-----------------------------------------------------------------------------
	
	function vinculo_editor()
/*
 	@@acceso:
	@@desc: 
*/
	//Muestra vinculos a los editores
	{
		if(apex_pa_acceso_directo_editor)
		{
			if($this->info["reflexivo"]){
				$estilo = "objeto-editores-reflexivo";
			}else{
				$estilo = "objeto-editores";
			}
	  		echo "<table class='tabla-0' border='1'><tr>\n";
			//Vinculo al EDITOR del OBJETO
			$vinc_editor= toba::get_vinculador()->obtener_vinculo_a_item(
						$this->info["clase_editor_proyecto"],$this->info["clase_editor_item"],
						array(apex_hilo_qs_zona=>implode(apex_qs_separador,$this->id)),
						true);        
	
			if($vinc_editor && isset($this->info["clase_editor"])){
				echo "<td class='$estilo'>";
	            echo $vinc_editor;
				echo "</td>\n";
			}
	
			//Vinculo al EDITOR de VINCULOS del OBJETO
	        $vinc_editor_vinc= toba::get_vinculador()->obtener_vinculo_a_item(
	        		"toba","/admin/objetos/vinculos",
	        		array(apex_hilo_qs_zona=>implode(apex_qs_separador,$this->id)),
	        		true);
	
			if($vinc_editor_vinc && $this->info["clase_vinculos"]==1){
				echo "<td class='$estilo'>";
	            echo $vinc_editor_vinc;
				echo "</td>\n";
			}
	
			//Vinculo a las NOTAS del OBJETO
			$vinc_notas= toba::get_vinculador()->obtener_vinculo_a_item(
						"toba","/admin/objetos/notas",
						array(apex_hilo_qs_zona=>implode(apex_qs_separador,$this->id)),
						true);
	        if ($vinc_notas){
	    		echo "<td class='$estilo'>";
	            echo $vinc_notas;
	    		echo "</td>\n";
	        }
	        
			//Vinculo a la INFORMACION del OBJETO
			$vinc_info= toba::get_vinculador()->obtener_vinculo_a_item(
						"toba","/admin/objetos/info",
						array(apex_hilo_qs_zona=>implode(apex_qs_separador,$this->id)),
						true);
	        if ($vinc_info){
	    		echo "<td class='$estilo'>";
	            echo $vinc_info;
	    		echo "</td>\n";
	        }            
	   		echo "</tr></table>";
		}
	}
}
?>
