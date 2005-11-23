<?
require_once("conversion_toba.php");

class conversion_0_8_3_3 extends conversion_toba
{
	function get_version()
	{
		return "0.8.3.3";	
	}

	/**
	*	Por un BUG en el editor, el estilo por defecto de los titulos de las columnas era incorrecto.
	*/
	function cambio_estilo_css_titulo_columnas()
	{
		$sql = "UPDATE apex_objeto_ei_cuadro_columna 
				SET estilo_titulo = 'lista-col-titulo'
				WHERE trim(estilo_titulo) = 'lista_col_titulo'
				AND objeto_cuadro_proyecto = '$this->proyecto';";
		$this->ejecutar_sql($sql,"instancia");				
	}
}