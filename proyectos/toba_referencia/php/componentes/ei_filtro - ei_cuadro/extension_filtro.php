<?php
require_once('nucleo/componentes/interface/toba_ei_filtro.php');

class extension_filtro extends toba_ei_filtro
{
	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__metodo__procesar = function() {
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