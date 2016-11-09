<?php 
//--------------------------------------------------------------------
class form_opciones extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__con_subclases__procesar = function(inicial) {
				if (this.ef('con_subclases').chequeado()) {
					this.ef('carpeta_subclases').mostrar();
				} else {
					this.ef('carpeta_subclases').ocultar();
				}
			}
			
			{$id_js}.evt__carpeta_subclases__validar = function() {
				if (this.ef('con_subclases').chequeado() &&
					this.ef('carpeta_subclases').valor().trim() == '') {
			 		this.ef('carpeta_subclases').set_error('Debe incluir un path');
			 		return false;
				}
				return true;
			}
		";
	}
}

?>