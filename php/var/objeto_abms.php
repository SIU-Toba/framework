<?
require_once("objeto.php");	//Ancestro de todos los OE
require_once("nucleo/browser/interface/ef.php");// Elementos de interface

define("apex_abms_submit","procesar");
define("apex_abms_submit_eli","Eliminar");
define("apex_abms_submit_mod","Modificar");

class objeto_abms extends objeto
/*
 	@@acceso: actividad
	@@desc: ABM simple sobre una tabla
*/
{
	var $info_abms;					// interno | string | Definicion del objeto ABM
	var $info_abms_ef;              // interno | string | Definicion de los EF
	var $elemento_formulario;		// interno | array | Rererencias a los ELEMENTOS de FORMULARIO
	var $datos;						// interno | array | Datos actuales (con el estado actual de cada EF)
    var $nombre_formulario;         // interno | string | Nombre del formulario HTML producto del ABM
	var $nombre_ef_cli;				// interno | array | Identificadores en el cliente de los EF
	var $submit;                    // interno | string | Nombre del boton de SUBMIT del ABM
	var $etapa_actual;  			// interno | string | Etapa ACTUAL. Puede ser SA, SM, PA o PM. Se define en el metodo procesar()
    var $lista_ef;                  // interno | array | Lista completa de a los EF
    var $lista_ef_clave;            // interno | array | Lista de elementos que forman parte de la CLAVE (PK)
    var $lista_ef_secuencia;       	// interno | array | Lista de elementos que representan secuencias
    var $lista_ef_post;             // interno | array | Lista de elementos que se reciben por POST
    var $faltas;                    // interno | string | Array donde se guardan las infracciones realizadas
	var $flag_no_propagacion;		// interno | string | Flag que indica si hay que dejar de reproducir el estado de la MEMORIA
	
//################################################################################
//###########################                         ############################
//###########################      INICIALIZACION     ############################
//###########################                         ############################
//################################################################################
    	
	function objeto_abms($id,&$solicitud)
/*
 	@@acceso: nucleo
	@@desc: constructor de la clase
*/
	{
		parent::objeto($id, $solicitud);
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		//-------------------------------------------------------------------
		//----------------------------> ABM <--------------------------------
		//-------------------------------------------------------------------
		$sql = "SELECT	tabla as						tabla,
						titulo as						titulo,
						ev_mod_eliminar as				ev_mod_eliminar,
						ev_mod_estado_i as				ev_mod_estado_i,
						auto_reset as					auto_reset
				FROM	apex_objeto_abms
				WHERE	objeto_abms_proyecto='".$this->id[0]."'
                AND     objeto_abms='".$this->id[1]."';";
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","OBJETO ABM: No se genero el recordset. id[". $this->id[0].",". $this->id[1] ."] clase[". $this->info["clase"] ."] -- " . $db["instancia"][apex_db_con]->ErrorMsg()." -- SQL: $sql -- ");
		}
		if($rs->EOF){
			monitor::evento("bug","OBJETO ABM: El objeto solicitado NO EXISTE. id[". $this->id[0].",". $this->id[1] ."] clase[". $this->info["clase"] ."]");
		}
		$temp = $rs->getArray();
		$this->info_abms = $temp[0];
		//-------------------------------------------------------------------
		//------------------> Elementos de Formulario ABM <------------------
		//-------------------------------------------------------------------
		$sql = "SELECT 	identificador as        identificador,
                        columnas as 			columnas,
                        obligatorio as          obligatorio,
						elemento_formulario as	elemento_formulario,
						inicializacion as		inicializacion,
						etiqueta as 			etiqueta,
						descripcion as			descripcion,
                        clave_primaria as       clave_primaria,
						orden as				orden
				FROM 	apex_objeto_abms_ef
				WHERE	objeto_abms_proyecto='".$this->id[0]."'
                AND     objeto_abms='".$this->id[1]."'
                AND     (desactivado=0 OR desactivado IS NULL)
				ORDER BY orden;";
		$rs = $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","OBJETO ABM - EF: No se genero el recordset. id[". $this->id[0].",". $this->id[1] ."] clase[". $this->info["clase"] ."]. -- <b>" . $db["instancia"][apex_db_con]->ErrorMsg(). " </b> -- SQL: $sql --");
		}
		if($rs->EOF){
			monitor::evento("bug","OBJETO ABM - EF: No hay informacion sobre elementos de formulario en: id[". $this->id[0].",". $this->id[1] ."] clase[". $this->info["clase"] ."]");
		}
		$this->info_abms_ef = $rs->getArray();
		//Boton de PROCESAR al que tengo que responder (puede haber otros ABM en el ITEM)
        $this->nombre_formulario = "abms_" . $this->id[1];
		$this->submit = $this->nombre_formulario . "_" . apex_abms_submit;
		$this->etapa_actual = "";
		$this->estado_proceso = "";
        $this->faltas = array();
		$this->lista_ef = array();
		$this->lista_ef_clave = array();
		$this->lista_ef_secuencia = array();
		$this->lista_ef_post = array();
		$this->flag_no_propagacion = $this->id[0] . "no_prop";
        //Creo el array de objetos EF (Elementos de Formulario) que conforman el ABM
		$this->crear_elementos_formulario();
	}
	//-------------------------------------------------------------------------------

	function crear_elementos_formulario()
