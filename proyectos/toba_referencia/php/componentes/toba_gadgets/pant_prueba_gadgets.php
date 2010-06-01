<?php
class pant_prueba_gadgets extends toba_ei_pantalla
{
	
	function generar_layout()
	{
		//Obtengo el contenedor de gadgets del Ci controlador donde se configuro
		//Y le pido que grafique los gadgets en pantalla
		$container = $this->controlador()->get_contenedor();
		$container->generar_html();
	}
}

?>