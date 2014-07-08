<?php
/**
 * Combo editable, es una caja de texto y un div que se deplega con las opciones
 * Al presionar enter utiliza el metodo o query declarado para buscar el texto ingresado y traer las opciones coincidentes
 * @package Componentes
 * @subpackage Efs
 * @jsdoc ef_combo_editable ef_combo_editable 
 */
class toba_ef_combo_editable extends toba_ef_seleccion
{	
	protected $descripcion_estado;
	protected $solo_lectura = false;
	protected $tamano = 200;
	protected $habilitar_modo_filtrado = false;
	protected $solo_permitir_selecciones = true;
	
	static function get_lista_parametros_carga()
	{
		$parametros = toba_ef_seleccion::get_lista_parametros_carga();   
		//$parametros[] = 'combo_editable_carga_tit';	
		$parametros[] = 'popup_carga_desc_metodo';
		$parametros[] = 'popup_carga_desc_clase';
		$parametros[] = 'popup_carga_desc_include';    	
		return $parametros;
	}
	
	static function get_lista_parametros()
	{
		$parametros[] = 'punto_montaje';
		$parametros[] = 'edit_tamano';
		$parametros[] = 'popup_carga_desc_metodo';
		$parametros[] = 'popup_carga_desc_clase';
		$parametros[] = 'popup_carga_desc_include';
		return $parametros;
	}
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		//Tamaño del editable
		if (isset($parametros['edit_tamano'])) {
			$this->tamano = $parametros['edit_tamano'];
			unset($parametros['edit_tamano']);	
		}
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}	
	
	function carga_depende_de_estado()
	{
		return true;	
	}	
	
	function get_input()
	{
		//-- Si tiene un estado, ponerlo como única opción
		$this->opciones = array();
		if (isset($this->descripcion_estado)) {
			$this->opciones[$this->estado] = $this->descripcion_estado;
		}
		$html = "";

		//El estado que puede contener muchos datos debe ir en un unico string
		$estado = $this->get_estado_para_input();
		if ($this->es_solo_lectura()) {
			$html .= toba_form::select("",$estado, $this->opciones, $this->clase_css, "disabled");
			$html .= toba_form::hidden($this->id_form, $estado);
			return $html;
		} else {
			$tab = $this->padre->get_tab_index();
			$extra = " tabindex='$tab'";
			$js = '';

			if ($this->cuando_cambia_valor != '') {
				$js = "onchange=\"{$this->get_cuando_cambia_valor()}\"";
			}

			$html .= toba_form::select($this->id_form, $estado ,$this->opciones, 'ef-combo', $js . $this->input_extra.$extra, $this->categorias);
		}
		$html .= $this->get_html_iconos_utilerias();
		return $html;
	}	

	function set_opciones($descripcion, $maestros_cargados=true, $tiene_maestros=false)
	{
		//--- No se actualiza $this->opciones_cargadas porque los combos_editables requieren
		//--- que siempre se refresquen sus opciones porque se basan en su estado
		//--- En cambio se cambia su descripcion
		$this->descripcion_estado = $descripcion;
		if (! $maestros_cargados) {
			$this->input_extra = 'disabled';
		}
	}

	function get_descripcion_estado($tipo_salida)
	{
		if (isset($this->descripcion_estado)) {
			$valor = $this->descripcion_estado;
		} else {
			$valor = null;
		}
		switch ($tipo_salida) {
			case 'html':
			case 'impresion_html':
				return "<div class='{$this->clase_css}'>$valor</div>";
			case 'pdf':
				return $valor;
			case 'excel':		
				return array($valor, null);
		}
	}

	function set_modo_filtrado($estado)
	{
		$this->habilitar_modo_filtrado = $estado;
	}
	
	function set_modo_solo_selecciones($estado)
	{
		$this->solo_permitir_selecciones = $estado;
	}
	
	function cargar_estado_post()
	{
		if (! isset($_POST[$this->id_form])) {
			return false;
		}
		$estado = $_POST[$this->id_form];
		if ($estado == apex_ef_no_seteado || $estado === '') {
			$estado = null;	
		}
		$this->set_estado($estado);
		return true;
	}	
	
	protected function parametros_js()
	{
		$parametros = parent::parametros_js().', '.$this->tamano;
		if (!$this->es_solo_lectura()) {
			$parametros .= $this->habilitar_modo_filtrado?', true':', false';
			$parametros .= $this->solo_permitir_selecciones?', true':', false';
		}
		return $parametros;
	}	
	
	function crear_objeto_js()
	{
		$mantiene_estado_js = toba_js::bool($this->mantiene_estado_cascada);
		if (! $this->es_solo_lectura()) {
			return "new ef_combo_editable({$this->parametros_js()}, $mantiene_estado_js)";
		} else {
			//--En el caso que sea solo-lectura en el server, se comporta como un combo normal en js
			$parametros = parent::parametros_js();
			return "new ef_combo($parametros, $mantiene_estado_js)";
		}
	}

	function get_consumo_javascript()
	{
		$consumos = array('efs/ef', 'efs/ef_combo', 'efs/ef_combo_editable', 'dhtmlxCombo/codebase/dhtmlxcombo', 'dhtmlxCombo/codebase/dhtmlxcommon');
		return $consumos;
	}

}

//########################################################################################################
//########################################################################################################

?>
