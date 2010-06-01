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
		$orden = $this->get_orden();
		$url = $this->get_gadget_url();
		$titulo = $this->get_titulo();
		
		echo "<div id='gadget-chrome-$orden' class='gadgets-gadget-chrome'></div>\n";

		echo toba_js::abrir();

		echo "
			var gadget$orden = gadgets.container.createGadget({specUrl: '$url', title: '$titulo', elim: ".($this->es_eliminable() ? 'true':'false')."});
			gadgets.container.addGadget(gadget$orden);

			if (typeof gadgets.container.layoutManager.gadgetChromeIds_ == 'undefined') {
				gadgets.container.layoutManager.gadgetChromeIds_ = [];
			}
			gadgets.container.layoutManager.gadgetChromeIds_.push('gadget-chrome-$orden');

			gadgets.container.renderGadget(gadget$orden);
		";

		echo toba_js::cerrar();
	}
}
?>
