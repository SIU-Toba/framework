<?php 
//--------------------------------------------------------------------
class eiform_proyecto_conflogin extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			.".evt__requiere_validacion__procesar = function () {
			if( this.ef('requiere_validacion').chequeado() ){
				this.ef('validacion_intentos').mostrar();
				this.ef('validacion_intentos_min').mostrar();
				this.ef('sesion_tiempo_no_interac_min').mostrar();
				this.ef('sesion_tiempo_maximo_min').mostrar();
				this.ef('validacion_debug').mostrar();
				//this.ef('sep_item_login').mostrar();
				this.ef('carpeta_pre_sesion').mostrar();
				this.ef('item_pre_sesion').mostrar();
				this.ef('item_pre_sesion_popup').mostrar();
				this.ef('usuario_anonimo').ocultar();
				this.ef('usuario_anonimo_desc').ocultar();
				this.ef('usuario_anonimo_grupos_acc').ocultar();
			}else{
				this.ef('usuario_anonimo').mostrar();
				this.ef('usuario_anonimo_desc').mostrar();
				this.ef('usuario_anonimo_grupos_acc').mostrar();

				this.ef('validacion_intentos').ocultar();
				this.ef('validacion_intentos_min').ocultar();
				this.ef('sesion_tiempo_no_interac_min').ocultar();
				this.ef('sesion_tiempo_maximo_min').ocultar();
				this.ef('validacion_debug').ocultar();
				//this.ef('sep_item_login').ocultar();
				this.ef('item_pre_sesion').ocultar();
				this.ef('carpeta_pre_sesion').ocultar();
				this.ef('item_pre_sesion_popup').ocultar();

			}
		}";
	}
}
?>