<?php
require_once('toba_ei.php');
require_once('nucleo/lib/toba_tab.php');

/**
* @package Objetos
* @subpackage Ei
*/
class toba_ei_pantalla extends toba_ei
{
	// Navegacion
	protected $lista_tabs = array();
	protected $dependencias;
	protected $nombre_formulario;					// Nombre del <form> del MT
	protected $submit;								// Boton de SUBMIT
	protected $id_en_controlador;

	function __construct($info_pantalla, $submit, $objeto_js)
	{
		parent::__construct($info_pantalla);
		$this->nombre_formulario = "formulario_toba" ;//Cargo el nombre del <form>
		$this->submit = $submit;
		$this->objeto_js = $objeto_js;
		$this->posicion_botonera = ($this->info_ci['posicion_botonera'] != '') ? $this->info_ci['posicion_botonera'] : 'abajo';
	}
	
	function set_controlador($controlador, $id_en_padre=null)
	{
		$this->controlador = $controlador;
		$this->id_en_controlador = $id_en_padre;
	}	

	function pre_configurar()
	{
		$this->cargar_lista_dep();
		$this->cargar_lista_tabs();
		$this->cargar_lista_eventos();
	}
	
	/**
	 * En la post_configuracion ya estan definidas las dependencias que participan
	 * Asi que es hora de pedir al controlador que construya los objetos, los inicialize y configure
	 */
	function post_configurar()
	{
		parent::post_configurar();
		$this->dependencias = array();
		foreach ($this->lista_dependencias as $id) {
			$this->dependencias[$id] = $this->controlador->dependencia($id);	
		}
	}
	
	function get_descripcion()
	{
		return trim($this->info_pantalla["descripcion"]);
	}
	
	function set_descripcion($descr)
	{
		$this->info_pantalla["descripcion"] = $descr;
	}

	//------------------------------------------------------
	//---------------		Dependencias    ----------------
	//------------------------------------------------------

	function agregar_dep($id_obj)
	{
		//--- Chequeo para evitar el bug #389				
		if ($this->controlador->existe_dependencia($id_obj)) {
			$this->lista_dependencias[] = $id_obj;
		} else {
			toba::logger()->error($this->get_txt(). 
					" Se quiere agregar la dependencia '$id_obj', pero esta no está definida en el CI");
		}
	}
	
	function eliminar_dep($id)
	{
		if (in_array($id, $this->lista_dependencias)) {
			array_borrar_valor($this->lista_dependencias, $id);
		} else {
			throw new toba_error($this->get_txt(). 
					" Se quiere eliminar la dependencia '$id', pero esta no está en la pantalla actual");
		}
	}
		
	/**
	 * Retorna los ID de las dependencias que se utilizan en esta pantalla
	 */
	function get_lista_dependencias()
	{
		return $this->lista_dependencias;
	}
	
	protected function cargar_lista_dep()
	{
		//Busco la definicion standard para la etapa
		$objetos = trim($this->info_pantalla["objetos"] );
		if ( $objetos != "" ) {
			$objetos = array_map("trim", explode(",", $objetos ) );
			foreach ($objetos as $id_obj) {
				$this->agregar_dep($id_obj);
			}
		}
	}

	//----------------------------------------------
	//---------------		TABS    ----------------
	//----------------------------------------------
	
	function eliminar_tab($id)
	{
		if($id == $this->id_en_controlador ) {
			throw new toba_error_def($this->get_txt(). 
					'No es posible eliminar el tab correspondiente a la pantalla que se esta mostrando');
		}
		if (isset($this->lista_tabs[$id])) {
			unset($this->lista_tabs[$id]);
		} else {
			throw new toba_error_def($this->get_txt(). 
					" Se quiere eliminar el tab '$id', pero esta no está en la pantalla actual");
		}
	}
	
	function get_lista_tabs()
	{
		return $this->lista_tabs;	
	}

	/**
	 * Carga la lista de botones que representan a las pestañas o tabs que se muestran en la pantalla actual
	 */
	protected function cargar_lista_tabs()
	{
		$this->lista_tabs = array();
		for($a = 0; $a<count($this->info_ci_me_pantalla);$a++)
		{
			$id = $this->info_ci_me_pantalla[$a]["identificador"];
			$datos['identificador'] = $id;
			$datos['etiqueta'] = $this->info_ci_me_pantalla[$a]["etiqueta"];
			$datos['ayuda'] = $this->info_ci_me_pantalla[$a]["tip"];
			$datos['imagen'] = $this->info_ci_me_pantalla[$a]["imagen"];
			$datos['imagen_recurso_origen'] = $this->info_ci_me_pantalla[$a]["imagen_recurso_origen"];
			$this->lista_tabs[$id] = new toba_tab($datos);
		}
	}	
	
