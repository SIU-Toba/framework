<?php
require_once('objetos_toba/ci_eventos.php');
class ci_eventos_mapa extends ci_eventos
{
	
	//-----------------------------------------------------------------------------------
	//---- eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__eventos(eiform_eventos $form)
	{
		$datos = parent::conf__eventos($form);
		$form->set_datos($datos);
		$form->set_solo_lectura(array('accion'));
	}

	//-----------------------------------------------------------------------------------
	//---- eventos_lista ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__eventos_lista(eiform_abm_detalle $form_ml)
	{
		parent::conf__eventos_lista($form_ml);
		$form_ml->desactivar_efs(array('implicito', 'defecto'));
		$form_ml->ef('imagen')->set_obligatorio(true);
	}

	
}
?>