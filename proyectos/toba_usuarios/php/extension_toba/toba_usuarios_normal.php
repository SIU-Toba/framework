<?php

class toba_usuarios_normal extends toba_tp_normal
{
	protected function cabecera_aplicacion()
	{
		$acceso_unico = (toba::manejador_sesiones()->get_cantidad_proyectos_cargados() == 1);
		$js = $acceso_unico ? 'salir()' : 'window.close()';
		echo '<a href="#" class="enc-salir" title="Cerrar la sesión" onclick="javascript:'.$js.'">';
		echo toba_recurso::imagen_toba('finalizar_sesion.gif', true);
		echo '</a>';
		//--- Usuario
		$this->info_usuario();
		
		//--- Logo
		echo "<div id='enc-logo' style='height:{$this->alto_cabecera}'>";
		$this->mostrar_logo();
		echo "</div>\n";
	}
}
?>