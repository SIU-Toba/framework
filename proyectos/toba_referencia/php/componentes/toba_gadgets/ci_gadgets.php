<?php
class ci_gadgets extends toba_ci
{
	protected $gadgets_base;
	protected $container;
	
	function ini()
	{
		//Recupero todos los gadgets asociados a este usuario en el proyecto actual
		$this->gadgets_base = toba::proyecto()->get_gadgets_proyecto(toba::usuario()->get_id());
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
		//Obtengo un contenedor para los gadgets a mostrar en pantalla
		$this->container = toba::contenedor_gadgets();
		$this->container->agregar_gadgets($this->gadgets_base);

		//Creo un gadget nuevo de manera manual, este no esta en la base de datos
		$gadget_nuevo = new toba_gadget_shindig();
		$gadget_nuevo->set_titulo('Gadget Manual');
		$gadget_nuevo->set_gadget_url('http://www.tc.df.gov.br/MpjTcdf/AlcCalc.xml');
		$gadget_nuevo->set_orden(3);

		//Lo agrego al contenedor
		$this->container->agregar_gadgets(array($gadget_nuevo));
	}

	function get_contenedor()
	{
		if (isset($this->container)) {
			return $this->container;
		}
	}
}

?>