/*
 	@@acceso: interno
	@@desc: Genera el array de objetos EF que constituye la columna vertebral del ABM
*/
	{
		for($a=0;$a<count($this->info_abms_ef);$a++){

            //-[1]- Armo las listas que determinan el plan de accion del ABM
            $this->lista_ef[] = $this->info_abms_ef[$a]["identificador"];
            switch ($this->info_abms_ef[$a]["elemento_formulario"]) {
                case "ef_oculto":
                    break;
                case "ef_oculto_secuencia":
                    $this->lista_ef_secuencia[] = $this->info_abms_ef[$a]["identificador"];
                    break;
                case "ef_oculto_proyecto":
                    break;
                case "ef_oculto_usuario":
                    break;
                default:
                    $this->lista_ef_post[] = $this->info_abms_ef[$a]["identificador"];
            }
            //Lista de CLAVES del ABM
            if($this->info_abms_ef[$a]["clave_primaria"]==1){
                $this->lista_ef_clave[] = $this->info_abms_ef[$a]["identificador"];
            }
            //-[2]- Genero el ARRAY de ELEMENTOS de FORMULARIO
            //Genero el array de parametros que inicializa al EF
            $parametros = parsear_propiedades($this->info_abms_ef[$a]["inicializacion"]);
            //ei_arbol($parametros,"PARAMETRO adicional ELEMENTO: ". $this->info_abms_ef[$a]["identificador"]);
			//A los elementos que consulten la base les seteo la fuente de datos que utiliza el objeto
			//Lo hago fijandome si tiene una clave "sql"
			if(isset($parametros["sql"])){
				$parametros["fuente"]=$this->info["fuente"];
			}
            //Preparo el identificador del dato que maneja el EF.
            //Esta parametro puede ser un ARRAY o un string: exiten EF complejos que manejan mas de una
			//Columna de la tabla a la que esta asociada el ABM
            if(ereg(",",$this->info_abms_ef[$a]["columnas"])){
                $dato = explode(",",$this->info_abms_ef[$a]["columnas"]);
				for($d=0;$d<count($dato);$d++){//Elimino espacios en las claves
					$dato[$d]=trim($dato[$d]);
				}
            }else{
                $dato = $this->info_abms_ef[$a]["columnas"];
            }
			$sentencia_creacion_ef = "\$this->elemento_formulario['".$this->info_abms_ef[$a]["identificador"]."'] =& new ".
                  $this->info_abms_ef[$a]["elemento_formulario"] ."(	\$this->id, ".
			  													"'" .	$this->nombre_formulario ."', '". 
                                                                        $this->info_abms_ef[$a]["identificador"] ."', '". 
                                                                        $this->info_abms_ef[$a]["etiqueta"] ."', '". 
                                                                        $this->info_abms_ef[$a]["descripcion"] ."', ". 
                                                                        "\$dato, '". 
                                                                        $this->info_abms_ef[$a]["obligatorio"] ."', ".
                                                                        "\$parametros);";
			//echo $sentencia_creacion_ef . "<br>";
			eval($sentencia_creacion_ef);
		}	
	}

	//-------------------------------------------------------------------------------

	function info_definicion()
/*
 	@@acceso: actividad
	@@desc: Muestra la definicion del OBJETO
*/
	{
		$dump["info_padre"]=parent::info_definicion();
		$dump["info_abms"]=$this->info_abms;
		$dump["info_abms_ef"]=$this->info_abms_ef;
		ei_arbol($dump,"Definicion del OBJETO");
	}
	//-------------------------------------------------------------------------------

	function info_estado()
/*
 	@@acceso: actividad
	@@desc: Muestra el estado del OBJETO
*/
	{
		$dump["padre"]=parent::info_estado();
		$dump["submit"]= $this->submit;
		$dump["etapa_actual"]= $this->etapa_actual;
		$dump["estado_proceso"]= $this->estado_proceso;
		$dump["lista_ef"]=$this->lista_ef;
		$dump["lista_ef_clave"]=$this->lista_ef_clave;
		$dump["lista_ef_secuencia"]=$this->lista_ef_secuencia;
		$dump["lista_ef_post"]=$this->lista_ef_post;
		$dump["elemento_formulario"]=$this->elemento_formulario;
		$dump["faltas"]=$this->faltas;
		ei_arbol($dump,"Estado del OBJETO");
	}
	//-------------------------------------------------------------------------------

	function info()
/*
 	@@acceso: actividad
	@@desc: Muestra es la informacion COMPLETA
*/
	{
		$this->info_definicion();
		$this->info_estado();
		$this->info_estado_ef();
	}	
	//-------------------------------------------------------------------------------

	function info_estado_ef()
/*
 	@@acceso: actividad
	@@desc: Muestra el estado de los EF
*/
	{
		foreach ($this->lista_ef as $ef){
			$temp1[$ef] = $this->elemento_formulario[$ef]->obtener_estado();
			$temp2[$ef] = $this->elemento_formulario[$ef]->obtener_dato();
		}
		$temp["DATOS"]=$temp2;
		$temp["ESTADO"]=$temp1;
		ei_arbol($temp,"Estado actual de los ELEMENTOS de FORMULARIO");
	}
	//-------------------------------------------------------------------------------

	function cargar_estado_ef($array_ef)
/*
 	@@acceso: actividad
	@@desc: Esta funcion permite establecer el valor de un elemento del FORMULARIO (Visible u Oculto)
	@@param: array | una entrada por EF (id->valor) que quiera cargar. los EF compuestos tienen que recibir como valor un ARRAY con la forma que espera el EF destino
*/
	{
		if(is_array($array_ef)){
			foreach($array_ef as $ef => $valor){
				if(isset($this->elemento_formulario[$ef])){
					$this->elemento_formulario[$ef]->cargar_estado($valor);
				}else{
					$this->registrar_info_proceso("OBJETO_ABMS: [cargar_estado_ef] ATENCION: No existe un elemento de formulario identificado '$ef'","error");
				}
			}
		}else{
			echo ei_mensaje("Los EF se cargan a travez de un array asociativo (\"clave\"=>\"dato a cargar\")!");
		}
	}
	//-------------------------------------------------------------------------------

	function resetear_ef()
