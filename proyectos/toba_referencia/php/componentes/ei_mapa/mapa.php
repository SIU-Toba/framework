<?php
class mapa extends toba_ei_mapa
{
	function extender_objeto_js()
	{
		$id_js = toba::escaper()->escapeJs($this->objeto_js);
		echo "
			var _iconSelectButton = imgDir + 'info_chico.gif';
			{$id_js}.evt__mapa__iniciar = function()
			{
				this._toolbar.addTool(new msTool('Puntualizame', 'evt__seleccionar__punto', _iconSelectButton, true));
			}

			{$id_js}.evt__seleccionar__punto = function (evento)
			{
					var punto = this.get_punto_click(evento);
					notificacion.agregar('Se selecciono el punto de coordenadas (x.y) = (' + punto['X'] + ' , ' + punto['Y'] + ') ', 'info');
					notificacion.mostrar();
			}
		";

	}
}

?>