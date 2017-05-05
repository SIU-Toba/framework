<?php
php_referencia::instancia()->agregar(__FILE__);

class extension_filtro extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js)
			. ".evt__metodo__procesar = function() {
				var es_dao = (this.ef('metodo').valor() == 'estatico');
				if (es_dao)
					this.ef('importe').ocultar();					
				else
					this.ef('importe').mostrar();
			}
		";
	}

}

?>