/*
 	@@acceso: actividad
	@@desc: Resetea los elementos de formulario
*/
	{
		//Atencion, no deberia tocar los ocultos?...
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->resetear_estado();
		}
	}
	//-------------------------------------------------------------------------------

    function procesar($registro=null)
/*
 	@@acceso: actividad
 	@@desc: Se identifica que ETAPA del CICLO de vida del ABM que corresponde al momento ACTUAL,y se dispara el procesamiento del mismo
	@@param: string | ID del registro que se debe procesar (Debe estar formateado de acuerdo a la clave definida)
*/
    {
		//Escuchar al CANAL de recepcion del objeto para ver si se recibio 
		//un dato
		if(isset($this->canal_recibidos)){//Recibi por el canal el registro que tengo que cargar?
			$registro = explode(apex_qs_separador,$this->canal_recibidos);		
			//Si existia una memoria que indicaba otro estado, la recepcion del parametro
			//fuerza la etapa de SOLICITAR MODIFICACION
			$this->memoria["proxima_etapa"]="SM";
			$this->procesar_etapa_SM($registro);
			//ei_arbol($registro,"CLAVE CANAL");
		}else{//No se recibio una clave por el canal
	    	$this->cargar_memoria();//Recupero la informacion cargada por la instanciacion previa.
			//Hay una especificacion de proxima etapa generada por la instanciacion previa?
        	if(isset($this->memoria["proxima_etapa"]))
	        {//-------> hay MEMORIA (La solicitud precedente genero una instancia del mismo objeto)
				//Una solicitud de procesamiento solo debe responderse cuando la disparo por el boton 
				//SUBMIT del FORM generado por la instanciacion anterior del mismo OBJETO.
				if(isset($_POST[$this->submit])){
					$activacion = true;		//Apretaron el SUBMIT de este FORM
				}else{
					$activacion = false;	//El submit no es de este formulario, la atencion esta en otro lugar...
				}
				if($this->memoria["proxima_etapa"]=="PA"){			
					if($activacion){
						$this->procesar_etapa_PA();
					}else{
						//La intanciacion previa del objeto preparo la situacion para realizar el ALTA
						//Pero no se envio el formulario. Retorno al estado de Solicitud de ALTA
						$this->procesar_etapa_SA();
					}
				}elseif ($this->memoria["proxima_etapa"]=="PM"){		
					if($activacion){
						$this->procesar_etapa_PM();
					}else{
						//La intanciacion previa del objeto preparo la situacion para realizar la MODIFICACION
						//Pero no se envio el formulario. Retorno al estado de Solicitud de MODIFICACION
						//Mientras se activan otros objetos, el ABM sigue reproduciondo su ESTADO
						if(isset($registro)){
							//El estado se mantiene por seteo directo
							$this->procesar_etapa_SM($registro);
						}else{
							//El estado se mantiene a partir de la clave de la memoria
							//Existe una solicitud especifica de no propagacion por las dudas...
							if($this->solicitud->hilo->obtener_parametro($this->flag_no_propagacion)){
								$this->procesar_etapa_SA();
							}else{
								$this->procesar_etapa_SM($this->memoria["clave"]);
							}
						}
					}
				}
	        }else
			{//--------> NO HAY MEMORIA (No hubo una instanciacion del objeto en la SOLICITUD precedente)
				if(isset($registro)){
					//Se paso el ID de un registro como parametro
					$this->procesar_etapa_SM($registro);
				}else{
					$this->procesar_etapa_SA($registro);
				}
	        }
			//Memorizo el estado para la proxima instanciacion
	    }
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: actividad
	@@desc: Devulve la interface grafica del ABM
*/
	{
		//Muestro el resultado del procesamiento
		$this->mostrar_info_proceso();
		//Genero la interface
		if($this->estado_proceso!="INFRACCION")
		{
			$this->obtener_javascript_formulario();
			$this->obtener_html_formulario();
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_etapa()
/*
 	@@acceso: actividad
	@@desc: Indica cual es la ETAPA actual ( SA | SM | PA | PM)
*/
	{
		return $this->etapa_actual;	
	}
	//-------------------------------------------------------------------------------

	function obtener_clave()
/*
 	@@acceso: actividad
	@@desc: Devuelve la clave que se esta procesando como un array asociativo (dato/valor)
	@@desc: (El dato representa la columna de la tabla).La clave de los EFs
	@@desc: Atencion: SI el ABM esta configurado en AUTO-RESET, es probable que este metodo devuelva un NULL
*/
	{
		$clave_actual = array();//Preparo un array para cargar claves
		foreach($this->lista_ef_clave as $clave)
		{
			$temp = $this->elemento_formulario[$clave]->obtener_estado();
			if(is_array($temp)){//Los EF compuestos ya devuelven un array dato/valor
				$clave_actual = array_merge($clave_actual,$temp);
			}else{//Los EF simples devuelven un string, tengo que armar el par dato/valor a mano
				$dato = $this->elemento_formulario[$clave]->obtener_dato();
				$clave_actual[$dato] = $temp;
			}
		}
		return $clave_actual;
	}
	//-------------------------------------------------------------------------------

	function obtener_clave_serializada()
/*
 	@@acceso: actividad
	@@desc: Retorna la clave que esta procesando el ABM, en el formato que utiliza los ABMS
	@@retorno: string | Valores que componen la clave separados por coma
*/
	{
		return implode(apex_qs_separador,$this->memoria["clave"]);
 	}
	//-------------------------------------------------------------------------------

	function obtener_datos()
/*
 	@@acceso: actividad
	@@desc: Recupera el estado actual del formulario
	@@retorno: array | estado de cada elemento de formulario
*/
	{
		foreach ($this->lista_ef as $ef)
		{
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error de consistencia interna del EF
					echo ei_mensaje("obtener_datos: Error de consistencia interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta(),"error");
				}
				for($x=0;$x<count($dato);$x++){
					$registro[$dato[$x]] = $estado[$dato[$x]];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$registro[$dato] = $estado;
			}
		}
		return $registro;
	}
	//-------------------------------------------------------------------------------

	function ejecutar_metodo_ef($ef, $metodo, $parametro)
/*
 	@@acceso: actividad
	@@desc: Esto sirve para comunicarse con EF que pueden cambiar en tiempo de ejecucion
	@@desc: EJ: un combo que necesita cambiar una propiedad del WHERE segun la solicitud
	@@param: string | elemento de formulario a llamar
	@@param: string | metodo a llamar en el EF
	@@param: array | Argumentos de la funcion
*/
	{
		if(isset($this->elemento_formulario[$ef])){
			$this->elemento_formulario[$ef]->$metodo($parametro);
		}else{
			echo ei_mensaje("El EF identificado '$ef' no existe.");
		}
	}

//#################################################################################
//##########################                             ##########################
//##########################   PROCESAMIENTO de ETAPAS   ##########################
//##########################                             ##########################
//#################################################################################
//Implementacion de la logica inherente a los momentos del ciclo de vida del ABM

	function procesar_etapa_SA($clave=null)
/*
 	@@acceso: interno
	@@desc: SOLICITUD de ALTA. Estado INICIAL por defecto
	@@param:
*/
	{
		$this->etapa_actual = "SA";
		if(isset($clave)){
			//Este mecanismo no esta terminado, para asignar el valor usar la ACTIVIDAD
			$this->memoria["clave"] = $this->formatear_clave($clave);
			//ei_arbol($this->memoria["clave"]);
		}else{
			unset($this->memoria["clave"]);
		}
		$this->memoria["proxima_etapa"] = "PA";//En la proxima instanciacion PROCESO
		$this->estado_proceso = "OK";
		$this->memorizar();
	}

	function procesar_etapa_SM($registro)
/*
 	@@acceso: interno
	@@desc: Etapa SOLICITUD de MODIFICACION. Busca el registro de la base y lo carga en la interface
	@@param: 
*/
	{
		$this->etapa_actual = "SM";
		$this->memoria["clave"] = $this->formatear_clave($registro);
		//ei_arbol($this->memoria["clave"]);
		$resultado = $this->recuperar_registro_db();
		if($resultado[0]){
			//El registro se cargo bien, valido su estado
			$resultado = $this->validar_estado();
			if($resultado[0]){
				//La validacion NO devolvio errores
				$this->memoria["proxima_etapa"] = "PM";//En la proxima instanciacion PROCESO
				$this->estado_proceso = "OK";
			}else{//Error en la validacion, se esta cargando un registro no permitido!
				$this->registrar_info_proceso($resultado[1],"error");
				//Lo mando al estado de PROCESAR MODIFICACION
				$this->memoria["proxima_etapa"] = "PM";//En la proxima instanciacion PROCESO
				$this->estado_proceso = "OK";
			}
		}else{//No se pudo cargar el registro solicitado
			//echo ei_mensaje($resultado[1]);
			//Si se intenta cargar un REGISTRO que no existe, se muestra la interface de INSERT
			//$this->cargar_estado_ef($registro);//NO, los formatos no coinciden, hacerlo en la actividad.
			$this->procesar_etapa_SA($registro);
		}
		$this->memorizar();
	}

	function procesar_etapa_PA()
/*
 	@@acceso: interno
	@@desc: PROCESAR ALTA
*/
	{
		$this->etapa_actual = "PA";
		$this->recuperar_registro_formulario();
		$resultado = $this->validar_estado();
		if($resultado[0]){
			//La validacion NO devolvio errores
			$resultado = $this->procesar_insert();//INSERTO el REGISTRO
			if($resultado[0]){//Las cosas salieron bien...
				$this->estado_proceso = "OK";
				//Si el ABM posee la propiedad AUTO_RESET, hay que vaciarlo y volver al INICIO
				//(Mecanismo para facilitar la carga sucesiva)
				if($this->info_abms["auto_reset"]){
					unset($this->memoria);
					$this->memoria["proxima_etapa"] = "PA";
					$this->resetear_ef();//Limpio el FORM
				}else{//No hay autoreset, la proxima instanciacion es para modificar lo ingresado
					//Tengo que obtener la clave del registro insertado para saber que modificar
					$this->recuperar_secuencias();//Cargo las secuencias, (el valor se genero en la base, el EF esta desactualizado)
					$this->memoria["clave"] = $this->obtener_clave();//Recupero la clave
					$this->memoria["proxima_etapa"] = "PM";
					//NOTA: falta agregar los campos a los que la base les de un valor por defecto y despues se pueden modificar
				}
			}else{//El registro no se pudo INSERTAR
				$this->registrar_info_proceso($resultado[1],"error");
				$this->memoria["proxima_etapa"] = "PA";//La proxima vuelvo a intentar...
				$this->estado_proceso = "ERROR";
			}
		}else{//Error en la validacion, el usuario cometio un ERROR ingresando DATOS.
			$this->registrar_info_proceso($resultado[1],"error");
			$this->memoria["proxima_etapa"] = "PA";//El usuario modifica el FORM y vuelve a intentar
			$this->estado_proceso = "ERROR";
		}
		$this->memorizar();
	}

	function procesar_etapa_PM()
/*
 	@@acceso: interno
	@@desc: PROCESAR MODIFICACION
*/
	{
		$this->etapa_actual = "PM";
		//Solo proceso si tengo memorizado con que REGISTRO hay que trabajar
		if(!isset($this->memoria["clave"])){
			$this->registrar_info_proceso("No se definio el registro a modificar");
			$this->estado_proceso = "INFRACCION";
			unset($this->memoria["proxima_etapa"]);
			return;
		}
		//Sino hay claves primarias definidas, esto no funciona
		if(count($this->lista_ef_clave)<1){
			$this->registrar_info_proceso("No hay claves primarias definidas. Es imposible llevar a cabo la accion solicitada","error");
			$this->estado_proceso = "INFRACCION";
			unset($this->memoria["proxima_etapa"]);
			return;
		}
		//UPDATE o DELETE?
		if($_POST[$this->submit]==apex_abms_submit_mod){		//-a----------->	MODIFICAR  ---------
			$this->etapa_actual = "PM-U";
			$this->recuperar_registro_formulario();
			$resultado = $this->validar_estado();
			if($resultado[0]){
				//La validacion NO devolvio errores
				$resultado = $this->procesar_update();//MODIFICO el REGISTRO
				if($resultado[0]){//Las cosas salieron bien...
					$this->estado_proceso = "OK";
					//Si parte de la clave estaba en el formulario, esta pudo haber sido modificada
					//y por lo tanto tiene que ser recargada
					$this->memoria["clave"] = $this->obtener_clave();//Recupero la clave
					//Si el ABM posee la propiedad AUTO_RESET (Facilita la carga sucesiva)...
					if($this->info_abms["auto_reset"]){
						unset($this->memoria);
						$this->memoria["proxima_etapa"] = "PA";
						$this->resetear_ef();//Limpio el FORM
					}else{//No hay autoreset, la proxima instanciacion es para modificar lo modificado
						$this->memoria["proxima_etapa"] = "PM";//Esto no deberia ser necesario, pero...
					}
				}else{//Error procesarndo el UPDATE
					$this->registrar_info_proceso($resultado[1],"error");
					$this->estado_proceso = "ERROR";
					//El registro no se pudo eliminar, el registro tiene que quedar en estado SM
					$this->procesar_etapa_SM($this->memoria["clave"]);
				}
			}else{//Error en la validacion, el usuario cometio un ERROR ingresando DATOS.
				$this->registrar_info_proceso($resultado[1],"error");
				$this->memoria["proxima_etapa"] = "PM";//Que el usuario modifique el FORM y vuelve a intentar
				$this->estado_proceso = "ERROR";
			}
		}elseif(($_POST[$this->submit]==apex_abms_submit_eli) 
					&& ($this->info_abms["ev_mod_eliminar"])){	//-b------------->	ELIMINAR  ------------
			$this->etapa_actual = "PM-D";
			$resultado = $this->procesar_delete();////ELIMINO el REGISTRO
			if($resultado[0]){//Las cosas salieron bien...
				$this->estado_proceso = "OK";
				unset($this->memoria);
				$this->memoria["proxima_etapa"] = "PA";
			}else{
				$this->registrar_info_proceso($resultado[1],"error");
				$this->estado_proceso = "ERROR";
				//El registro no se pudo eliminar, el registro tiene que quedar en estado SM
				$this->procesar_etapa_SM($this->memoria["clave"]);
			}
		}else{
			//Esto no deberia pasar nunca.
			$this->registrar_info_proceso("ERROR, el ABM no fue activado correctamente");
			$this->estado_proceso = "INFRACCION";
			unset($this->memoria["proxima_etapa"]);
		}
		$this->memorizar();
	}
	
//################################################################################
//#############################                     ##############################
//#############################      UTILERIA       ##############################
//#############################                     ##############################
//################################################################################

	function recuperar_registro_formulario()
/*
 	@@acceso: interno
	@@desc: Carga el estado de cada EF a partir del POST!
*/
	{
		//Cargo los EF que pertenecen al formulario
		foreach ($this->lista_ef as $ef){
			$this->elemento_formulario[$ef]->cargar_estado();
		}
	}
	//-------------------------------------------------------------------------------

	function recuperar_registro_db()
/*
 	@@acceso: interno
	@@desc: Busca un registro de la base y lo carga en los EF. Utiliza la clave existente en la memoria
*/
	{
		//Busco las columnas que tengo que recuperar
		foreach ($this->lista_ef as $ef){	//Tengo que recorrer todos los EF...
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				for($x=0;$x<count($dato);$x++){
					$sql_col[] = $dato[$x];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$sql_col[] = $dato;
			}
		}
		//Armo la porcion de SQL que corresponde al WHERE
		foreach($this->memoria["clave"] as $columna => $valor){
			$sql_where[] = "( $columna = '$valor')";
		}
		$sql = 	" SELECT " . implode(", ",$sql_col) . 
				" FROM " . $this->info_abms["tabla"] .
				" WHERE " . implode(" AND ",$sql_where) .";";
		//Busco el registro en la base
		global $db, $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
		if(!$rs){//SQL mal formado
			$this->observar("error","OBJETO ABMS [cargar_registro] - No se genero un recordset [SQL] $sql - [ERROR] " . 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),false,true,true);
		}
		if($rs->EOF){//NO existe el registro
			return array(false,"El registro no fue recuperado - SQL: $sql");
		}
		$datos_db = current($rs->getArray());//Siempre va a ser un solo registro
		//ei_arbol($datos_db,"DATOS DB");
		//Seteo los EF con el valor recuperado
		foreach ($this->lista_ef as $ef){	//Tengo que recorrer todos los EF...
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				$temp = array();
				for($x=0;$x<count($dato);$x++){
					$temp[$dato[$x]]=$datos_db[$dato[$x]];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$temp = $datos_db[$dato];
			}
			$this->elemento_formulario[$ef]->cargar_estado($temp);
		}
		return array(true,"El registro fue cargado satisfactoriamente");
	}
	//-------------------------------------------------------------------------------

	function validar_estado()
/*
 	@@acceso: interno
	@@desc: Valida el registro (cualquiera sea su procedencia: DB o FORM)
	@@pendiente: grados, un ef_oculto_proyecto no la deberia dejar pasar...
*/
	{
		//Valida el estado de los ELEMENTOS de FORMULARIO
        $resultado[0] = true;
		foreach ($this->lista_ef as $ef){
			$temp = $this->elemento_formulario[$ef]->validar_estado();
            if(!$temp[0]){
                $resultado[0] = false;
                $this->faltas[] = "[". $this->elemento_formulario[$ef]->obtener_etiqueta(). "] - ". $temp[1];
            }
		}

		//Validacion ESPECIFICA del ABM
		$this->datos = $this->obtener_datos();//CArga los datos para que el validador los encuentre
		$status = $this->validar_registro_servidor();
		if(!$status){
			$resultado[0] = $status;
		}

		//Si hubo un error, empaqueto y devuelvo.
		if(!$resultado[0]){
			$resultado[1]= "<b>ERROR en la validacion!</b><BR><br>";
			foreach($this->faltas as $falta){
				$resultado[1] .= "$falta<BR>";
			}
		}
        return $resultado;
	}

/*    function controlar_validacion()
    {
        if($this->validar_estado()===false){
            ei_arbol($this->faltas,"FALTAS en la VALIDACION");
        }else{
            echo "Esta todo OK<br>";
        }
    }*/
	//-------------------------------------------------------------------------------

	function procesar_insert()
/*
 	@@acceso: interno
	@@desc: INSERTA el registro en la BASE
*/
	{
		global $db;
		foreach ($this->lista_ef as $ef){					//Tengo que recorrer todos los EF...
			if(!(in_array($ef,$this->lista_ef_secuencia)))	//... Menos las secuencias
			{
				$dato = $this->elemento_formulario[$ef]->obtener_dato();
				$estado = $this->elemento_formulario[$ef]->obtener_estado();
				if(is_array($dato)){	//El EF maneja DATO COMPUESTO
					if((count($dato))!=(count($estado))){//Error de consistencia interna del EF
						return array(false,"procesar_insert: Error de consistencia interna en el EF etiquetado: ".
											$this->elemento_formulario[$ef]->obtener_etiqueta() );
					}
					for($x=0;$x<count($dato);$x++){
						$sql_col[] = $dato[$x];
						$sql_val[] = $estado[$dato[$x]];
					}
				}else{					//El EF maneja un DATO SIMPLE
					$sql_col[] = $dato;
					$sql_val[] = $estado;
				}
			}
		}
		//Reduzco repeticiones en los ARRAYS
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			$columnas[$sql_col[$a]] = $sql_val[$a];
		}
		$sql_col = array_keys($columnas);
		$sql_val = array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");

		//Genero el SQL de INSERCION
		$sql = "INSERT INTO ". $this->info_abms["tabla"] ." (". implode(",",$sql_col) .") 
				VALUES ('". implode("','",$sql_val) ."');";
		//ATENCION!!: esto implica que nunca se va a poder grabar la palabra "NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		//return array(false,"SQL: ".$sql);
		if( $db[$this->info["fuente"]][apex_db_con]->Execute($sql) === false ){
			return array(false,"No se pudo realizar la operacion solicitada. [SQL] $sql - [ERROR] " . 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg());
		}else{
			return array(true,"El registro ha sido incorporado");
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_update()
/*
 	@@acceso: interno
	@@desc: Realizo un UPDATE del registro en la base.
*/
	{
		global $db;
		//Recupero los valores de los EF, para generar el SQL de UPDATE
		foreach ($this->lista_ef_post as $ef){		//Recorro SOLO los EF que vienen del POST
			$dato = $this->elemento_formulario[$ef]->obtener_dato();
			$estado = $this->elemento_formulario[$ef]->obtener_estado();
			if(is_array($dato)){	//El EF maneja DATO COMPUESTO
				if((count($dato))!=(count($estado))){//Error de consistencia interna del EF
					//No tengo que dejar una observacion tambien???
					ei_arbol($dato,"DATOS manejados");
					ei_arbol($estado,"ESTADO interno");
					return array(false,"procesar_update: Error de consistencia interna en el EF etiquetado: ".
										$this->elemento_formulario[$ef]->obtener_etiqueta() );
				}
				for($a=0;$a<count($dato);$a++){
					$sql_col[] = $dato[$a];
					$sql_val[] = $estado[$dato[$a]];
				}
			}else{					//El EF maneja un DATO SIMPLE
				$sql_col[] = $dato;
				$sql_val[] = $estado;
			}
		}
		//Reduzco repeticiones en los ARRAYS
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");
		for($a=0;$a<count($sql_col);$a++){
			$columnas[$sql_col[$a]] = $sql_val[$a];
		}
		$sql_col = array_keys($columnas);
		$sql_val = array_values($columnas);
		//ei_arbol($sql_col,"DATO");
		//ei_arbol($sql_val,"VALOR");

		//Armo la porcion de SQL que corresponde a las COLUMNAS
		for($a=0;$a<count($sql_col);$a++){
			$sql_update[] = $sql_col[$a] . " = '" . $sql_val[$a] . "'";
		}
		//Armo la porcion de SQL que corresponde al WHERE
		foreach($this->memoria["clave"] as $columna => $valor){
			$sql_where[] = "( $columna = '$valor')";
		}
		//Armo el SQL completo
		$sql =	" UPDATE " . $this->info_abms["tabla"] . 
				" SET " . implode(", ",$sql_update) . 
				" WHERE " . implode(" AND ",$sql_where) .";";
		//ATENCION!!: esto implica que nunca se va a poder grabar la palabra "NULL"
		$sql = ereg_replace("'NULL'","NULL",$sql);
		if( $db[$this->info["fuente"]][apex_db_con]->Execute($sql) === false ){
			return array(false,"No se pudo realizar la operacion solicitada. [SQL] $sql - [ERROR] ". 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg());
		}else{
			return array(true,"El registro fue modificado correctamente");
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_delete()
/*
 	@@acceso: interno
	@@desc: ELIMINA el registro en la BASE
*/
	{
		global $db;
		//Grabo el contenido de la interface en la base
		//Armo la porcion de SQL que corresponde al WHERE
		foreach($this->memoria["clave"] as $columna => $valor){
			$sql_where[] = "( $columna = '$valor')";
		}
		$sql = 	" DELETE FROM " . $this->info_abms["tabla"] . 
				" WHERE " . implode(" AND ",$sql_where) .";";
		//echo $sql;
		//return array(true,"El registro fue modificado correctamente");
 		if( $db[$this->info["fuente"]][apex_db_con]->Execute($sql) === false ){
			return array(false,"No se pudo realizar la operacion solicitada. [SQL] $sql - [ERROR] ". 
							$db[$this->info["fuente"]][apex_db_con]->ErrorMsg());
		}else{
			return array(true,"El registro fue modificado correctamente");
		}
	}
	//-------------------------------------------------------------------------------

	function recuperar_secuencias()
/*
 	@@acceso: interno
	@@desc: Recupera el valor de las secuencias de la base
*/
	{
		if(is_array($this->lista_ef_secuencia)){//Hay secuencias?
			global $db, $ADODB_FETCH_MODE;
			//Itero las secuencias y les cargo su estado
			foreach($this->lista_ef_secuencia as $secuencia){
				$columna = $this->elemento_formulario[$secuencia]->obtener_dato();//Una secuencia no puede tener un dato compuesto.
				$sql = "SELECT MAX($columna) FROM {$this->info_abms['tabla']};";
				$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
				$rs = $db[$this->info["fuente"]][apex_db_con]->Execute($sql);
				if(!$rs){//SQL mal formado
					$this->observar("error","OBJETO ABMS [recuperar_secuencias] - error buscando el valor de la secuencia: $secuencia 
								[SQL] $sql - [ERROR] " . $db[$this->info["fuente"]][apex_db_con]->ErrorMsg(),false,true,true);
				}
				if($rs->EOF){//NO existe el registro
					$this->observar("error","OBJETO ABMS [recuperar_secuencias] - error buscando el valor de la secuencia (NULL) : $secuencia",false,true,true);
				}
				if(trim($rs->fields[0])==""){
					$this->observar("error","OBJETO ABMS [recuperar_secuencias] - error buscando el valor de la secuencia (\"\") : $secuencia",false,true,true);
				}
				//Cargo el valor de la secuencia en el EF
				$this->elemento_formulario[$secuencia]->cargar_estado($rs->fields[0]);	
			}
		}
	}
	//-------------------------------------------------------------------------------

	function formatear_clave($clave_pos)
/*
 	@@acceso: interno
	@@desc: Le da formato a un clave definida en forma posicional, transformandola en formato asociativo
	@@param: 
*/
	{
		//ei_arbol($clave_pos,"Clave RECIBIDA");
		//Obtengo los nombres de los indices (datos manejados por los EF clave)
		foreach($this->lista_ef_clave as $ef){
			$temp = $this->elemento_formulario[$ef]->obtener_dato();
			//Si se maneja un dato complejo lo DESARMO...				
			if(is_array($temp)){
				//ei_arbol($temp,"Dato recibido de un EF");
				for($a=0;$a<count($temp);$a++){
					$dato[] = $temp[$a];
				}
			}else{
				$dato[] = $temp;
			}
		}
		//ei_arbol($dato,"FORMATO de la CLAVE");
		//Si la cantidad de indices no coincide con la cantidad de valores, algo esta mal
		if(count($clave_pos)!=count($dato)){
			echo ei_mensaje("La clave solicitada esta mal formada","error");
			ei_arbol($clave_pos,"CLAVE recibida");
			ei_arbol($dato,"Estructura esperada");
			exit();//No puedo seguir el procesamiento si las cosas estan asi
		}
		//Armo la definicion asociativa. El criterio es: el orden de los valores pasados
		//tiene que corresponder al orden de los EF, y dentro de estos al orden de las
		//columnas definidas.
		$indice = 0;
		//Itero de esta manera para dar la posibilidad de pasar una clave no numerica (aunque ordenada)
		foreach($clave_pos as $clave){
			$clave_asoc[$dato[$indice]]=$clave;
			$indice++;
		}
		return $clave_asoc;
	}
	
	function validar_registro_servidor()
	//Reescrita en los hijos
	{ return true; }
	function validar_registro_cliente()
	//Reescrita en los hijos
	{ return null; }

	function obtener_html_lista()
/*
 	@@acceso: nulo
	@@desc: Devuelve una lista que permite elegir un registro a cargar en el ABM
*/
	{
		$this->cargar_info_dependencias();
		//$this->obtener_info_dependencias();
		if(is_array($this->indice_dependencias["objeto_lista"])){
			$lista = $this->cargar_dependencia("objeto_lista",0);
			$this->dependencias[$lista]->cargar_datos();
			$this->dependencias[$lista]->obtener_html();
		}
 	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_formulario()
/*
 	@@acceso: interno
	@@desc: devuelve el javascript del formulario
*/
	{
		//----------------> codigo generico CONDICIONAL <----------------

		//Este codigo acopla solo si algun EF lo necesita.
		$dependencias = array();
		foreach ($this->lista_ef_post as $ef){
			$temp = $this->elemento_formulario[$ef]->obtener_consumo_javascript();
			if(isset($temp)) $dependencias = array_merge($dependencias, $temp);
		}
		js::cargar_consumos_globales($dependencias);

		//----------------> codigo generico BASICO <----------------

		echo "\n<script language='javascript'>
eliminar_{$this->nombre_formulario} = 0;";//FLAG que indica si el evento corresponde a la eliminacion del registro
		//Funcion que valida al formulario en el cliente
		echo "
function validar_form_{$this->nombre_formulario}(formulario){\n";
//        echo "alert(\"estoy aca!!\");return false;\n";

		//----------------> Confirmacion en de la ELIMINACION del REGISTRO
		echo "if( eliminar_{$this->nombre_formulario} == 1 ){
	if(!(confirm('Desea ELIMINAR el registro?'))){
		eliminar_{$this->nombre_formulario}=0;
		return false;
	}else{
		return true;
	}
}\n";

		//Obtengo el javascript de validacion de cada EF
		foreach ($this->lista_ef_post as $ef){
			echo $this->elemento_formulario[$ef]->obtener_javascript();
		}

		//----------------> codigo ESPECIFICO del ABM <----------------

		//Cargo el array que posee los nombres que los EF toman en el cliente
		foreach ($this->lista_ef_post as $ef){
			$this->nombre_ef_cli[$ef] = $this->elemento_formulario[$ef]->obtener_id_form();
		}

		echo "\n//Validador especifico\n";
		echo $this->validar_registro_cliente();
		echo "\n";

        echo    "return true;\n";//Todo OK, salgo de la validacion del formulario
        echo    "}";
        echo    "</script>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_html_formulario()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del formulario
*/
	{
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "<br>\n";
		echo form::abrir($this->nombre_formulario, $vinculo, " onSubmit='return validar_form_".$this->nombre_formulario."(this)' ");
		echo "<div align='center'>\n";
		echo "<table class='objeto-base'>\n";
		echo "<tr><td>";
		$this->barra_superior($this->info_abms["titulo"]);
		echo "</td></tr>\n";
		echo "<tr><td>";
		echo "<TABLE width='100%' class='tabla-0'>";
		foreach ($this->lista_ef_post as $ef){
			echo "<tr><td class='abm-fila'>\n";
			$this->elemento_formulario[$ef]->obtener_interface();
			echo "</td></tr>\n";
		}
		echo "</table>\n";
		echo "</td></tr>\n";
		echo "<tr><td>";
		$this->obtener_html_botones();
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo form::cerrar();
/*
		//Dejo el FOCO en el primer elemento del FORM
		$nombre_elemento = $this->elemento_formulario[$this->lista_ef_post[0]]->obtener_id_form();
		echo "\n<script language='javascript'>document.". $this->nombre_formulario.".$nombre_elemento.focus()</script>\n"; 
*/
		echo "<br>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_html_botones()
/*
 	@@acceso: interno
	@@desc: Genera los botones de ABM
*/
	{
		echo "<table class='tabla-0' align='center' width='100%'>\n";
		echo "<tr><td class='abm-zona-botones'>";
//		if(isset($this->memoria["accion"])){
		if($this->memoria["proxima_etapa"]=="PA"){
			echo form::submit($this->submit,"Agregar","abm-input");
		}elseif($this->memoria["proxima_etapa"]=="PM"){
			if($this->info_abms["ev_mod_estado_i"])
				echo "&nbsp;&nbsp;" . form::button("boton","Limpiar formulario","onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->flag_no_propagacion=>1),true)."';\"","abm-input");
			echo "&nbsp;&nbsp;" . form::submit($this->submit, apex_abms_submit_mod, "abm-input");
			if($this->info_abms["ev_mod_eliminar"])
				echo  "&nbsp;&nbsp;&nbsp;" .form::submit($this->submit, apex_abms_submit_eli, "abm-input-eliminar", " onclick='eliminar_{$this->nombre_formulario}=1' ") . "&nbsp;";
		}else{
			echo "Atencion: la proxima etapa no se encuentra definida!";
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
}
?>
