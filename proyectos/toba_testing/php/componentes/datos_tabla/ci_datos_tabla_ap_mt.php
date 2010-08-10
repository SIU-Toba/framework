<?php
class ci_datos_tabla_ap_mt extends toba_testing_pers_ci
{
	function ini()
	{
		$this->dep('tabla')->cargar();
	}

	//-----------------------------------------------------------------------------------
	//---- ml ---------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__ml(toba_ei_formulario_ml $form_ml)
	{
		$form_ml->set_datos($this->dep('tabla')->get_filas(null, true));
	}

	function evt__ml__modificacion($datos)
	{
		$this->dep('tabla')->procesar_filas($datos);
		$this->dep('tabla')->sincronizar();
	}
}
?>