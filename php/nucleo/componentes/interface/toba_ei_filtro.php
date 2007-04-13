<?php
/**
 * Un filtro presenta una grilla de campos similar al formulario, pero con el objetivo de reducir el conjunto de datos mostrados por otro objeto. 
 * @package Componentes
 * @subpackage Eis
 * @jsdoc ei_formulario ei_formulario
 * @wiki Referencia/Objetos/ei_filtro
 */
class toba_ei_filtro extends toba_ei_formulario
{
	protected $_item_editor = '/admin/objetos_toba/editores/ei_filtro';
	
	/**
	 * Genera la etiqueta y el componente HTML de un ef
	 * @param string $ef Identificador del ef
	 */
	protected function generar_html_ef($ef)
	{
		$clase = 'ei-form-fila';
		$estilo_nodo = "";
		$id_ef = $this->_elemento_formulario[$ef]->get_id_form();
		if (! $this->_elemento_formulario[$ef]->esta_expandido()) {
			$clase .= ' ei-form-fila-oculta';
			$estilo_nodo = "display:none";
		}
		if ($this->_elemento_formulario[$ef]->seleccionado()) {
			$clase .= ' ei-form-fila-filtrada';
		}		
		if ($this->_elemento_formulario[$ef]->tiene_etiqueta()) {
			echo "<div class='$clase' style='$estilo_nodo' id='nodo_$id_ef'>\n";
			$this->generar_etiqueta_ef($ef);
			//--- El margin-left de 0 y el heigth de 1% es para evitar el 'bug de los 3px'  del IE
			echo "<div id='cont_$id_ef' style='margin-left:{$this->_ancho_etiqueta};_margin-left:0;_height:1%;'>\n";
			echo $this->_elemento_formulario[$ef]->get_input();
			echo "</div>";
			echo "</div>\n";
		} else {		
			echo $this->_elemento_formulario[$ef]->get_input();
		}
	}
	
	
}
?>
