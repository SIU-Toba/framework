<?php
require_once("objeto_ci.php");

class objeto_ci_me extends objeto_ci
{
/*
	- Considerar el nombre del metodo:  get_etapa_actual
	- Le falta un subtitulo que cambie con la etapa
	- ATENCION: controlar si el metodo de memorizacion de la etapa es el adecuado 
		(no puede afectar en el caso de los multietapa en multietapa)
	- A los eventos por defecto les falta la activacion GENERAL
	- ASERCION para ver si la etapa este definida y sea correcta
*/

	var $indice_etapas;
	var $etapa_gi;			// Etapa a utilizar para generar la interface

	function __construct($id)
	{
		parent::__construct($id);
		//Indice de etapas
		for($a = 0; $a<count($this->info_ci_me_etapa);$a++){
			$this->indice_etapas[ $this->info_ci_me_etapa[$a]["posicion"] ] = $a;
		}
		//Lo que sigue solo sirve para el request inicial, en los demas casos es rescrito
		// por "definir_etapa_gi_pre_eventos" o "definir_etapa_gi_post_eventos"
		$this->set_etapa_gi( $this->get_etapa_inicial() );
	}
	//-------------------------------------------------------------------------------

	function destruir()
	{
		$this->memoria['etapa_gi'] = $this->etapa_gi;
		parent::destruir();
	}
	//-------------------------------------------------------------------------------

	function obtener_definicion_db()
	{
		$sql = parent::obtener_definicion_db();
		//-- CI - Multiples etapas --------------
		$sql["info_ci_me_etapa"]["sql"] = "SELECT	posicion			  	as posicion,
													etiqueta			  	as etiqueta,
													descripcion			  	as descripcion,
													objetos				  	as objetos,
													ev_procesar		   		as ev_procesar,
													ev_cancelar				as ev_cancelar
										FROM	apex_objeto_mt_me_etapa
										WHERE	objeto_mt_me_proyecto='".$this->id[0]."'
										AND	objeto_mt_me = '".$this->id[1]."'
										ORDER	BY	posicion;";
		$sql["info_ci_me_etapa"]["tipo"]="x";
		$sql["info_ci_me_etapa"]["estricto"]="1";
		return $sql;
	}

	//--  Primitivas de manejo de etapas  --------------------------------------------------

	function get_etapa_inicial()
	{
		return $this->info_ci_me_etapa[0]["posicion"];
	}
	
	function get_etapa_actual()
	//Esta funcion PROPONE la etapa ACTUAL (la etapa a la que se deberia ingresar en este request)
	// La etapa DEBE ser una etapa VALIDA!
	{
		$this->log->warning( $this->get_txt() . "Para establecer un esquema de navegacion es necesario definir el metodo 'get_etapa_actual'");
		return $this->get_etapa_inicial();		
	}	

	function set_etapa_gi($etapa)
	{
		$this->etapa_gi	= $etapa;
	}

