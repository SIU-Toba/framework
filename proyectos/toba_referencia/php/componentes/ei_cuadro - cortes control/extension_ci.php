<?php
php_referencia::instancia()->agregar(__FILE__);

class ci_cuadro_cc extends toba_ci
{
	/*        PLANO         */

	function conf__cuadro()
	{
		return $this->get_info_localidades();
	}

	/*		TABULARES		*/
	
	function conf__cuadro_tab()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_2(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_mostrar_titulo_antes_cc(true);
		return $this->get_info_localidades();
	}
	
	function conf__cuadro_tab_regs()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_sum()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_sum_ah_1()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_sum_ah_2()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_est_1()
	{
		return $this->get_info_localidades();
	}		

	function conf__cuadro_tab_est_2()
	{
		return $this->get_info_localidades();
	}		

	function conf__cuadro_tab_full()
	{
		return $this->get_info_localidades();
	}

	function conf__cuadro_tab_full_ext()
	{
		return $this->get_info_localidades();
	}

	/*      ANIDADOS         */
	
	function conf__cuadro_cortes_sum()
	{
		return $this->get_info_localidades();
	}
	
	function conf__cuadro_cortes_est()
	{
		return $this->get_info_localidades();
	}

	// DATOS
	
	private function get_info_localidades()
	{
		require_once('componentes/datos_ejemplos.php');
		return datos_ejemplos::get_localidades();
	}

}
?>