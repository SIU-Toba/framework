<?php

class tp_basico extends tipo_pagina
{
	function encabezado()
	{
		$this->cabecera_html();
		$this->comienzo_cuerpo();
		$this->barra_superior();
	}
	
	function pie()
	{
		echo "</BODY>\n";
		echo "</HTML>\n";
	}	
	
	protected function cabecera_html()
	{
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
		echo "<HTML>\n";
		echo "<HEAD>\n";
		echo "<title>".$this->titulo_pagina()."</title>\n";
		$this->encoding();
		$this->plantillas_css();
		$this->estilos_css();
		js::cargar_consumos_basicos();
		echo "</HEAD>\n";
	}
	
	protected function titulo_pagina()
	{
		$item = toba::get_solicitud()->get_datos_item();
		return $item['item_nombre'];
	}
	
	protected function encoding()
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
	}

	protected function plantillas_css()
	{
		echo recurso::link_css(apex_proyecto_estilo, "screen");
		echo recurso::link_css(apex_proyecto_estilo."_impr", "print");		
	}
	
	protected function estilos_css()
	{
	}
	

	protected function comienzo_cuerpo()
	{
		echo "<body>\n";
		$this->menu();
	}
	
	protected function menu()
	{
	}
	
	protected function barra_superior()
	{
	}

}


?>
