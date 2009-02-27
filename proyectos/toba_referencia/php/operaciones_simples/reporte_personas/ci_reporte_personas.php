<?php
php_referencia::instancia()->agregar(__FILE__);
require_once('operaciones_simples/consultas.php'); 

class ci_reporte_personas extends toba_ci
{
	protected $s__datos_filtro;

	function conf()
	{
		if (! isset($this->s__datos_filtro)) {
			$this->pantalla()->eliminar_dep('cuadro');
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_formateo_columna('dia_semana', 'dia_semana', 'formateo_reporte');
		$where = $this->dep('filtro')->get_sql_where();
		$datos = consultas::get_deportes_por_persona($where);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(toba_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		if (isset($this->s__datos_filtro)) {
			unset($this->s__datos_filtro);
		}
	}

}


/**
 * Clase que hereda el formato de cuadro de toba para agregar un nuevo mecanismo
 */
class formateo_reporte extends toba_formateo
{
	function formato_dia_semana($valor)
	{
		$valor = consultas::get_dia_semana($valor);
		$valor = $valor[0]['desc_dia_semana'];
		if ($this->tipo_salida != 'excel') {
			return $valor;
		} else {
			return array($valor, null);
		}
	}

}
?>