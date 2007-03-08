<?php
require_once("toba_tp_basico_titulo.php");
require_once("nucleo/lib/interface/toba_form.php");

/**
 * Este tipo de página incluye una cabecera con:
 *  - Menú
 *  - Logo
 *  - Información básica del usuario logueado
 *  - Capacidad de cambiar de proyecto 
 *  - Capacidad de desloguearse
 * @package SalidaGrafica
 */
class toba_tp_normal extends toba_tp_basico_titulo
{
	protected $menu;
	protected $alto_cabecera = "34px";

	function __construct()
	{
		$archivo_menu = toba::proyecto()->get_parametro('menu_archivo');
		require_once($archivo_menu);
		$clase = basename($archivo_menu, ".php");
		$this->menu = new $clase();
	}
	
	protected function comienzo_cuerpo()
	{
		parent::comienzo_cuerpo();
		$this->menu();					
		$this->cabecera_aplicacion();			
	}

	protected function menu()
	{
		if (isset($this->menu)) {
			$this->menu->mostrar();
		}		
	}

	protected function plantillas_css()
	{
		if (isset($this->menu)) {
			$estilo = $this->menu->plantilla_css();
			if ($estilo != '') {
				echo toba_recurso::link_css($estilo, "screen");
			}
		}
		parent::plantillas_css();
	}
	
	protected function cabecera_aplicacion()
	{
		if ( toba::proyecto()->get_parametro('requiere_validacion') ) {
			//--- Salir
			$js = toba_editor::modo_prueba() ? 'window.close()' : 'salir()';
			echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.$js.'"><img src='.
					toba_recurso::imagen_toba('finalizar_sesion.gif').
					' border="0"></a>';
			
			//--- Usuario
			$this->info_usuario();
		}
		
		//--- Proyecto
		if(toba::proyecto()->es_multiproyecto()) {		
			$this->cambio_proyecto();
		}		
		//--- Logo
		echo "<div id='enc-logo' style='height:{$this->alto_cabecera}'>";
		$this->mostrar_logo();
		echo "</div>\n";
	}

	/**
	 * Genera el HTML que posibilita cambiar entre procesos
	 * @ventana
	 */
	protected function cambio_proyecto()
	{
		$proyectos = toba::instancia()->get_proyectos_accesibles();
		$actual = toba::proyecto()->get_id();
		if (count($proyectos) > 1) {
			//-- Si hay al menos dos proyectos
			echo '<div class="enc-cambio-proy">';
			echo '<a href="#" title="Ir a la inicio" onclick="vinculador.ir_a_proyecto(\''.$actual.'\');">'.
					toba_recurso::imagen_toba("home.png",true).'</a>';
			$datos = rs_convertir_asociativo($proyectos, array(0), 1);
			echo toba_form::select(apex_sesion_qs_cambio_proyecto, $actual, 
								$datos, 'ef-combo', 'onchange="vinculador.ir_a_proyecto(this.value)"');
			echo toba_js::abrir();
			echo 'var url_proyectos = '.toba_js::arreglo(toba::instancia()->get_url_proyectos(array_keys($datos)), true);
			echo toba_js::cerrar();
			echo '</div>';
		}
	}
	
	protected function mostrar_logo()
	{
		echo toba_recurso::imagen_proyecto('logo.gif', true);
	}
	
	protected function info_usuario()
	{
		echo '<div class="enc-usuario">';
		echo "<span class='enc-usuario-nom'>".toba::usuario()->get_nombre()."</span>";
		echo "<span class='enc-usuario-id'>".toba::usuario()->get_id()."</span>";
		echo '</div>';
	}		

}
?>