	//---------------------------------------------
	//---------------	EVENTOS    ----------------
	//---------------------------------------------

	/**
	 * Carga la lista de eventos definidos desde el administrador 
	 * La redefinicion filtra solo aquellos utilizados en esta pantalla
	 * y agrega los tabs como eventos
	 */
	protected function cargar_lista_eventos()
	{
		//--- Filtra los eventos definidos por el usuario segun la asignacion a pantallas
		parent::cargar_lista_eventos();
		$ev_etapa = explode(',', $this->info_pantalla['eventos']);
		foreach (array_keys($this->eventos_usuario_utilizados) as $id) {
			if (! in_array($id, $ev_etapa)) {
				unset($this->eventos_usuario_utilizados[$id]);
			}
		}
		
		//-- Agrega los eventos internos relacionados con la navegacion tabs
		switch($this->info_ci['tipo_navegacion']) {
			case "tab_h":
			case "tab_v":
				foreach ($this->lista_tabs as $id => $tab) {
					$this->eventos += eventos::ci_cambiar_tab($id);
				}
				break;
			case "wizard":
				list($anterior, $siguiente) = array_elem_limitrofes(array_keys($this->lista_tabs),
																	$this->info_pantalla['identificador']);
				if ($anterior !== false) {
					$e = new toba_evento_usuario();
					$e->set_id('cambiar_tab__anterior');
					$e->set_etiqueta('< &Anterior');
					$this->eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
					$this->eventos_usuario_utilizados[ $e->get_id() ] = $e;		//Lista de utilizados
				}
				if ($siguiente !== false) {
					$e = new toba_evento_usuario();
					$e->set_id('cambiar_tab__siguiente');
					$e->set_etiqueta('&Siguiente >');
					$this->eventos_usuario[ $e->get_id() ] = $e;				//Lista de eventos
					$this->eventos_usuario_utilizados[ $e->get_id() ] = $e;		//Lista de utilizados
				}
				break;
		}		
	}

	//---------------------------------------------------------------
	//-------------------------- SALIDA HTML --------------------------
	//----------------------------------------------------------------
	
	function generar_html()
	{
		echo "\n<!-- ################################## Inicio CI ( ".$this->id[1]." ) ######################## -->\n\n";		
		//-->Listener de eventos
		if ( (count($this->eventos) > 0) || (count($this->eventos_usuario_utilizados) > 0) ) {
			echo toba_form::hidden($this->submit, '');
			echo toba_form::hidden($this->submit."__param", '');
		}
		$ancho = isset($this->info_ci["ancho"]) ? "style='width:{$this->info_ci["ancho"]};'" : '';
		echo "<table class='ei-base ci-base' $ancho id='{$this->objeto_js}_cont'><tr><td>\n";
		$this->barra_superior(null,true,"ci-barra-sup");
		$colapsado = (isset($this->colapsado) && $this->colapsado) ? "style='display:none'" : "";
		echo "<div $colapsado id='cuerpo_{$this->objeto_js}'>\n";
		$this->generar_html_cuerpo();
		echo "\n</div>";
		echo "</td></tr></table>";
		echo "\n<!-- ###################################  Fin CI  ( ".$this->id[1]." ) ######################## -->\n\n";
	}
	
	protected function generar_html_cuerpo()
	{	
		//--> Botonera
		$con_botonera = $this->hay_botones();
		if($con_botonera && ($this->posicion_botonera == "arriba" || $this->posicion_botonera == "ambos") ) {
			$this->generar_botones('ci-botonera');
		}
		//--> Cuerpo del CI
		$alto = isset($this->info_ci["alto"]) ? "style='_height:".$this->info_ci["alto"].";min-height:" . $this->info_ci["alto"] . "'" : "";
		echo "<div class='ci-cuerpo' $alto>\n";
		$this->generar_html_pantalla();
		echo "</div>\n";
		
		//--> Botonera
		if($con_botonera && ($this->posicion_botonera == "abajo" || $this->posicion_botonera == "ambos")) {
			$this->generar_botones('ci-botonera');
		}
		if ( $this->utilizar_impresion_html ) {
			$this->generar_utilidades_impresion_html();
		}
	}
	
