<?php

class toba_usuarios_normal extends toba_tp_normal
{
	protected function cabecera_aplicacion()
	{
		if ( toba::proyecto()->get_parametro('requiere_validacion') ) {
			//--- Salir
			$acceso_desde_administrador = toba::memoria()->existe_dato_instancia('instancia');
			$js = $acceso_desde_administrador ? 'window.close()' : 'salir()';
			echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.$js.'">';
			echo toba_recurso::imagen_toba('finalizar_sesion.gif', true);
			echo '</a>';
			//--- Usuario
			$this->info_usuario();
		}
		
		//--- Logo
		echo "<div id='enc-logo' style='height:{$this->alto_cabecera}'>";
		$this->mostrar_logo();
		echo "</div>\n";
	}
}
?>