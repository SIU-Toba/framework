<?php

class eiform_cuadro_prop_cortes extends toba_ei_formulario
{
	function extender_objeto_js() 
	{
		echo "
		{$this->objeto_js}.evt__cc_modo__procesar = function (inicial) {
			var flag;
			flag = this.ef('cc_modo').valor();
			if( flag == 'a' ){
				this.ef('cc_modo_a_colap').mostrar();
				this.ef('cc_modo_a_totcol').mostrar();
				this.ef('cc_modo_a_totcua').mostrar();
			}else{
				this.ef('cc_modo_a_colap').ocultar();
				this.ef('cc_modo_a_totcol').ocultar();
				this.ef('cc_modo_a_totcua').ocultar();
			}
		}
		";
	}
}
?>


