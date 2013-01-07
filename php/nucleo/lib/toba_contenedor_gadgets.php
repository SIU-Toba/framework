<?php
/**
 * Clase para modelar un contenedor de gadgets.
 * @package Centrales 
 */

class toba_contenedor_gadgets
{
	static protected $instancia;
	protected $gadgets = array();

	private function  __construct()
	{}

	static function instancia()
	{
		if (! isset(self::$instancia)) {
			self::$instancia = new toba_contenedor_gadgets();
		}
		return self::$instancia;
	}

	/**
	 *  Agrega nuevos gadgets en runtime a la coleccion que mantiene el contenedor
	 * @param array $new_gadgets Arreglo de objetos toba_gadgets
	 */
	function agregar_gadgets($new_gadgets)
	{
		if (! is_array($new_gadgets)) {
			throw new toba_error_def('Se espera un arreglo de objetos toba_gadget');
		}
		$this->gadgets = array_merge($this->gadgets, $new_gadgets);
	}
	
	/**
	 * Generación de salida HTML para el contenedor de gadgets.
	 */
	function generar_html()
	{
		if (! empty($this->gadgets)) {
			$this->configurar_consumos_globales_js();
			echo toba_recurso::link_css('gadgets', 'screen');
			echo "<div id='gadgets-container' class='gadgets-container'>";
			$this->generar_html_gadgets();
			echo '</div>';
			echo toba_js::abrir();
			echo '
				var gc = document.getElementById("gadgets-container");
				var e;
				if(document.getElementsByClassName) {
				 	e = document.getElementsByClassName("encabezado")[0];
				} else {
					var divs = document.getElementsByTagName("div");
					for(var i in divs) {
						if(divs[i].className == "encabezado") {
							e = divs[i];
							break;
						}
					}
				}
				gc.style.top = (e.clientHeight + 5)+"px";
			';
			echo toba_js::cerrar();
		}
	}

	/**
	 *  Envia los archivos js necesarios para correr shindig
	 * @ignore
	 */
	protected function configurar_consumos_globales_js()
	{
			toba_js::cargar_consumos_globales(array(
									'shindig/features/src/main/javascript/features/rpc/wpm.transport',
									'shindig/features/src/main/javascript/features/rpc/rpc',
									'shindig/javascript/container/cookies',
									'shindig/javascript/container/util',
									'shindig/javascript/container/gadgets',
									'shindig/javascript/container/cookiebaseduserprefstore')
			);
	}

	/**
	 *  Cicla por los gadgets pidiendoles que generen su HTML
	 * @ignore
	 */
	protected function generar_html_gadgets()
	{
			toba::logger()->debug( 'Gadgets cargados: ' . count($this->gadgets));
			foreach ($this->gadgets as $gadget) {
				$gadget->generar_html();
			}
	}
}

?>
