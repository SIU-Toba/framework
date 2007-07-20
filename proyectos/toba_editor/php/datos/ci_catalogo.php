<?php 

class ci_catalogo extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fuentes(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_fuentes_datos();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'fuente.png';
		}
		$cuadro->set_datos($datos);
	}

	function conf__tablas(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_objetos_dt();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'objetos/datos_tabla.gif';
		}
		$cuadro->set_datos($datos);
	}

	function conf__relaciones(toba_ei_cuadro $cuadro)
	{
		$datos = toba_info_editores::get_lista_objetos_dr();
		foreach(array_keys($datos) as $id) {
			$datos[$id]['icono']	= 'objetos/datos_relacion.gif';
		}
		$cuadro->set_datos($datos);
	}

	function conf__consultas(toba_ei_cuadro $cuadro)
	{
	}
}
?>