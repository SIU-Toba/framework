<?php 
class ci_bloqueo_usuarios extends toba_ci
{
	protected $s__filtro;

	function conf()
	{
		if (consultas_instancia::get_cantidad_usuarios_bloqueados() == 0) {
			$this->pantalla()->eliminar_evento('desbloquear');	
		}	
		if (consultas_instancia::get_cantidad_usuarios_desbloqueados() == 0) {
			$this->pantalla()->eliminar_evento('bloquear');	
		}	
	}
	
	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			if ($this->s__filtro['estado']) {
				$this->dep('cuadro_usuarios')->eliminar_evento('bloquear');
				$this->pantalla()->eliminar_evento('bloquear');
			} else {
				$this->dep('cuadro_usuarios')->eliminar_evento('desbloquear');
				$this->pantalla()->eliminar_evento('desbloquear');
			}
			$componente->set_datos($this->s__filtro);
		} else {
			$this->pantalla()->eliminar_evento('desbloquear');	
			$this->pantalla()->eliminar_evento('bloquear');	
		}
	}
	
	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	function evt__cuadro_usuarios__desbloquear($seleccion)
	{
		admin_instancia::eliminar_bloqueo_usuario($seleccion['usuario']);
	}
	
	function evt__cuadro_usuarios__bloquear($seleccion)
	{
		admin_instancia::agregar_bloqueo_usuario($seleccion['usuario']);
	}	

	function conf__cuadro_usuarios($componente)
	{
		if (isset($this->s__filtro)) {
			if ($this->s__filtro['estado']) {
				$titulo = 'Listado de Usuarios Bloqueados';
			} else {
				$titulo = 'Listado de Usuarios Desbloqueados';
			}
			$componente->set_titulo($titulo);
			$componente->set_datos(admin_instancia::get_lista_usuarios_bloqueados($this->s__filtro['estado']));	
		}		
	}

	function evt__desbloquear()
	{
		admin_instancia::eliminar_bloqueo_usuarios();
	}
	
	function evt__bloquear()
	{
		admin_instancia::agregar_bloqueo_usuarios();
	}
}

?>