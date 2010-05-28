<?php
/**
 * Clase abstracta para manejo de un gadget via shindig
 * @package Centrales
 */

class toba_gadget_shindig extends toba_gadget
{
	function get_tipo()
	{
		return apex_tipo_gadget_shindig;
	}

	function generar_html()
	{
		echo "<div id='gadget-chrome-{$this->_datos['orden']}' class='gadgets-gadget-chrome'></div>\n";

		echo toba_js::abrir();

		echo "
			var gadget{$this->_datos['orden']} = gadgets.container.createGadget({specUrl: '{$this->_datos['gadget_url']}', title: '{$this->_datos['titulo']}', elim: ".($this->es_eliminable() ? 'true':'false')."});
			gadgets.container.addGadget(gadget{$this->_datos['orden']});

			if (typeof gadgets.container.layoutManager.gadgetChromeIds_ == 'undefined') {
				gadgets.container.layoutManager.gadgetChromeIds_ = [];
			}
			gadgets.container.layoutManager.gadgetChromeIds_.push('gadget-chrome-{$this->_datos['orden']}');

			gadgets.container.renderGadget(gadget{$this->_datos['orden']});
		";

		echo toba_js::cerrar();
	}
}
?>
