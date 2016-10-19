<?php 
class ei_form_filtro_usuarios extends toba_ei_formulario
{
	
	function generar_layout()
	{
		foreach ($this->_lista_ef_post as $ef) {
			if ($ef == 'pertenencia') {
				$escapador = toba::escaper();
				$id_ef = $this->_elemento_formulario[$ef]->get_id_form();
				echo "<div id='". $escapador->escapeHtmlAttr('nodo_'. $id_ef)."' class='ei-form-fila' style=''>";
				$this->generar_etiqueta_ef('pertenencia');
				echo "<div id='". $escapador->escapeHtmlAttr('cont_'.$id_ef)."' style='margin-left:". $escapador->escapeHtmlAttr($this->_ancho_etiqueta).";'>";
				$this->generar_input_ef('pertenencia');
				$this->generar_input_ef('proyecto');
				echo '</div>';
				echo '</div>';
			} else {
				if ($ef <> 'proyecto') {
					$this->generar_html_ef($ef);	
				}				
			}
		}
	}

	function extender_objeto_js()
	{
		echo  toba::escaper()->escapeJs($this->objeto_js) .
		 ".evt__pertenencia__procesar = function(es_inicial)
		{
			var opcion = this.ef('pertenencia').valor();
			if (opcion == 'T' || opcion == 'S') {
				$$('ef_form_2189_filtroproyecto').style.display = 'none';
			}else{
				$$('ef_form_2189_filtroproyecto').style.display = '';
			}
		}
		";
	}
}

?>