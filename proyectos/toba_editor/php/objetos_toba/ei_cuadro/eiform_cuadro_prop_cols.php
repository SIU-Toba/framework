<?php

class eiform_cuadro_prop_cols extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		//--- Se insertan los css de cada estilo para hacer la visualización
		$estilos = rs_convertir_asociativo(toba_info_editores::get_lista_estilos_columnas(), array('columna_estilo'), 'css');
		echo 'var editor_col_css='.toba_js::arreglo($estilos, true);
		echo "
			{$id_js}.evt__usar_vinculo__procesar = function(inicial) 
			{
				var se_muestra = (this.ef('usar_vinculo').valor() == '1');
				this.ef('evento_asociado').mostrar(se_muestra, true);
				this.ef('vinculo_indice').mostrar(se_muestra, true);
			}
					
			{$id_js}.es_estilo_manual = function()
			{
				var estado = this.ef('estilo_precarga').get_estado();					
				return (estado == apex_ef_no_seteado);
			}

			{$id_js}.evt__estilo_precarga__procesar = function(inicial) 
			{					
				if (this.es_estilo_manual()) {
					this.ef('estilo_editable').mostrar(true);
				} else {
					this.ef('estilo_editable').mostrar(false, true);		//Reseteo el valor del editable cuando lo oculto
					var estado = this.ef('estilo_precarga').get_estado();
					var input = this.ef('prueba_estilo').input();							//Modifico el preview
					input.className = editor_col_css[estado] + ' columna-preview';	
				}
			}
			
			{$id_js}.evt__estilo_editable__procesar = function(inicial)
			{			
				if (this.es_estilo_manual()) {
					var estilo = this.ef('estilo_editable').get_estado();
					var input = this.ef('prueba_estilo').input();
					input.className = estilo + ' columna-preview';
				}
			}
			

		";
	}
}
?>
