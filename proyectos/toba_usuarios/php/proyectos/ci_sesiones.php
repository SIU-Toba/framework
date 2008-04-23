<?php 
class ci_sesiones extends toba_ci
{
	protected $s__filtro;
	protected $s__proyecto;
	protected $s__sesion;
	
	//-----------------------------------------------------------------------------------
	//---- Inicializacion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	function ini__operacion()
	{
		if (toba::memoria()->existe_dato_instancia('instancia')) {
			//$this->s__proyecto = toba_contexto_info::get_proyecto();
			$this->s__proyecto = toba::memoria()->get_dato_instancia('proyecto');
		}else{
			
		}
	}

	function ini()
	{
		$proyecto = toba::memoria()->get_parametro('proyecto');
		if(isset($proyecto)) $this->s__proyecto = $proyecto;
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
		if(isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
			$componente->colapsar();
		}
	}

	//---- sesiones ---------------------------------------------------------------------

	function conf__listar_sesiones()
	{
		$desc = 'Sesiones del proyecto <strong>'.$this->s__proyecto.'</strong>';
		$this->pantalla()->set_descripcion($desc);	
	}

	function evt__sesiones__seleccion($seleccion)
	{
		$this->s__sesion = $seleccion['id'];
		$this->set_pantalla('listar_solicitudes');
	}

	function conf__sesiones($componente)
	{
		if(isset($this->s__filtro)) {
			$componente->set_datos( consultas_instancia::get_sesiones($this->s__proyecto, $this->s__filtro) );
		}
	}

	//---- solicitudes ------------------------------------------------------------------

	function conf__listar_solicitudes()
	{
		$datos = consultas_instancia::get_sesiones($this->s__proyecto, array('sesion'=>$this->s__sesion));
		$desc = 'Sesiones del proyecto <strong>'.$this->s__proyecto.'</strong><br>';
		$desc .= 'Usuario: <strong>'.$datos[0]['usuario'].'</strong><br>';
		$desc .= 'Ingreso: <strong>'.$datos[0]['ingreso'].'</strong><br>';
		$desc .= 'Egreso: <strong>'.$datos[0]['egreso'].'</strong><br>';
		$desc .= 'IP: <strong>'.$datos[0]['ip'].'</strong><br>';
		$desc .= 'Solicitudes registradas: <strong>'.$datos[0]['solicitudes'].'</strong><br>';
		$this->pantalla()->set_descripcion($desc);	
	}

	function conf__solicitudes($componente)
	{
		$componente->set_datos( consultas_instancia::get_solicitudes_browser($this->s__sesion) );
	}

	function evt__volver()
	{
		unset($this->s__sesion);
		$this->set_pantalla('listar_sesiones');	
	}
}
?>