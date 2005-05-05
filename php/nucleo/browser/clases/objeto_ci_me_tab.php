<?php
require_once("objeto_ci_me.php");

class objeto_ci_me_tab extends objeto_ci_me
{
/*
	Falta customizar el display de los TABS (arriba, izquierda, derecha, etc...)
	Falta generar secuencias estrictas 
	Entrada de JS para inhabilitar TABS desde el cliente
*/
	protected $lista_tabs;
	protected $submit_tab;
	protected $display;

	function __construct($id)
	{
		parent::__construct($id);
		$this->display = "arriba"; //arriba, izquierda
	}

	function destruir()
	{
		if( isset($this->lista_tabs) ){
			$this->memoria['tabs'] = array_keys($this->lista_tabs);
			parent::destruir();
		}
	}
	
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   PROCESAMIENTO de EVENTOS   --------------------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	
	function get_etapa_actual()
	{
		if (isset($_POST[$this->submit])) {
			$submit = $_POST[$this->submit];
			$tab = (strpos($submit, 'cambiar_tab_') !== false) ? str_replace('cambiar_tab_', '', $submit) : false;
		}
		else
			$tab = false;
		
		if($tab !== false) { //Pidio cambiar de tab
			if(in_array($tab, $this->memoria['tabs'])){
				//El usuario selecciono un tab
				return $tab;
			}else{
				$this->log->error($this->get_txt() . "Se solicito un TAB inexistente.");			
				//Error, voy a etapa inicial
				return $this->get_etapa_inicial();
			}
		}elseif(isset( $this->memoria['etapa_gi'] )){
			//El post fue generado por otro componente
			return $this->memoria['etapa_gi'];
		}else{
			//Etapa inicial
			return $this->get_etapa_inicial();
		}
	}

	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------
	//-----------------   Generacion de la INTERFACE GRAFICA   ----------------------
	//-------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------

	function obtener_html_contenido()
	{
		//-[2]- Genero la SALIDA
		echo "<table class='tabla-0' width='100%'>\n";
		//Tabs
		echo "<tr><td class='celda-vacia'>";
		$this->obtener_barra_navegacion();
		echo "</td></tr>\n";
		//Interface de la etapa correspondiente
		echo "<tr><td class='tabs-contenedor' height='100%'>";
		parent::obtener_html_contenido();
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function obtener_barra_navegacion()
	{
		$this->lista_tabs = $this->get_lista_tabs();
		echo "<table width='100%' class='tabla-0'>\n";
		echo "<tr>";
		//echo "<td width='1'  class='tabs-solapa-hueco'>".gif_nulo(6,1)."</td>";
		foreach( $this->lista_tabs as $id => $tab )
		{
			$tip = $tab["tip"];
			$clase = 'tabs-boton';
			$tab_order = 0;
			$acceso = tecla_acceso( $tab["etiqueta"] );
			$html = $acceso[0]; //Falta concatenar la imagen
			if(isset($tab['imagen'])) $html = $tab['imagen'] . "&nbsp;&nbsp;" . $html;
			$tecla = $acceso[1];
			$js = "onclick=\"{$this->objeto_js}.set_evento(new evento_ei('cambiar_tab_$id', true, ''));\"";
			if( $this->etapa_gi == $id ){
				//TAB actual
				echo "<td class='tabs-solapa-sel'>";
				echo form::button_html( "actual", $html, '', $tab_order, null, '', 'button', '', "tabs-boton-sel");
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}else{
				echo "<td class='tabs-solapa'>";
				echo form::button_html( $this->submit.$id, $html, $js, $tab_order, $tecla, $tip, 'button', '', $clase);
				echo "</td>\n";
				echo "<td width='1' class='tabs-solapa-hueco'>".gif_nulo(4,1)."</td>\n";
			}
		}
		echo "<td width='90%'  class='tabs-solapa-hueco'>".gif_nulo()."</td>\n";
		echo "</tr>";
		echo "</table>\n";
	}
	//-------------------------------------------------------------------------------

	function get_lista_tabs()
	{
		for($a = 0; $a<count($this->info_ci_me_etapa);$a++)
		{
			$id = $this->info_ci_me_etapa[$a]["posicion"];
			$tab[$id]['etiqueta'] = $this->info_ci_me_etapa[$a]["etiqueta"];
			$tab[$id]['tip'] = $this->info_ci_me_etapa[$a]["descripcion"];
			//$tab[$id]['imagen'] = recurso::imagen_apl('doc.gif',true);
		}
		return $tab;
	}
	//-------------------------------------------------------------------------------
	
	function get_lista_eventos()
	/*
		Cada tab califica como un evento que no se muestra en la botonera estandar
	*/
	{
		$eventos = parent::get_lista_eventos();
		foreach ($this->get_lista_tabs() as $id => $tab) {
			$eventos['cambiar_tab_'.$id]['validar'] = "true";
			$eventos['cambiar_tab_'.$id]['en_botonera'] = false;			
		}
		return $eventos;
	}
	//-------------------------------------------------------------------------------	
}
?>	