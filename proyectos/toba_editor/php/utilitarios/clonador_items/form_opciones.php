<?php 
//--------------------------------------------------------------------
class form_opciones extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__con_subclases__procesar = function(inicial) {
				if (this.ef('con_subclases').chequeado()) {
					this.ef('carpeta_subclases').mostrar();
				} else {
					this.ef('carpeta_subclases').ocultar();
				}
			}
			
			{$this->objeto_js}.evt__carpeta_subclases__validar = function() {
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