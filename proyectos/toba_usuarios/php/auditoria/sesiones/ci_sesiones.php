<?php 
class ci_sesiones extends toba_ci
{
	protected $s__filtro;
	protected $s__sesion;
	protected $s__id_solicitud;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}
	}
	
	function ini()
	{
		$id_solicitud = toba::memoria()->get_parametro('auditoria_id_solicitud');
		if (isset($id_solicitud)) {
			$this->s__id_solicitud = $id_solicitud;
			$this->s__sesion = consultas_instancia::get_id_sesion($id_solicitud);
			$this->set_pantalla('listar_solicitudes');
		}		
	}
	
	//-----------------------------------------------------------------------------------
	//---- DEPENDENCIAS -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//---- filtro -----------------------------------------------------------------------

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
	}

	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}
	}

	//---- sesiones ---------------------------------------------------------------------

	function evt__sesiones__seleccion($seleccion)
	{
		$this->s__sesion = $seleccion['id'];
		$this->set_pantalla('listar_solicitudes');
	}

	function conf__sesiones($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos(consultas_instancia::get_sesiones($this->s__filtro['proyecto'], $this->s__filtro));
		}
	}

	//---- solicitudes ------------------------------------------------------------------

	function conf__listar_solicitudes()
	{
		$formateo = new toba_formateo('html');
		$datos = consultas_instancia::get_sesiones($this->s__filtro['proyecto'], array('sesion'=>$this->s__sesion));
		if (! empty($datos)) {
			$desc = 'Sesiones del proyecto <strong>'.$this->s__filtro['proyecto'].'</strong><br>';
			$desc .= 'Usuario: <strong>'.$datos[0]['usuario'].'</strong><br>';
			$desc .= 'Ingreso: <strong>'.$formateo->formato_fecha_hora($datos[0]['ingreso']).'</strong><br>';
			$desc .= 'Egreso: <strong>'.$formateo->formato_fecha_hora($datos[0]['egreso']).'</strong><br>';
			$desc .= 'IP: <strong>'.$datos[0]['ip'].'</strong><br>';
			$desc .= 'Accesos registrados: <strong>'.$datos[0]['solicitudes'].'</strong><br>';
			$this->pantalla()->set_descripcion($desc);
		}
	}

	function conf__solicitudes($componente)
	{
		$solicitud = isset($this->s__id_solicitud) ? $this->s__id_solicitud : null;
		if (isset($this->s__sesion)) {
			$componente->set_datos(consultas_instancia::get_solicitudes_browser($this->s__sesion, $solicitud));
		}
	}

	function evt__volver()
	{
		unset($this->s__sesion);
		if (isset($this->s__id_solicitud)) {
			unset($this->s__id_solicitud);
		}
		$this->set_pantalla('listar_sesiones');	
	}
}
?>