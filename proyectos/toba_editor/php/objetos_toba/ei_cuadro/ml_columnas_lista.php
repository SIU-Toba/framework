<?php 
require_once('objetos_toba/eiform_abm_detalle.php');

class ml_columnas_lista extends eiform_abm_detalle
{
	/*function generar_layout()
	{
		//--- Se insertan los css de cada estilo para hacer la visualización
		$estilos = rs_convertir_asociativo(toba_info_editores::get_lista_estilos_columnas(), array('columna_estilo'), 'css');
		echo toba_js::abrir();
		echo 'var editor_col_css='.toba_js::arreglo($estilos, true);
		echo toba_js::cerrar();
				
		parent::generar_layout();
	}*/
}

?>