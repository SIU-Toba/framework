<?php 
//--------------------------------------------------------------------
class ci_cascadas extends toba_testing_pers_ci
{
	protected $form_simple;
	protected $form_comp;	
	protected $form_ml;
	
	function ini()
	{
		$props = array('form_simple', 'form_comp', 'form_ml');
		$this->set_propiedades_sesion($props);
	}
	
	function get_combo_dao2($pal1, $pal2)
	{
		return array(
				array('clave' => $pal1."_".$pal2, 'valor' => "$pal1 - $pal2"),
				array('clave' => 'clave', 'valor' => "Valor")
			);
	}
	
	function get_radio_esclavo($par='')
	{
		return array(
				array(1, "Uno $par"),
				array(2, "Dos $par")
			);
	}
	
	function get_editable_dao($par1, $par2)
	{
		return "Valores: $par1, $par2";	
	}
	
	function get_datos_multi($par1)
	{
		return array(
			array('uno', $par1 . ' - 1'),
			array('dos', $par1 . ' - 2'),
			array('tres', $par1 . ' - 3'),
			array('cuatro', $par1 . ' - 4'),
		);	
	}
	
	function get_combo_temp($popup)
	{
		return array(
				array($popup, $popup)
			);
	}
	
	function get_popup($clave, $oculto=null)
	{
		return "Descripción: $clave ($oculto)";	
	}
	
	function get_datos_multi_claves($par1)
	{
		return array(
			array('clave1' => 'a1', 'clave2' => 'a2', 'valor' => $par1 . ' - 1'),
			array('clave1' => 'b1', 'clave2' => 'b2', 'valor' => $par1 . ' - 2'),
			array('clave1' => 'c1', 'clave2' => 'c2', 'valor' => $par1 . ' - 3'),
			array('clave1' => 'd1', 'clave2' => 'd2', 'valor' => $par1 . ' - 4'),
		);			
		
	}
	
	function get_combo_dao_comp2($pal1, $pal2)
	{
		return array(
				array('clave1' => $pal1, 'clave2' => $pal2, 'clave3' => $pal1.'_'.$pal2, 'valor' => "$pal1 - $pal2"),
				array('clave1' => 'clave', 'clave2' => 'clave2', 'clave3' => 'clave3', 'valor' => "Valor")
			);
	}	
	
	function get_fijo($clave)
	{
		return "Se selecciono la clave $clave";
	}
	
	function evt__debug()
	{
		if (isset($this->form_simple)) 
			ei_arbol($this->form_simple, 'simple');	
		if (isset($this->form_comp)) 
			ei_arbol($this->form_comp, 'comp');
		if (isset($this->form_ml)) 
			ei_arbol($this->form_ml, 'ml');
	}
	
	//-------------------------------------------------------------------
	//--- DEPENDENCIAS
	//-------------------------------------------------------------------

	function evt__form_ml__modificacion($datos)
	{
		$this->form_ml = $datos;
	}

	function conf__form_ml()
	{
		if (isset($this->form_ml)) {
			return $this->form_ml;
		}
	}
	
	
	//---- form -------------------------------------------------------

	function evt__form_simple__modificacion($datos)
	{
		$this->form_simple = $datos;
	}

	function conf__form_simple()
	{
		if (isset($this->form_simple)) {
			return $this->form_simple;
		}
	}

	//---- Eventos CI -------------------------------------------------------

	function evt__form_comp__modificacion($datos)
	{
		$this->form_comp = $datos;
	}

	function conf__form_comp()
	{
		if (isset($this->form_comp)) {
			return $this->form_comp;
		}
	}	

}

?>