<?php
class ci_asociar_menu_perfil extends toba_ci
{
	protected $s__filtro;
	protected $s__proyecto;
	protected $s__menu_id;
	private $datos;
	
	function ini()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto()) && ! isset($this->s__proyecto)) {
			$this->s__proyecto = admin_instancia::get_proyecto_defecto();
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());			
		}		
	}

	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	function get_menus_disponibles()
	{
		return consultas_instancia::get_menus_existentes($this->s__proyecto);
	}
	
	function get_perfiles_disponibles()
	{
		return consultas_instancia::get_lista_grupos_acceso_proyecto($this->s__proyecto);
	}	
	
	//-----------------------------------------------------------------------------------
	//---- Evt CI -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
		$menu = quote($this->datos['id_menu']);
		$perfiles = quote($this->datos['ids_perfil_funcional']);
		$proyecto = quote($this->s__proyecto);
		toba::db()->abrir_transaccion();
		try {
			//Primero le quito el menu a todos los grupos de acceso que actualmente lo usan
			$sql = "UPDATE apex_usuario_grupo_acc SET menu_usuario = null
					WHERE proyecto = $proyecto AND menu_usuario = $menu;";
			toba::db()->ejecutar($sql);
			
			//Le agrego el menu a los grupos existentes
			foreach ($perfiles as $grupo) {
				$sql = "UPDATE apex_usuario_grupo_acc SET menu_usuario = $menu 
						WHERE proyecto = $proyecto AND usuario_grupo_acc = $grupo;";
				toba::logger()->debug($sql);
				toba::db()->ejecutar($sql);
			}
			toba::db()->cerrar_transaccion();				
		} catch (toba_error_db $e) {
			toba::db()->abortar_transaccion();
			toba::logger()->debug($e->getMessage());
			throw new toba_error_usuario('Hubo un inconveniente al actualizar los datos');
		}
		$this->set_pantalla('pant_inicial');
	}
	
	function evt__cancelar()
	{
		$this->set_pantalla('pant_inicial');
	}
	
	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form(toba_ei_formulario $form)
	{
		if (isset($this->s__menu_id)) {
			$perfiles = consultas_instancia::get_lista_grupos_acceso(array('proyecto' => $this->s__proyecto, 'menu' => $this->s__menu_id));
			$ids = array_column($perfiles, 'usuario_grupo_acc');			
			$form->set_datos(array('id_menu' => $this->s__menu_id, 'ids_perfil_funcional' => $ids));
		}
	}

	function evt__form__modificacion($datos)
	{
		$this->datos = $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function evt__filtro__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->set_proyecto($datos['proyecto']);
	}
	
	function evt__filtro__cancelar()
	{
		unset($this->s__filtro);
		unset($this->s__proyecto);
	}
	
	function conf__filtro($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}
	
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos($this->get_menus_disponibles());
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->s__menu_id = $seleccion['menu_id'];
		$this->set_pantalla('pant_edicion');
	}
	
	
	

}
?>