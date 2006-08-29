<?php
require_once("tp_basico_titulo.php");
require_once("nucleo/lib/interface/form.php");

class tp_normal extends tp_basico_titulo
{
	protected $menu;
	protected $alto_cabecera = "34px";

	function __construct()
	{
		$archivo_menu = info_proyecto::instancia()->get_parametro('menu_archivo');
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
				echo recurso::link_css($estilo, "screen");
			}
		}
		parent::plantillas_css();
	}
	
	protected function cabecera_aplicacion()
	{
		//--- Salir
		$js = editor::modo_prueba() ? 'window.close()' : 'salir()';
		echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.$js.'"><img src='.
				recurso::imagen_apl('finalizar_sesion.gif').
				' border="0"></a>';
		
		//--- Usuario
		$this->info_usuario();
		
		//--- Proyecto
		if(info_proyecto::instancia()->es_multiproyecto()) {		
			$this->cambio_proyecto();
		}		
		if (apex_pa_proyecto=="multi") {
			echo form::abrir("multiproyecto",toba::get_hilo()->cambiar_proyecto(),"target = '_top'");
		}	
		//--- Logo
		echo "<div style='height:{$this->alto_cabecera}'>";
		$this->mostrar_logo();
		echo "</div>\n";
	}

	protected function cambio_proyecto()
	{
		echo "<div class='enc-cambio-proy'>";
		echo recurso::imagen_apl("proyecto.gif",true);
		$datos = info_instancia::get_lista_proyectos_instancia(toba::get_hilo()->obtener_usuario());
		echo form::select(apex_sesion_qs_cambio_proyecto, info_proyecto::instancia()->get_id(), 
					rs_convertir_asociativo($datos, array(0), 1),
					'ef-combo', "onchange='multiproyecto.submit();'");
		echo "</div>";
	}
	
	protected function mostrar_logo()
	{
		echo recurso::imagen_pro('logo.gif', true);
	}
	
	protected function info_usuario()
	{
		echo '<div class="enc-usuario">';		
		echo "<span class='enc-usuario-nom'>".toba::get_usuario()->get_nombre()."</span>";
		echo "<span class='enc-usuario-id'>".toba::get_usuario()->get_id()."</span>";
		echo '</div>';		
	}		
	
	function pie()
	{
		parent::pie();	
	}
}
?>