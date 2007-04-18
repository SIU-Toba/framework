<?php
require_once('seleccion_imagenes.php');

class form_prop_basicas extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__menu__procesar = function() {
				if (this.ef('menu').chequeado())
					this.ef('orden').mostrar();
				else
					this.ef('orden').ocultar();
			}
			
			{$this->objeto_js}.evt__zona__procesar = function() {
				if (this.ef('zona').valor() != apex_ef_no_seteado) {
					this.ef('zona_listar').mostrar();
				} else {
					this.ef('zona_listar').ocultar();
				}
				this.evt__zona_listar__procesar();
			}
			
			{$this->objeto_js}.evt__zona_listar__procesar = function() {
				if (this.ef('zona_listar').chequeado()) {
					this.ef('zona_orden').mostrar();
				} else {
					this.ef('zona_orden').ocultar();				
				}
			}
		";
		seleccion_imagenes::generar_js($this->objeto_js);
	}
	
	function generar_input_ef($ef)
	{
		if ($ef == 'imagen') {
			echo "<div class='editor-imagen-preview'>";
			$this->generar_input_ef('imagen_recurso_origen');	
		}
		parent::generar_input_ef($ef);
		if ($ef == 'imagen') {
			$origen = $this->ef('imagen_recurso_origen')->get_estado();
			$img = $this->ef($ef)->get_estado();
			seleccion_imagenes::generar_input_ef($origen, $img, $this->objeto_js);
			echo "</div>";
		} 
	}
	
	function generar_html_ef($ef)
	{
		if ($ef != 'imagen_recurso_origen') {
			parent::generar_html_ef($ef);
		}	
	}
}

?>