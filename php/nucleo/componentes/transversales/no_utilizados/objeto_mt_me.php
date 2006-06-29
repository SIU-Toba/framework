<?php
require_once("objeto_mt.php");	//Ancestro de todos los OE
include_once("nucleo/lib/interface/form.php");// HTML FROM

/*
Pasos a seguir:

	- Falta disparar eventos de moficacion con los cambios de pantalla.
	- Javascript del formulario
	- Como se maneja la memoria en un ML cuando el mismo maneja mas de una clase
		1) Subdependencias
		2) Que el ML recuerde con ID + Clave

Varios:
		
	- Como englobar en MTs los hijos de los UTS conocidos, de manera generica?
	- Como usar en MTs objetos que no sean UTs?
		
*/

class objeto_mt_me extends objeto_mt
/*
 	@@acceso: nucleo
	@@desc: Descripcion
*/
{
	//Etapas
	var $indice_etapas;
	var $etapa_actual;
	var $etapa_previa;
	//Dependencias
	var $dependencias_actual;
	var $dependencias_previas;
	//Proceso
	var $clave_actual;

	
	function objeto_mt_me($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_mt($id);
		global $db,	$ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE	= ADODB_FETCH_ASSOC;
		//	Datos	Basicos
		$sql = "SELECT	incremental	as					incremental
				FROM	apex_objeto_mt_me
				WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
				AND	objeto_mt_me='".$this->id[1]."';";
		$rs =	$db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("OBJETO MT ME: No	se	genero el recordset.	id[".	$this->id[0].",".	$this->id[1] ."] clase[". $this->info["clase"] ."]	--	" . $db["instancia"][apex_db_con]->ErrorMsg()."	--	SQL: $sql -- ");
		}
		if($rs->EOF){
			throw new excepcion_toba("OBJETO	MT ME: El	objeto solicitado	NO	EXISTE. id[". $this->id[0].",". $this->id[1]	."] clase[". $this->info["clase"] ."]");
		}
		$temp	= $rs->getArray();
		$this->info_mt_me = $temp[0];
		//	Definicion EF
		$sql = "SELECT		posicion					  as posicion,
								etiqueta					  as etiqueta,
								descripcion				  as descripcion,
								objetos					  as objetos		
				FROM	apex_objeto_mt_me_etapa
				WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
				AND	objeto_mt_me = '".$this->id[1]."'
				ORDER	BY	posicion;";
		$rs =	$db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("OBJETO	 MT ME - Etapa:	No	se	genero el recordset.	id[".	$this->id[0].",".	$this->id[1] ."] clase[". $this->info["clase"] ."]. -- <b>"	. $db["instancia"][apex_db_con]->ErrorMsg().	" </b> -- SQL:	$sql --");
		}
		if($rs->EOF){
			throw new excepcion_toba("OBJETO	 MT ME - Etapa:	No	hay informacion sobre elementos de formulario en: id[". $this->id[0].",". $this->id[1]	."] clase[". $this->info["clase"] ."]");
		}
		$this->info_mt_me_etapa = $rs->getArray();
		$this->clave_actual = null;

		//Inicializo informacion
		for($a = 0; $a<count($this->info_mt_me_etapa);$a++){
			//Preparo el nombre del SUBMIT de cada etapa
			$this->info_mt_me_etapa[$a]["submit"] = $this->submit."_".$this->info_mt_me_etapa[$a]["posicion"];
			//Indice de acceso por etapas
			$this->indice_etapas[$this->info_mt_me_etapa[$a]["posicion"]] = $a;
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------  Manejo de DEPENDENCIAS  --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_dependencias($etapa=null)
/*
 	@@acceso: actividad
	@@desc: Devuelve las dependencias asociadas especificamente con el ME
*/
	{
		//Devuelvo las dependencias de todas las etapas.
		$dependencias = array();
		if(isset($etapa)){
			if(isset($this->info_mt_me_etapa[$this->indice_etapas[$etapa]]["objetos"])){
				$dependencias = explode(",",$this->info_mt_me_etapa[$this->indice_etapas[$etapa]]["objetos"]);
				$dependencias = array_map("trim",$dependencias);
			}
		}else{
			//Todas las etapas
			for($a=0;$a<count($this->info_mt_me_etapa);$a++)
			{
				$temp = null;
				if(isset($this->info_mt_me_etapa[$a]["objetos"])){
					$temp = explode(",",$this->info_mt_me_etapa[$a]["objetos"]);
					$temp = array_map("trim",$temp);
				}
				if(is_array($temp)) $dependencias = array_merge($dependencias, $temp) ;
			}
		}
		return $dependencias;
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias($dependencias)
	{
		//Parametros a los formularios
		$parametro["nombre_formulario"] = $this->nombre_formulario;
		//Cargo dependencias
		foreach($dependencias as $dep){
			//Crear
			$this->cargar_dependencia($dep);		
			//Inicializar
			$this->dependencias[$dep]->inicializar($parametro);
			//Recuperar estado interno
			if( $this->existe_dato($dep) ){
				$estado = $this->recuperar_dato($dep);
				$this->dependencias[$dep]->establecer_estado_interno($estado);
			}
		}
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias_activas()
	{
		//-[1]- Obtengo la dependencias de la etapa ACTUAL
		$this->dependencias_actual = $this->obtener_dependencias($this->etapa_actual);
		//-[2]- Obtengo la dependencias de la etapa ANTERIOR
		if(isset($this->etapa_previa)){
			$this->dependencias_previa = $this->obtener_dependencias($this->etapa_previa);
		}else{
			$this->dependencias_previa = array();
		}
		$dependencias_activas = array_unique(array_merge($this->dependencias_actual, $this->dependencias_previa));
		$this->cargar_dependencias($dependencias_activas);
	}
	//-------------------------------------------------------------------------------

	function cargar_dependencias_inactivas()
/*
 	@@acceso: interno
	@@desc: Carga las dependencias
*/
	{
		//Cargo todas las dependencias (Las instanciadas no se repiten)
		$dependencias_activas = array_keys($this->dependencias);
		//$dependencias = $this->lista_dependencias;
		$dependencias = $this->obtener_dependencias();
		foreach($dependencias as $dep){
			if(!(in_array($dep, $dependencias_activas))){
				$dependencias_inactivas[] = $dep;
			}
		}
		$this->cargar_dependencias($dependencias_inactivas);
	}
	//-------------------------------------------------------------------------------

	function descargar_dependencias()
	{
		foreach(array_keys($this->dependencias) as $dep){
			$estado = $this->dependencias[$dep]->obtener_estado_interno();
			$this->guardar_dato($dep, $estado);
		}
	}	

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------  PROCESAMIENTO de ETAPAS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function evaluar_etapa()
	{
		$this->etapa_actual = null;
		$this->etapa_previa = null;
		//Toda la navegacion interna es por POST
		if(acceso_post()){
			//Navegacion de TABS: en que etapa entre
			for($a=0;$a<count($this->info_mt_me_etapa);$a++)
			{
				if(isset($_POST[$this->info_mt_me_etapa[$a]["submit"]])){
					$this->etapa_actual = $this->info_mt_me_etapa[$a]["posicion"];
					$this->etapa_previa = $this->memoria["etapa"];
					break;			
				}
			}
			//Se activo un subelemento, mantengo la etapa
			if(!isset($this->etapa_actual)){ 
				if(isset($this->memoria["etapa"])){
					$this->etapa_actual = $this->memoria["etapa"];
					$this->etapa_previa = $this->memoria["etapa"];
				}else{
					echo ei_mensaje("ERROR de NAVEGACION");
				}
			}
		}else{
			//Entrada a la ETAPA inicial!!
			$this->limpiar_memoria_global();//Limpio la memoria reclamada por este objeto
			$this->etapa_actual = $this->info_mt_me_etapa[0]["posicion"];
		}
		$this->memoria["etapa"] = $this->etapa_actual;
	}
	//-------------------------------------------------------------------------------

	function procesar($clave=null)
	{
		//-[0]- Inicializacion de proceso
		//Proceso
		if(isset($clave)){
			$this->clave_actual = $clave;
		}
		//Veo en que etapa estoy.
		$this->evaluar_etapa();
		//Creo las dependencias de esta etapa
		$this->cargar_dependencias_activas();		

		//-[1]- Procesamiento de la <<< SALIDA de la etapa PREVIA >>>
		if(isset($this->etapa_previa)){
			$proceso_salida_especifico = "procesar_salida_" . $this->etapa_previa;
			if(method_exists($this, $proceso_salida_especifico)){
				$this->$proceso_salida_especifico();
			}else{
				//Utilizo el procesamiento generico
				$this->procesar_salida();
			}
		}

		//-[2]- Procesamiento de la <<< ENTRADA a etapa ACTUAL >>>
		//Hay un metodo de procesamiento especifico
		$proceso_entrada_especifico = "procesar_entrada_" . $this->etapa_actual;
		if(method_exists($this, $proceso_entrada_especifico)){
			$this->$proceso_entrada_especifico();
		}else{
			//Utilizo el procesamiento generico
			$this->procesar_entrada();
		}

		//-[3]- Procesamiento de la <<< OPERACION >>>
		if($this->controlar_activacion()){ //Procesar el Marco transaccional
			$this->cargar_dependencias_inactivas();
			$this->procesar_operacion();
		}
		//Guardo el estado de las dependencias
		$this->descargar_dependencias();	
	}
	//-------------------------------------------------------------------------------

	function procesar_entrada()
	//Entrada GENERICA a una ETAPA
	{
		//echo "ENTRADA<br>";
		if(isset($this->clave_actual)){
			foreach($this->dependencias_actual as $dependencia){
				//ei_arbol($this->dependencias[$dependencia]->info_estado());
				if(!$this->dependencias[$dependencia]->estado_cargado()){
					$this->dependencias[$dependencia]->cargar_db( $this->clave_actual );		
				}
			}			
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_salida()
	//Salida GENERICA a una ETAPA
	{
		//echo "SALIDA<br>";
		foreach($this->dependencias_previa as $dependencia)
		{
			//-[1]- Cargo el POST
			$this->dependencias[$dependencia]->cargar_post();
			//-[2]- Valido el ESTADO
			$this->dependencias[$dependencia]->validar_estado();
			//-[3]- Guardo el estado de las dependencias
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_operacion()
	//Esta funcion se redeclara para hacer algo especifico.
	{
		//El mecanismo STANDART aca es tomal el SQL de cada UT
		//y ejecutar todo en una transaccion
		echo ei_mensaje("Activacion FINAL");		
	}

//*******************************************************************************************
//**************************************<  Memoria GLOBAL   >********************************
//*******************************************************************************************
/*
	Esto se encontraba antes en el ancestro de todos los objeto... pero solo se usaba aca.
	Se trata de una forma vieja de pensar la persistencia en la memoria, entonces se paso aca.
*/
	function limpiar_memoria_global()
	{
		unset($_SESSION["global"][$this->id_ses_g]);
	}
	
	function existe_dato($indice)
	{
		return isset($_SESSION["global"][$this->id_ses_g][$indice]);
	}

	function guardar_dato($indice, $dato)
	//El indice no puede ser "x_propiedades_persistidas"
	{
		$_SESSION["global"][$this->id_ses_g][$indice] = $dato;
	}
	
	function recuperar_dato($indice)
	{
		return $_SESSION["global"][$this->id_ses_g][$indice];
	}
	
	function eliminar_dato($indice)
	{
		unset($_SESSION["global"][$this->id_ses_g][$indice]);
		if(count($_SESSION["global"][$this->id_ses_g])==0){
			unset($_SESSION["global"][$this->id_ses_g]);
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------------------------------  SALIDA  HTML --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_interface()
/*
 	@@acceso: interno
	@@desc: Genera la INTERFACE de la transaccion.
*/
	{
		//Las hijas cambian la forma de mostrar la interface para una etapa?
		$interface_especifica = "obtener_interface_" . $this->etapa_actual;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			echo "<table class='tabla-0'  width='100%'>\n";
			foreach($this->dependencias_actual as $dep){
				echo "<tr><td>";
				$this->dependencias[$dep]->obtener_html();	
				echo "</td></tr>\n";
			}
			echo "</table>\n";
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_html()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del Marco Transaccional
*/
	{
/*
		//-[1]- Muestro el resultado del procesamiento
		$this->mostrar_info_proceso();
		if(is_array($this->dependencias)){
			foreach(array_keys($this->dependencias) as $dependencia){
				$this->dependencias[$dependencia]->mostrar_info_proceso();//DETALLES
			}
		}

		$this->obtener_javascript_global_consumido();
		$this->obtener_javascript_validador_form();
*/
		//-[2]- Genero la SALIDA
		$vinculo = $this->solicitud->vinculador->generar_solicitud(null,null,null,true);
		echo "\n\n<!-- #################################### Inicio MT ( ".$this->id[1]." ) ######################## -->\n\n\n\n";
		echo "<br>\n";
		echo form::abrir($this->nombre_formulario, $vinculo, " onSubmit='return validar_form_".$this->nombre_formulario."(this)' ");
		echo "<div align='center'>\n";
		echo "<table width='80%' class='tabla-0'>\n";

/*
		echo "<tr><td>";
		$this->barra_superior();
		echo "</td></tr>\n";
*/
		//Tabs
		echo "<tr><td class='celda-vacia'>";
		$this->obtener_tabs();
		echo "</td></tr>\n";

		//Interface de la etapa correspondiente
		echo "<tr><td class='tabs-contenedor'>";
		$this->obtener_interface();		//El hijo genera la INTERFACE
		$this->obtener_botones();
		echo "</td></tr>\n";

		echo "</table>\n";
		echo "</div>\n";
		echo form::cerrar();
		echo "<br>\n";
		echo "<!-- ------------------ Fin MT -------------- -->\n\n\n";
	}
	//-------------------------------------------------------------------------------


	function obtener_tabs()
/*
 	@@acceso: interno
	@@desc: Genera la INTERFACE de los TABS
*/
	{
		echo "<table width='100%' class='tabla-0'>\n";
		echo "<tr>";
		//echo "<td width='1'  class='tabs-solapa-hueco'>".gif_nulo(3,1)."</td>";
		foreach($this->info_mt_me_etapa as $etapa)
		{
			if($this->etapa_actual == $etapa["posicion"]){
				echo "<td class='tabs-solapa-sel'>";
				echo form::button($etapa["submit"],$etapa["etiqueta"],null,"tabs-boton-sel");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}else{
				echo "<td class='tabs-solapa'>";
				echo form::submit($etapa["submit"],$etapa["etiqueta"],"tabs-boton");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}
		}
		echo "<td width='90%'  class='tabs-solapa-hueco'>".gif_nulo()."</td>\n";
		echo "<td width='1%'  class='tabs-solapa-hueco'>";
		$this->vinculo_editor();
		echo "</td>\n";
		echo "</tr>";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_global_consumido()
/*
 	@@acceso: interno
	@@desc: Genera el javascript GLOBAL que se consumen los EF. El javascript GLOBAL esta compuesto
	@@desc: por porciones de codigo reutilizadas entre distintos subelementos.
*/
	{
		$consumo_js = $this->get_consumo_javascript();
		if(is_array($this->dependencias)){
			//Que necesita el Marco?
			//Que necesita cada UT?
			foreach(array_keys($this->dependencias) as $ut){
				$temp = $this->dependencias[$ut]->get_consumo_javascript();
				if(isset($temp)) $consumo_js = array_merge($consumo_js, $temp);
			}
		}
		js::cargar_consumos_globales($consumos_js);
	}
	//-------------------------------------------------------------------------------

	function obtener_javascript_validador_form()
/*
 	@@acceso: interno
	@@desc: Devuelve la interface del formulario
*/
	{
		//-[2]- Incluyo el JAVASCRIPT de CONTROLA el FORM
		echo "\n<script language='javascript'>
eliminar_{$this->nombre_formulario} = 0;";//FLAG que indica si el evento corresponde a la eliminacion del registro
		//Funcion que valida al formulario en el cliente
		echo "
		
//----------- Funcion VALIDADORA del FORM ----------

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

		//Control especifico del MT
		$this->obtener_javascript();
		
		//Cargo el JAVASCRIPT de las dependencias
		if(is_array($this->dependencias)){
			foreach(array_keys($this->dependencias) as $ut){
				echo $this->dependencias[$ut]->obtener_javascript();
			}
		}
        echo    "return true;\n";//Todo OK, salgo de la validacion del formulario
        echo    "}
//----------- FIN Funcion VALIDADORA del FORM ----------
</script>
";
	}
	//-------------------------------------------------------------------------------
	
	function obtener_botones()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del Marco Transaccional
*/
	{
		echo "<table align='center' width='100%'>\n";
		echo "<tr><td>";
		echo form::submit($this->submit,"Procesar","abm-input");
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
}
?>
