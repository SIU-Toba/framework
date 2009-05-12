<?php
class ci_cuadro_seleccion_multiple extends toba_ci
{
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = array();
		for ($i = 0; $i < 10; $i++) {
			$datos['fila'.$i] = array(
				'a' => 'a'.$i,
				'b' => 'b'.$i,
				'c' => 'c'.$i,
				'd' => 'd'.$i, 
			);
		}
		$cuadro->evento('seleccion')->set_seleccion_multiple();
		$cuadro->set_datos($datos);
	}
	
	function extender_objeto_js()
	{
		echo "
		
			{$this->objeto_js}.evt__probar = function()
			{
				var seleccion = this.dep('cuadro').get_ids_seleccionados('seleccion');
				notificacion.agregar('Seleccionadas: ' + seleccion.join(', '), 'info');
				return false;
			}
		";
	}

}

?>