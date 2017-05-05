<?php

class toba_usuarios_normal extends toba_tp_normal
{
	
	protected function menu()
	{
		if (isset($this->menu)) {
			//Modifico el JS de una opcion de menu en particular para que se ejecute en popup siempre.
			$js_nuevo = 'return toba.ir_a_operacion("toba_usuarios", "33000035", true)';
			$this->menu->set_datos_opcion('33000035', array('js' => $js_nuevo));						
			$this->menu->mostrar();
		}		
	}
	
	protected function cabecera_aplicacion()
	{
		$mostrar_app_launcher = toba::proyecto()->get_parametro('proyecto', 'app_launcher', false);
		if (!$mostrar_app_launcher) {
			$acceso_unico = (toba::manejador_sesiones()->get_cantidad_proyectos_cargados() == 1);
			$js = $acceso_unico ? 'salir()' : 'window.close()';
			echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.toba::escaper()->escapeHtmlAttr($js).'">';
			echo toba_recurso::imagen_toba('finalizar_sesion.gif', true);
			echo '</a>';
			//--- Usuario
			$this->info_usuario();
		} else {
			//--- Usuario y aplicaciones
			$this->info_usuario_aplicaciones();
		}
		
		//--- Logo
		echo "<div id='enc-logo' style='height:". toba::escaper()->escapeHtmlAttr($this->alto_cabecera)."'>";
		$this->mostrar_logo();
		echo "</div>\n";
	}
}
?>
