<?php

class eiform_cuadro_prop_cols extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		//--- Se insertan los css de cada estilo para hacer la visualización
		$estilos = rs_convertir_asociativo(toba_info_editores::get_lista_estilos_columnas(), array('columna_estilo'), 'css');
		echo "var editor_col_css=".toba_js::arreglo($estilos, true);
		echo "
			{$this->objeto_js}.evt__usar_vinculo__procesar = function(inicial) {

				var se_muestra = (this.ef('usar_vinculo').valor() == '1');
				this.ef('evento_asociado').mostrar(se_muestra, true);
				this.ef('vinculo_indice').mostrar(se_muestra, true);
			}
					
			{$this->objeto_js}.evt__estilo__procesar = function(inicial) {
				var estado = this.ef('estilo').get_estado();	
				var input = this.ef('prueba_estilo').input();
				input.className = editor_col_css[estado] + ' columna-preview';
			}			
		";
	}
}
?>