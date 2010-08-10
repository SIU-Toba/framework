<?php
class form_perfil_funcional extends toba_testing_pers_ei_formulario
{

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		{$this->objeto_js}.evt__fecha__procesar = function(es_inicial)
		{
			var chequeado = true;
			if (this.ef('check')) {
				chequeado = this.ef('check').chequeado();
			}
			if (chequeado) {
				this.ef('editable').set_estado( 'Fecha es: ' + this.ef('fecha').get_estado());
			}
			this.controlador.controlador.mostrar_boton('procesar');
		}
		";
	}

	function generar_layout()
	{
		$this->generar_html_ef('fecha');
		$this->generar_html_ef('check');
		$this->generar_html_ef('editable');
	}

}
?>