	protected function generar_html_pantalla()
	{
		switch($this->info_ci['tipo_navegacion'])
		{
			case "tab_h":									//*** TABs horizontales
				echo "<table class='tabla-0' width='100%'>\n";
				//Tabs
				echo "<tr><td>";
				$this->generar_tabs_horizontales();
				echo "</td></tr>\n";
				//Interface de la etapa correspondiente
				echo "<tr><td class='ci-tabs-h-cont'>";
				$this->generar_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			case "tab_v": 									//*** TABs verticales
				echo "<table class='tabla-0' width='100%'>\n";
				echo "<tr><td class='ci-tabs-v-lista'>";
				$this->generar_tabs_verticales();
				echo "</td>";
				echo "<td class='ci-tabs-v-cont'>";
				$this->generar_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			case "wizard": 									//*** Wizard (secuencia estricta hacia adelante)
				echo "<table class='tabla-0'>\n";
				echo "<tr><td class='ci-wiz-toc'>";
				if ($this->info_ci['con_toc']) {
					$this->generar_toc_wizard();
				}
				echo "</td>";
				echo "<td class='ci-wiz-cont'>";
				$this->generar_html_pantalla_contenido();
				echo "</td></tr>\n";
				echo "</table>\n";
				break;				
			default:										//*** Sin mecanismo de navegacion
				$this->generar_html_pantalla_contenido();
		}
	}

	/**
	 * Grafica el contenido de la pantalla actual
	 */
	protected function generar_html_pantalla_contenido()
	{
		//--- Descripcion de la PANTALLA
		$descripcion = $this->get_descripcion();
		$es_wizard = $this->info_ci['tipo_navegacion'] == 'wizard';
		if($descripcion !="" || $es_wizard) {
			$imagen = toba_recurso::imagen_apl("info_chico.gif",true);
			$descripcion = toba_parser_ayuda::parsear($descripcion);
			if ($es_wizard) {
				$html = "<div class='ci-wiz-enc'><div class='ci-wiz-titulo'>";
				$html .= $this->info_pantalla["etiqueta"];
				$html .= "</div><div class='ci-wiz-descr'>$descripcion</div></div>";
				echo $html;
			} else {
				echo "<div class='ci-pant-desc'>$imagen&nbsp;$descripcion</div>\n";
			}
			echo "<hr>\n";
		}
/*		//--- Controla la existencia de una funcion que redeclare la generacion de una PANTALLA puntual
		$interface_especifica = "generar_html_contenido". apex_ei_separador . $this->etapa_gi;
		if(method_exists($this, $interface_especifica)){
			$this->$interface_especifica();
		}else{
			//--- Solicita el HTML de todas las dependencias que forman parte de la generacion de la interface*/
		$this->generar_html_dependencias();
//		}
	}
	
	/**
	 * Dispara la generación de html de los objetos contenidos en esta pantalla
	 * Para redefinir la generación de una pantalla puntual, hay que definir un método:
	 * 		generar_html_contenido__PANTALLA 
	 */	
	protected function generar_html_dependencias()
	{
		$existe_previo = 0;
		foreach($this->dependencias as $dep) {
			if($existe_previo){ //Separador
				echo "<hr>\n";
			}
			$dep->generar_html();	
			$existe_previo = 1;
		}
	}

	protected function generar_toc_wizard()
	{
		echo "<ol class='ci-wiz-toc-lista'>";
		$pasada = true;
		foreach ($this->lista_tabs as $id => $pantalla) {
			if ($pasada)
				$clase = 'ci-wiz-toc-pant-pasada';
			else
				$clase = 'ci-wiz-toc-pant-futuro';			
			if ($id == $this->id_en_controlador) {
				$clase = 'ci-wiz-toc-pant-actual';
				$pasada = false;
			}
			echo "<li class='$clase'>";
			echo $pantalla->get_etiqueta();
			echo "</li>";
		}		
		echo "</ol>";
	}
	

	protected function generar_tabs_horizontales()
	{
		$estilo = 'background: url("'.toba_recurso::imagen_apl('tabs/bg.gif').'") repeat-x bottom;';
		echo "<div style='$estilo' class='ci-tabs-h-lista'><ul>\n";
		foreach( $this->lista_tabs as $id => $tab ) {
			$editor = '';
			if (toba_editor::modo_prueba()) {
				$editor = toba_editor::get_vinculo_pantalla($this->id, $this->info['clase_editor_item'], $id)."\n";
			}			
			if ($this->id_en_controlador == $id) {
  				$estilo_li = 'background:url("'.toba_recurso::imagen_apl('tabs/left_on.gif').'") no-repeat left top;';
  				$estilo_a = 'background:url("'.toba_recurso::imagen_apl('tabs/right_on.gif').'") no-repeat right top;';
				echo "<li class='ci-tabs-h-solapa-sel' style='$estilo_li'>$editor";
				echo $tab->get_html($this->submit, $this->objeto_js, false, $estilo);
				echo "</li>";
			} else {
  				$estilo_li = 'background:url("'.toba_recurso::imagen_apl('tabs/left.gif').'") no-repeat left top;';
  				$estilo_a = 'background:url("'.toba_recurso::imagen_apl('tabs/right.gif').'") no-repeat right top;';
				echo "<li  class='ci-tabs-h-solapa' style='$estilo_li'>$editor";
				echo $tab->get_html($this->submit, $this->objeto_js, true, $estilo);
				echo "</li>";
			}
		}
		echo "</ul></div>";
	}

