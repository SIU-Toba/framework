<?php
class ci_cuadro_seleccion_multiple extends toba_ci
{
	protected $datos;

	function ini()
	{
			$datos = array();
			for ($i = 0; $i < 10; $i++) {
				$datos['fila'.$i] = array(
					'a' => 'a'. ($i - 1) ,
					'b' => ($i + 4),
					'c' => ($i % 2),
					'd' => 'd'.$i,
					'i' => $i,
				);
			}
			$this->datos = $datos;
	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->evento('seleccion')->set_disparo_diferido(true);
		$cuadro->evento('seleccion')->set_alineacion_pre_columnas(true);
		$cuadro->set_datos($this->datos);
	}
	
	function extender_objeto_js()
	{
		echo toba::escaper()->escapeJs($this->objeto_js).
			".evt__probar = function()
			{
				var seleccion = this.dep('cuadro').get_ids_seleccionados('seleccion');
				var clickadas = this.dep('cuadro').get_ids_seleccionados('multiple_con_etiq');
				notificacion.agregar('Seleccionadas: ' + seleccion.join(', '), 'info');
				notificacion.agregar('Clickeadas! : ' + clickadas.join(','), 'info');
				return false;
			}
		";
	}

	function evt__cuadro__seleccion($seleccion)
	{
		ei_arbol($seleccion, 'Interaccion del evento multiple <strong> Seleccion </strong>');
	}
	
	/**
	 * Atrapa la interacción del usuario con el cuadro mediante los checks
	 * @param array $datos Ids. correspondientes a las filas chequeadas.
	 * El formato es de tipo recordset array(array('clave1' =>'valor', 'clave2' => 'valor'), array(....))
	 */
	function evt__cuadro__multiple_con_etiq($datos)
	{
		ei_arbol($datos, 'Interaccion del evento multiple <strong> Clickeame! </strong>');
	}

	/**
	 * Permite configurar el evento por fila.
	 * Útil para decidir si el evento debe estar disponible o no de acuerdo a los datos de la fila
	 * [wiki:Referencia/Objetos/ei_cuadro#Filtradodeeventosporfila Ver más]
	 */
	function conf_evt__cuadro__multiple_con_etiq(toba_evento_usuario $evento, $fila)
	{
		if (($this->datos[$fila]['i'] % 2) == 0) {
			$evento->anular();
		}
	}
}
?>