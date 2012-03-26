<?php 
class ci_bloqueo_ip extends toba_ci
{
	function conf()
	{
		if (consultas_instancia::get_cantidad_ips_rechazadas() == 0) {
			$this->pantalla()->eliminar_evento('desbloquear');	
		}	
	}
	
	function conf__cuadro($componente)
	{
		$componente->set_datos(admin_instancia::get_lista_ips_rechazadas());
	}

	function evt__cuadro__desbloquear($seleccion)
	{
		admin_instancia::eliminar_bloqueo($seleccion['ip']);
	}

	function evt__desbloquear()
	{
		admin_instancia::eliminar_bloqueos();
	}
}
?>