	protected function generar_tabs_verticales()
	{
		echo "<div  class='ci-tabs-v-solapa' style='height:20px'> </div>";
		foreach( $this->lista_tabs as $id => $tab ) {
			$editor = '';
			if (toba_editor::modo_prueba()) {
				$editor = toba_editor::get_vinculo_pantalla($this->id, $this->info['clase_editor_item'], $id)."\n";
			}
			if ( $this->id_en_controlador == $id ) {
				echo "<div class='ci-tabs-v-solapa-sel'><div class='ci-tabs-v-boton-sel'>$editor ";
				echo $tab->get_html($this->submit, $this->objeto_js, false);
				echo "</div>";
			} else {
				echo "<div class='ci-tabs-v-solapa'>$editor ";
				echo $tab->get_html($this->submit, $this->objeto_js);
				echo "</div>";
			}
		}
		echo "<div class='ci-tabs-v-solapa' style='height:99%;'></div>";
	}
	
	protected function generar_utilidades_impresion_html()
	{
		$id_frame = "{$this->submit}_print";
		echo "<iframe style='position:absolute;width: 0px; height: 0px; border-style: none;' "
			."name='$id_frame' id='$id_frame' src='about:blank'></iframe>";
		echo toba_js::abrir();
		echo "
		function imprimir_html( url, forzar_popup )
		{
			var usar_popup = (forzar_popup) ? true : false ;
			var f = window.frames.$id_frame.document;
			if ( f && !usar_popup ) {
			    var html = '';
			    html += '<html>';
			    html += '<body onload=\"parent.printFrame(window.frames.urlToPrint);\">';
			    html += '<iframe name=\"urlToPrint\" src=\"' + url + '\"><\/iframe>';
			    html += '<\/body><\/html>';
			    f.open();
			    f.write(html);
			    f.close();
			} else {
				solicitar_item_popup( url, 650, 500, 'yes', 'yes');
			}
		}
		function printFrame (frame) {
		  if (frame.print) {
		    frame.focus();
		    frame.print();
		  }
		}
		";
		echo toba_js::cerrar();
	}

	//-------------------------------------------------------------------------------
	//---- JAVASCRIPT ---------------------------------------------------------------
	//-------------------------------------------------------------------------------

	/**
	 * Retorna los consumos javascript requerido por este objeto y sus dependencias
	 * @return array
	 */
	function get_consumo_javascript()
	{
		$consumo_js = parent::get_consumo_javascript();
		$consumo_js[] = 'componentes/ci';
		foreach($this->dependencias as $dep) {
			$temp = $dep->get_consumo_javascript();
			if (isset($temp)) {
				$consumo_js = array_merge($consumo_js, $temp);
			}
		}
		return $consumo_js;
	}

	function crear_objeto_js()
	{
		$identado = toba_js::instancia()->identado();	
		//Crea le objeto CI
		echo $identado."window.{$this->objeto_js} = new ci('{$this->objeto_js}', '{$this->nombre_formulario}', '{$this->submit}', '{$this->id_en_controlador}');\n";

		//Crea los objetos hijos
		$objetos = array();
		toba_js::instancia()->identar(1);		
		foreach($this->dependencias as $id => $dep)	{
			$objetos[$id] = $dep->generar_js();
		}
		$identado = toba_js::instancia()->identar(-1);		
		//Agrega a los objetos hijos
		//ATENCION: Esto no permite tener el mismo formulario instanciado dos veces
		echo "\n";
		foreach ($objetos as $id => $objeto) {
			echo $identado."{$this->objeto_js}.agregar_objeto($objeto, '$id');\n";
		}
	}

	function generar_js()
	{
		$identado = toba_js::instancia()->identado();
		echo "\n$identado//---------------- CREANDO OBJETO {$this->objeto_js} --------------  \n";
		$this->crear_objeto_js();
		$this->controlador->extender_objeto_js();
		$this->extender_objeto_js();
		echo "\n";
		$this->iniciar_objeto_js();
		echo "$identado//-----------------------------------------------------------------  \n";		
		return $this->objeto_js;
	}

	//---------------------------------------------------------------
	//------------------------ SALIDA Impresion ---------------------
	//---------------------------------------------------------------
	
	function vista_impresion( toba_impresion $salida )
	{
		$salida->titulo( $this->get_titulo() );
		foreach($this->dependencias as $dep) {
			$dep->vista_impresion( $salida );
		}
	}
	
}

?>
