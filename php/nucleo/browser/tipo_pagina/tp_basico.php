<?php
require_once("nucleo/lib/usuario_toba.php");

class tp_basico extends tipo_pagina
{
	protected $menu;
	protected $alto_cabecera = "34px";
	
	function __construct()
	{
		if (defined("apex_pa_menu_archivo")) {
			require_once(apex_pa_menu_archivo);
			$clase = basename(apex_pa_menu_archivo, ".php");;
			$this->menu = new $clase();
		}
	}
	
	function encabezado()
	{
		$this->cabecera_html();
		$this->comienzo_cuerpo();
		$this->barra_superior();
	}
	
	function pre_contenido(){}
	
	function post_contenido(){}

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
		return toba::get_hilo()->obtener_proyecto_descripcion() . ' - ' . $item['item_nombre'];
	}
	
	protected function encoding()
	{
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>\n";
	}

	protected function plantillas_css()
	{
		if (isset($this->menu)) {
			$estilo = $this->menu->plantilla_css();
			if ($estilo != '') {
				echo recurso::link_css($estilo, "screen");
			}
		}
		echo recurso::link_css(apex_proyecto_estilo, "screen");
		echo recurso::link_css(apex_proyecto_estilo."_impr", "print");		
	}
	
	protected function estilos_css()
	{
		?>
		<style type="text/css">
			#dhtmltooltip{
				position: absolute;
				width: 150px;
				border: 1px solid black;
				padding: 2px;
				background-color: lightyellow;
				visibility: hidden;
				z-index: 100;
			}
			#dhtml_tooltip_div{
				position:absolute;
				width: 200px;
				visibility:hidden;
				background-color: lightyellow;			 
				padding: 2px;
				border: 1px solid black;
				line-height:18px;
				z-index:100;
			}			
			#div_calendario {
				visibility:hidden;
				position:absolute;
				background-color: white;
			}
		</style>			
		<?php
	}

	protected function comienzo_cuerpo()
	{
		echo "<body>\n";
		js::cargar_consumos_globales(array('dhtml_tooltip'));
	}
	

	protected function menu()
	{
		if (isset($this->menu)) {
			$this->menu->mostrar();
		} elseif(defined('apex_pa_menu') && apex_pa_menu == "milonic") {
			//--- Migracion 0.8.3 ----
			//Cargo el menu milonic, si el punto de acceso lo solicita
			toba::get_logger()->obsoleto("", "", "0.8.3", "El menú debe ser una propiedad del proyecto");
			require_once("nucleo/browser/includes/menu_inferior.php");
			//--------------------------------
		}		
	}
	
	protected function barra_superior()
	{
	}


}


?>
