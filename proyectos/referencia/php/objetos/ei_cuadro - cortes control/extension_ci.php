<?
require_once("nucleo/componentes/interface/objeto_ci.php");

class ci_cuadro_cc extends objeto_ci
{
	/*        PLANO         */

	function evt__cuadro__carga()
	{
		return $this->get_info_localidades();
	}

	/*		TABULARES		*/
	
	function evt__cuadro_tab__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_2__carga()
	{
		return $this->get_info_localidades();
	}
	
	function evt__cuadro_tab_regs__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_sum__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_sum_ah_1__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_sum_ah_2__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_est_1__carga()
	{
		return $this->get_info_localidades();
	}		

	function evt__cuadro_tab_est_2__carga()
	{
		return $this->get_info_localidades();
	}		

	function evt__cuadro_tab_full__carga()
	{
		return $this->get_info_localidades();
	}

	function evt__cuadro_tab_full_ext__carga()
	{
		return $this->get_info_localidades();
	}

	/*      ANIDADOS         */
	
	function evt__cuadro_cortes_sum__carga()
	{
		return $this->get_info_localidades();
	}
	
	function evt__cuadro_cortes_est__carga()
	{
		return $this->get_info_localidades();
	}

	// DATOS
	
	private function get_info_localidades()
	{
		require_once('objetos/datos_ejemplos.php');
		return datos_ejemplos::get_localidades();
	}

}
?>