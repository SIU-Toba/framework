<?php 
class ci_bloqueo_usuarios extends toba_ci
{

	function conf()
	{
		if ( consultas_instancia::get_cantidad_usuarios_bloqueados() == 0) {
			$this->pantalla()->eliminar_evento('desbloquear');	
		}	
	}

	function evt__cuadro_usuarios__desbloquear($seleccion)
	{
		admin_instancia::eliminar_bloqueo_usuario($seleccion['usuario']);
	}

	function conf__cuadro_usuarios($componente)
	{
		$componente->set_datos( admin_instancia::get_lista_usuarios_bloqueados() );
	}

	function evt__desbloquear()
	{
		admin_instancia::eliminar_bloqueo_usuarios();
	}
}

?>