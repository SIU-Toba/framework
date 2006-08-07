<?php 
//--------------------------------------------------------------------
class eiform_proyecto_conflogin extends objeto_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
		{$this->objeto_js}.evt__requiere_validacion__procesar = function () {
			if( this.ef('requiere_validacion').chequeado() ){
				this.ef('validacion_intentos').mostrar();
				this.ef('validacion_intentos_min').mostrar();
				this.ef('sesion_tiempo_no_interac_min').mostrar();
				this.ef('sesion_tiempo_maximo_min').mostrar();
				this.ef('validacion_debug').mostrar();
				this.ef('carpeta_inicio_sesion').mostrar();
				this.ef('carpeta_pre_sesion').mostrar();
				this.ef('item_inicio_sesion').mostrar();
				this.ef('item_pre_sesion').mostrar();
			}else{
				this.ef('validacion_intentos').ocultar();
				this.ef('validacion_intentos_min').ocultar();
				this.ef('sesion_tiempo_no_interac_min').ocultar();
				this.ef('sesion_tiempo_maximo_min').ocultar();
				this.ef('validacion_debug').ocultar();
				this.ef('item_inicio_sesion').ocultar();
				this.ef('item_pre_sesion').ocultar();
				this.ef('carpeta_inicio_sesion').ocultar();
				this.ef('carpeta_pre_sesion').ocultar();
			}
		}";
	}
}

?>