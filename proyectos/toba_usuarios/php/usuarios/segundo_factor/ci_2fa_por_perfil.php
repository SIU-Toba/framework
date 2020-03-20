<?php
class ci_2fa_por_perfil extends toba_ci
{
	protected $s__filtro;
	protected $s__perfiles_marcados = array();
	protected $s__perfiles_originales= array();
	
	function ini__operacion()
	{
		if (! is_null(\admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => \admin_instancia::get_proyecto_defecto());
		}		
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)) {
			$this->s__perfiles_originales = \consultas_instancia::get_lista_grupos_acceso($this->s__filtro);
			$cuadro->set_datos($this->s__perfiles_originales);
		}

	}

	function evt__cuadro__segundo_factor_mas($seleccion)
	{
		try {
			toba::db()->abrir_transaccion();
			$membresias = $this->buscar_membresias($seleccion);
			$this->marcar_usuarios_perfil($membresias);
			toba::db()->cerrar_transaccion();
		}catch (\toba_error_db $e) {
			toba::db()->abortar_transaccion();
			throw $e;
		}
	}

	
	function evt__cuadro__segundo_factor_menos($seleccion)
	{
		try {
			toba::db()->abrir_transaccion();
			$membresias = $this->buscar_membresias($seleccion);
			if (count($membresias) == 1) {		//No desactivo cuando hay mas de un perfil funcional involucrado
				$this->desmarcar_usuarios_perfil($membresias);
			}
			toba::db()->cerrar_transaccion();
		}catch (\toba_error_db $e) {
			toba::db()->abortar_transaccion();
			throw $e;
		}
	}
	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(form_proyecto $form)
	{
		if (isset($this->s__filtro)) {
			$form->set_datos($this->s__filtro);
		}		

	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	//-----------------------------------------------------------------------------------
	//---- AUXILIARES -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function buscar_membresias($seleccion) 
	{
		$membresias = \toba_proyecto_db::get_perfiles_funcionales_asociados($seleccion['proyecto'], $seleccion['usuario_grupo_acc']);
		array_push($membresias, $seleccion['usuario_grupo_acc']);
		return $membresias;
	}
	
	function marcar_usuarios_perfil($perfiles)
	{
		foreach($perfiles as $perfil) {
			\consultas_instancia::set_segundo_factor_x_perfil($this->s__filtro['proyecto'], $perfil, TRUE);
		}
	}
	
	function desmarcar_usuarios_perfil($perfil)
	{
		$usuarios = \consultas_instancia::get_usuarios_con_grupo_acceso_unico($this->s__filtro['proyecto'], $perfil); 
		\consultas_instancia::set_segundo_factor_x_perfil($this->s__filtro['proyecto'], $perfil, FALSE, $usuarios);
	}
}
?>