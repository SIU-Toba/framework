<?php

/**
 * Icono de utileria que se le agrega a un ef_popup para poder abrir/ver un codigo php
 */
class ef_popup_utileria_php implements toba_ef_icono_utileria
{
	protected $vinculo;
	protected $es_abrir;

	function __construct($es_abrir)
	{
		$this->es_abrir = $es_abrir;
		$vinculo = new toba_vinculo('toba_editor', 30000014);
		$vinculo->set_celda_memoria('php');
		if ($this->es_abrir) {
			$vinculo->set_servicio('ejecutar');
			$vinculo->set_ajax(true);
		}
		$this->vinculo = toba::vinculador()->registrar_vinculo($vinculo);
	}

	function get_html(toba_ef $ef)
	{
		$objeto_js = $ef->objeto_js();
		if ($this->es_abrir) {
			$img = toba_recurso::imagen_proyecto('reflexion/abrir.gif', true);
		} else {
			$img = toba_recurso::imagen_toba('nucleo/php.gif', true);
		}
		$salida = "<a href='#' onclick=\"if ($objeto_js.get_estado() == '') return;
										vinculador.agregar_parametros({$this->vinculo}, {archivo: $objeto_js.get_estado()});
										vinculador.invocar({$this->vinculo})\">$img</a>";
		return $salida;
	}
}

?>
