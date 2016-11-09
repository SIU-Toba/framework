<?php
class cuadro_catalogo extends toba_ei_cuadro
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		$escapador = toba::escaper();
		$opciones_abrir = array('servicio' => 'ejecutar', 'celda_memoria' => 'ajax', 'menu' => true);
		$vinculo_abrir = toba::vinculador()->get_url(toba_editor::get_id(), '30000014', array(), $opciones_abrir);

		$opciones_editar = array('celda_memoria' => 'central', 'menu' => true);
		$vinculo_editar = toba::vinculador()->get_url(toba_editor::get_id(), '30000014', array(), $opciones_editar);
		$frame = 'parent.'.apex_frame_centro;

		$id_js = $escapador->escapeJs($this->objeto_js);
		echo "
		//---- Eventos ---------------------------------------------
		
		{$id_js}.evt__abrir = function(archivo)
		{
			var url = '". $escapador->escapeJs($vinculo_abrir)."';
			var url = vinculador.concatenar_parametros_url(url, {'archivo' : archivo});
			toba.comunicar_vinculo(url);
			return false;
		}
		
		{$id_js}.evt__editar = function(archivo)
		{
			var url = '". $escapador->escapeJs($vinculo_editar)."';
			var url = vinculador.concatenar_parametros_url(url, {'archivo' : archivo});
			$frame.location.href = url;
			return false;
		}
		";
	}

}

?>