<?php

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
		$this->menu = toba::menu();
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
				echo toba_recurso::link_css($estilo, 'screen', false);
			}
		}
		parent::plantillas_css();
	}
	
	protected function cabecera_aplicacion()
	{
		if ( toba::proyecto()->get_parametro('requiere_validacion') ) {
			$mostrar_app_launcher = toba::proyecto()->get_parametro('proyecto', 'app_launcher', false);
			if (!$mostrar_app_launcher) {
				//--- Salir
				$js = toba_editor::modo_prueba() ? 'window.close()' : 'salir()';
				echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.$js.'">';
				echo toba_recurso::imagen_toba('finalizar_sesion.gif', true, null, null, 'Cerrar la sesión');
				echo '</a>';

				//--- Usuario
				$this->info_usuario();
			} else {
				//--- Usuario y aplicaciones
				$this->info_usuario_aplicaciones();
			}
		}
		
		$muestra = toba::proyecto()->get_parametro('proyecto', 'mostrar_resize_fuente', false);
		if (! is_null($muestra) && $muestra) {
			$this->mostrar_resize_fuente();
		}

		//--- Proyecto
		if(toba::proyecto()->es_multiproyecto()) {
			$this->cambio_proyecto();
		}
		if (toba::proyecto()->permite_cambio_perfiles()) {
			$this->cambio_perfil();
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
	
	function cambio_perfil()
	{
		$perfiles = toba::instancia()->get_datos_perfiles_funcionales_usuario_proyecto( toba::usuario()->get_id(), toba::proyecto()->get_id());		
		if (count($perfiles) > 1) {
			//-- Si hay al menos dos perfiles funcionales
			echo '<div class="enc-cambio-proy">';
			$perfiles[] = array('grupo_acceso' => apex_ef_no_seteado, 'nombre' => ' Todos ' );
			$datos = rs_convertir_asociativo($perfiles, array('grupo_acceso' ), 'nombre');
			$actual = toba::memoria()->get_dato('usuario_perfil_funcional_seleccionado');
			if (is_null($actual)) {
				$actual = apex_ef_no_seteado;
			}			
			echo toba_form::abrir('chng_profile', toba::vinculador()->get_url());
			echo toba_form::select(apex_sesion_qs_cambio_pf, $actual, $datos, 'ef-combo', 'onchange="submit();"');	
			echo toba_form::cerrar();			
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
		echo "<span class='enc-usuario-nom'>".texto_plano(toba::usuario()->get_nombre())."</span>";
		echo "<span class='enc-usuario-id'>".texto_plano(toba::usuario()->get_id())."</span>";
		echo '</div>';
	}

	protected function info_usuario_aplicaciones()
	{
		toba::app_launcher()->mostrar_html_app_launcher();
	}
	
}
?>