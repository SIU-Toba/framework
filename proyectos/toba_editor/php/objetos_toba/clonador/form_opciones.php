<?php

class form_opciones extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			{$id_js}.evt__identificador__validar = function() {
				if (this.ef('con_destino').chequeado() && 
					(this.ef('tipo').valor() == 'toba_ci' || 
						this.ef('tipo').valor() == 'toba_datos_relacion')) {	
					if (this.ef('identificador').valor() == '') {
						this.ef('identificador').set_error('El identificador es obligatorio');
						return false
					}
				}
				return true;
			}
			
			
			{$id_js}.evt__con_destino__procesar = function(inicial) {
				if (this.ef('con_destino').chequeado()) {
					this.ef('tipo').mostrar();
					this.ef('objeto_id').mostrar();		
				} else { 
					this.ef('tipo').ocultar();
					this.ef('objeto_id').ocultar();
				}
				this.evt__tipo__procesar(inicial);
			}
			
			{$id_js}.evt__tipo__procesar = function(inicial) {
				this.ef('ci_pantalla').ocultar();
				this.ef('identificador').ocultar();
				this.ef('min_filas').ocultar();
				this.ef('max_filas').ocultar();

				if (this.ef('con_destino').chequeado()) {
					switch (this.ef('tipo').valor()) {
						case 'toba_ci':
							this.ef('ci_pantalla').mostrar();
							this.ef('identificador').mostrar();
							break;
						case 'toba_datos_relacion':
							this.ef('identificador').mostrar();
							this.ef('min_filas').mostrar();
							this.ef('max_filas').mostrar();
							break;
					} 
				}
			}
			
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