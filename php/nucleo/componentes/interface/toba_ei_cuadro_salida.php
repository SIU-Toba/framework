<?php
class toba_ei_cuadro_salida
{
	protected $_cuadro;
	protected $_objeto_toba_salida;

	function __construct(toba_ei_cuadro $cuadro)
	{
		$this->_cuadro = $cuadro;
	}
	
	/**
	 * Retorna el texto que sumariza la cantidad de filas de un nivel de corte
	 * @param integer $profundidad Nivel de profundidad actual
	 * @return string
	 */
	protected function etiqueta_cantidad_filas($profundidad)
	{
		$etiqueta = $this->_cuadro->get_etiqueta_cantidad_filas();
		if (! is_null($etiqueta) && trim($etiqueta) != '') {
			$etiqueta .= ': ';
		} else {
			$etiqueta = 'Cantidad de filas: ';
		}		
		return $etiqueta;
	}

	function set_instancia_toba_salida($salida)
	{
		$this->_objeto_toba_salida = $salida;
	}
}
?>