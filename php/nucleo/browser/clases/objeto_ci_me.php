<?php
require_once("objeto_ci.php");	//Ancestro de todos los OE
include_once("nucleo/browser/interface/form.php");// HTML FROM

/*
	//ATENCION Ordenar los eventos antes de rutearlos... (seleccion ultimos)
*/

class objeto_ci_me extends objeto_ci
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
	var $dependencias_previas;

	function objeto_ci_me($id)
/*
 	@@acceso: nucleo
	@@desc: Muestra la definicion del OBJETO
*/
	{
		parent::objeto_ci($id);
		//Inicializo informacion
		for($a = 0; $a<count($this->info_ci_me_etapa);$a++){
			//Preparo el nombre del SUBMIT de cada etapa
			$this->info_ci_me_etapa[$a]["submit"] = $this->submit."_".$this->info_ci_me_etapa[$a]["posicion"];
			//Indice de acceso por etapas
			$this->indice_etapas[$this->info_ci_me_etapa[$a]["posicion"]] = $a;
		}
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
/*
 	@@acceso:
	@@desc: 
*/
	{
		$sql = parent::obtener_definicion_db();
		//-- CI - Multiples etapas --------------
		$sql["info_ci_me_etapa"]["sql"] = "SELECT	posicion			  as posicion,
													etiqueta			  as etiqueta,
													descripcion			  as descripcion,
													objetos				  as objetos,
													pre_condicion		as	pre_condicion,	
													post_condicion	    as	post_condicion,	
													ev_procesar		    as	ev_procesar		
										FROM	apex_objeto_mt_me_etapa
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me = '".$this->id[1]."'
										ORDER	BY	posicion;";
		$sql["info_ci_me_etapa"]["tipo"]="x";
		$sql["info_ci_me_etapa"]["estricto"]="1";
		return $sql;
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
			if(isset($this->info_ci_me_etapa[$this->indice_etapas[$etapa]]["objetos"])){
				$dependencias = explode(",",$this->info_ci_me_etapa[$this->indice_etapas[$etapa]]["objetos"]);
				$dependencias = array_map("trim",$dependencias);
			}
		}else{
			//Todas las etapas
			for($a=0;$a<count($this->info_ci_me_etapa);$a++)
			{
				$temp = null;
				if(isset($this->info_ci_me_etapa[$a]["objetos"])){
					$temp = explode(",",$this->info_ci_me_etapa[$a]["objetos"]);
					$temp = array_map("trim",$temp);
				}
				if(is_array($temp)) $dependencias = array_merge($dependencias, $temp) ;
			}
		}
		return $dependencias;
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
		//ei_arbol($dependencias_activas);
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
        $navegando_tabs = false;     //Indica que se esta navegando por el objeto_ci

        for($a=0;$a<count($this->info_ci_me_etapa);$a++)
        {
            if(isset($_POST[$this->info_ci_me_etapa[$a]["submit"]])){
            	$this->etapa_actual = $this->info_ci_me_etapa[$a]["posicion"];
            	$this->etapa_previa = $this->memoria["etapa"];
                $navegando_tabs = true;
   				//echo "Etapa " . $this->etapa_actual;
            	break;			
            }
        } 

		//Toda la navegacion interna es por POST
		if(!$navegando_tabs){
			//Navegacion de TABS: en que etapa entre
			//Se activo un subelemento, mantengo la etapa
			if(isset($this->memoria["etapa"])){
				$this->etapa_actual = $this->memoria["etapa"];
				$this->etapa_previa = $this->memoria["etapa"];
			}else{
				//--> Entrada a la ETAPA inicial!!
				//echo "Estado INICIAL";
				$this->limpiar_memoria_global();//Limpio la memoria reclamada por este objeto
				$this->etapa_actual = $this->info_ci_me_etapa[0]["posicion"];
				//$this->cn->reset();
			}
		}
		$this->memoria["etapa"] = $this->etapa_actual;
	}
	//-------------------------------------------------------------------------------

	function procesar()
	{
		// -[0]- Cancelar la operacion?
		if( $this->operacion_cancelada() ){
			$this->cancelar_operacion();
		}
		//ATENCION: Falta controlar si el paso de etapas es VALIDO
		//Veo en que etapa estoy.
		$this->evaluar_etapa();
		//Creo las dependencias de esta etapa
		$this->cargar_dependencias_activas();		

		try 
		{
			//-[1]- Procesamiento de la <<< SALIDA de la etapa PREVIA >>>
			if(isset($this->etapa_previa)){
				$this->disparar_salida();
			}

			//-[2]- Procesamiento de la <<< ENTRADA a etapa ACTUAL >>>
			$this->disparar_entrada();

		} catch(excepcion_toba $e) 
		{
			$this->cargar_etapa_anterior();
			echo ei_mensaje($e->getMessage(), 'error');
		}
		
		//-[3]- Procesamiento de la <<< OPERACION >>>
		if($this->controlar_activacion()){ //Procesar el Marco transaccional
			//$this->cargar_dependencias_inactivas();
			$this->procesar_operacion();
		}
		//-[4]- Cargar de los DAOS
		$this->cargar_daos();
				
		//Guardo el estado de las dependencias
		$this->descargar_dependencias();	
	}
	//-------------------------------------------------------------------------------

	function disparar_entrada()
	//Dispara la entrada a una etapa
	{
		$proceso_entrada_especifico = "procesar_entrada_" . $this->etapa_actual;
		if(method_exists($this, $proceso_entrada_especifico)){
			return $this->$proceso_entrada_especifico();
		}else{
			//Utilizo el procesamiento generico
			return $this->procesar_entrada();
		}
	}
	//-------------------------------------------------------------------------------
	
	function disparar_salida()
	{
		$proceso_salida_especifico = "procesar_salida_" . $this->etapa_previa;
		if(method_exists($this, $proceso_salida_especifico)){
			return $this->$proceso_salida_especifico();
		}else{
			//Utilizo el procesamiento generico
			return $this->procesar_salida();
		}
	}
	//-------------------------------------------------------------------------------

	function procesar_salida()
	//Salida GENERICA a una ETAPA
	{
		//echo "SALIDA<br>";
		$this->controlar_eventos($this->dependencias_previa);
		//--> controlo que se cumplo la POST-Condicion!!!
		if($metodo = $this->info_ci_me_etapa[$this->indice_etapas[$this->etapa_previa]]["post_condicion"]){
			return $this->cn->$metodo();
		}
		return true;	
	}
	//-------------------------------------------------------------------------------

	function procesar_entrada()
	//Entrada GENERICA a una ETAPA
	{
		//echo "ENTRADA<br>";
		//Controlo la PRE-Condicion
		if($metodo = $this->info_ci_me_etapa[$this->indice_etapas[$this->etapa_actual]]["pre_condicion"]){
			$this->cn->$metodo();
		}
		$this->cargar_datos_dependencias();
		return true;	
	}
	//-------------------------------------------------------------------------------

	function cargar_etapa_anterior()
	{
		$this->etapa_actual = $this->etapa_previa;
		$this->dependencias_actual = $this->dependencias_previa;
		$this->memoria["etapa"] = $this->etapa_actual;
		$this->disparar_entrada();
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
		//-[2]- Genero la SALIDA
		$ancho = isset($this->info_ci["ancho"]) ? $this->info_ci["ancho"] : "10%";
		echo "<table width='$ancho' class='tabla-0'>\n";
		//Tabs
		echo "<tr><td class='celda-vacia'>";
		$this->obtener_barra_navegacion();
		echo "</td></tr>\n";
		//Interface de la etapa correspondiente
		echo "<tr><td class='tabs-contenedor'>";
		//Las hijas cambian la forma de mostrar la interface para una etapa?
		$interface_especifica = "obtener_interface_" . $this->etapa_actual;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			$this->interface_estandar();
		}
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function interface_estandar()
	{
		$existe_previo = 0;
		echo "<table class='tabla-0'  width='100%'>\n";
		foreach($this->dependencias_actual as $dep){
			if($existe_previo){
				//Separador
				echo "<tr><td class='celda-vacia'><hr></td></tr>\n";
			}
			echo "<tr><td class='celda-vacia'>";

			//Acceso al PLAN de ruteo de EVENTOS de la DEPENDENCIA
			if($this->solicitud->hilo->entorno_instanciador() || true)
			{
				//Vinculo al EDITOR del OBJETO
				//Hay que controlar que el objeto no se CI
				$zona = implode(apex_qs_separador,$this->id);
				//Armo la clave a enviar por el CANAL del FORM de edicion de deps
				$temp = $this->id; 
				$temp[] = $dep; 
				$ei = implode(apex_qs_separador, $temp);
				//Esta es una llamada harcodeada al FORM de propiedades de la dependencia
				$id_objeto_formulario =  151;
				$vinc_ruteo= $this->solicitud->vinculador->obtener_vinculo_a_item(
							"toba","/admin/objetos/dependencias",
							array(	apex_hilo_qs_zona => $zona,
									apex_hilo_qs_canal_obj .$id_objeto_formulario => $ei ),
							true);   
		
				if($vinc_ruteo){
		            echo $vinc_ruteo;
				}
			}
			//HTML de la DEPENDENCIA
			$this->dependencias[$dep]->obtener_html();	
			echo "</td></tr>\n";
			$existe_previo = 1;
		}
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------
	
	function obtener_pie()
/*
 	@@acceso: interno
	@@desc: Genera los BOTONES del Marco Transaccional
*/
	{
		if(trim($this->info_ci_me_etapa[$this->indice_etapas[$this->etapa_actual]]["ev_procesar"])==""){
			$disabled = " disabled ";
		}else{
			$disabled = "";
		}
		echo form::submit($this->submit,$this->submit_etiq,"abm-input", $disabled);
		if($this->info_ci['ev_cancelar']){
			echo "&nbsp;" . form::button("boton", $this->cancelar_etiq ,"onclick=\"document.location.href='".$this->solicitud->vinculador->generar_solicitud(null,null,array($this->flag_cancelar_operacion=>1),true)."';\"","abm-input");
		}
	}																		
	//-------------------------------------------------------------------------------
}
?>