	function get_etapa_gi()
	{
		return $this->etapa_gi;	
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//---------------------  PROCESAMIENTO de ETAPAS  -------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function disparar_eventos()
	{
		$this->definir_etapa_gi_pre_eventos();
		parent::disparar_eventos(); //Si se dispara una excepcion, el codigo que sigue no se ejecuta
		$this->definir_etapa_gi_post_eventos();
	}
	//-------------------------------------------------------------------------------

	function definir_etapa_gi_pre_eventos()
	//Define la etapa de Generacion de Interface del request ANTERIOR
	{
		$this->log->debug( $this->get_txt() . "[ definir_etapa_gi_pre_eventos ]");
		if( isset($this->memoria['etapa_gi']) ){
			// Habia una etapa anterior
			$this->set_etapa_gi( $this->memoria['etapa_gi'] );
			// 
		}else{
			// Request inicial
			// Esto no deberia pasar nunca, porque en el request inicial no se disparan los eventos
			// porque el CI no se encuentra entre las dependencias previas
			$this->set_etapa_gi( $this->get_etapa_inicial() );
		}
		$this->log->debug( $this->get_txt() . "etapa_gi_PRE_eventos: {$this->etapa_gi}");
	}
	//-------------------------------------------------------------------------------

	function definir_etapa_gi_post_eventos()
	//Define la etapa de Generacion de Interface correspondiente al procesamiento del evento ACTUAL
	//ATENCION: esto se esta ejecutando despues de los eventos propios... 
	//				puede traer problemas de ejecucion de eventos antes de validar la salida de etapas
	{
		$this->log->debug( $this->get_txt() . "[ definir_etapa_gi_post_eventos ]");
		// -[ 1 ]-  Controlo que se pueda salir de la etapa anterior
		// Esto no lo tengo que subir al metodo anterior?
		if( isset($this->memoria['etapa_gi']) ){
			// Habia una etapa anterior
			$evento_salida = apex_ci_evento . apex_ci_separador . "salida" . apex_ci_separador . $this->memoria['etapa_gi'];
			//Evento SALIDA
			if(method_exists($this, $evento_salida)){
				$this->$evento_salida();
			}
		}	
		// -[ 2 ]-  Controlo que se pueda ingresar a la etapa propuesta como ACTUAL
		$etapa_actual = $this->get_etapa_actual();
		$evento_entrada = apex_ci_evento . apex_ci_separador . "entrada" . apex_ci_separador . $etapa_actual;
		if(method_exists($this, $evento_entrada)){
			$this->$evento_entrada();
		}
		// -[ 3 ]-  Seteo la etapa PROPUESTA
		$this->set_etapa_gi($etapa_actual);
		$this->log->debug( $this->get_txt() . "etapa_gi_POST_eventos: {$this->etapa_gi}");
	}
	//-------------------------------------------------------------------------------

	function evt__post_recuperar_interaccion()
	//Despues de recuperar la interaccion con el usuario
	{
		/*	En este caso la validacion global "evt__validar_datos" no tiene sentido porque
			puede referirse a elementos que se encuentres en otra etapa distinta.
			Rutear a una especifica [evt__validar_datos__"numero"] tampoco porque se puede
			usar una ventana parecida con las [evt__salida__"numero"]. 
			Entonces solo desactivo	el funcionamiento del padre
		*/ 
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//--------   Generacion de la INTERFACE GRAFICA  ( ETAPA ACTUAL ) ---------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html_contenido()
	{
		if(trim($this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["descripcion"])!=""){
			$descripcion = 	$this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["descripcion"];
			$imagen = recurso::imagen_apl("info_chico.gif",true);
			echo "<div class='txt-info'>$imagen&nbsp;$descripcion</div>\n";
			echo "<hr>\n";
		}
		$interface_especifica = "obtener_html_contenido". apex_ci_separador . $this->etapa_gi;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			parent::obtener_html_contenido();
		}
	}
	//-------------------------------------------------------------------------------

	function get_lista_ei()
	{
		//Existe una definicion especifica para esta etapa?
		$metodo_especifico = "get_lista_ei" . apex_ci_separador . $this->etapa_gi;
		if(method_exists($this, $metodo_especifico)){
			return $this->$metodo_especifico();	
		}		
		//Busco la definicion standard para la etapa
		$objetos = trim( $this->info_ci_me_etapa[ $this->indice_etapas[ $this->etapa_gi ] ]["objetos"] );
		if( $objetos != "" ){
			return array_map("trim", explode(",", $objetos ) );
		}else{
			return array();
		}
	}
	//-------------------------------------------------------------------------------

	function get_lista_eventos()
	{
		$eventos = array();
		//Evento PROCESAR
		if($this->info_ci_me_etapa[ $this->indice_etapas[$this->etapa_gi] ]['ev_procesar'])
		{
			if($this->info_ci['ev_procesar_etiq']){
				$eventos['procesar']['etiqueta'] = $this->info_ci['ev_procesar_etiq'];
			}else{
				$eventos['procesar']['etiqueta'] = "Proce&sar";
			}
		}
		//Evento CANCELAR
		if($this->info_ci_me_etapa[ $this->indice_etapas[$this->etapa_gi] ]['ev_cancelar'])
		{
			//$eventos['cancelar']['confirm'] = "Esta seguro que desea cancelar?";
			if($this->info_ci['ev_cancelar_etiq']){
				$eventos['cancelar']['etiqueta'] = $this->info_ci['ev_cancelar_etiq'];
			}else{
				$eventos['cancelar']['etiqueta'] = "&Cancelar";
			}
		}
		return $eventos;
	}
	//-------------------------------------------------------------------------------
}
?>