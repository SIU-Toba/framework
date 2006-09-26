<?
require_once("toba_ei_formulario.php");

/**
 * Un filtro presenta una grilla de campos similar al formulario, pero con el objetivo de reducir el conjunto de datos mostrados por otro objeto. 
 * @package Componentes
 * @subpackage Eis
 */
class toba_ei_filtro extends toba_ei_formulario
{
	protected $item_editor = '/admin/objetos_toba/editores/ei_filtro';
	
	protected function generar_envoltura_ef($ef, $editor=null)
	{
		$clase = 'ei-form-fila';
		$estilo_nodo = "";
		$id_ef = $this->elemento_formulario[$ef]->get_id_form();
		if (! $this->elemento_formulario[$ef]->esta_expandido()) {
			$clase .= ' ei-form-fila-oculta';
			$estilo_nodo = "display:none";
		}
		if ($this->elemento_formulario[$ef]->seleccionado()) {
			$clase .= ' ei-form-fila-filtrada';
		}				
		echo "<div class='$clase' style='$estilo_nodo' id='nodo_$id_ef'>\n";
		$this->generar_etiqueta_ef($ef);
		echo "<div id='cont_$id_ef' style='margin-left:{$this->ancho_etiqueta}'>\n";		
		echo $this->elemento_formulario[$ef]->get_input();
		echo "</div>";
		echo "</div>\n";
	}	
	
}
?>
