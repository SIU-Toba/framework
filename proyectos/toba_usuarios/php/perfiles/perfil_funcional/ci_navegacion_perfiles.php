<?php 
require_once('lib/consultas_instancia.php');

class ci_navegacion_perfiles extends toba_ci
{
	protected $s__filtro;
	
	function ini__operacion()
	{
		if (! is_null(admin_instancia::get_proyecto_defecto())) {
			$this->s__filtro = array('proyecto' => admin_instancia::get_proyecto_defecto());
		}		
	}
	
	function datos($tabla)
	{
		return $this->dep('datos')->tabla($tabla);
	}
	
	function conf__seleccion_perfil()
	{
		if (!isset($this->s__filtro)) {
			$this->pantalla('seleccion_perfil')->eliminar_evento('agregar');
		}
	}
	
	function conf__edicion_perfil()
	{
		//-- Si es una instalación de producción avisar que los cambios se aplicaran solo a esta instalacion y no al proyecto/personalizacion
		admin_instancia::chequear_usar_perfiles_propios($this->dep('editor_perfiles')->get_proyecto(), $this->pantalla());
	}
	
	function actualizar_info_ini()
	{
		admin_instancia::set_usar_perfiles_propios($this->dep('editor_perfiles')->get_proyecto());
	}
	
	function evt__guardar()
	{
		$this->dep('datos')->persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		//- Sincronizar la relacion
		if ($this->dep('datos')->esta_cargada()) {
			$alta = false;
		}else{
			$alta = true;
		}
		$this->dep('datos')->sincronizar();
		//- Sincroniza el arbol de items		
		$this->dep('editor_perfiles')->guardar_arbol_items($alta);
		$this->dep('datos')->resetear();
		toba::db()->cerrar_transaccion();
		
		//-- Si estamos en produccion guardamos un flag indicando que cambiaron los perfiles y ahora se encarga el proyecto de manejarlos
		$this->actualizar_info_ini();
				
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__volver()
	{
		$this->dep('datos')->resetear();
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__eliminar()
	{
		$this->dep('datos')->persistidor()->desactivar_transaccion();
		toba::db()->abrir_transaccion();
		$datos = $this->datos('accesos')->get();
		$this->dep('datos')->eliminar();
		$this->dep('datos')->resetear();
		//- Elimino el acceso a los items
		$sql = "DELETE FROM 
					apex_usuario_grupo_acc_item 
				WHERE 
						usuario_grupo_acc = '{$datos['usuario_grupo_acc']}'
					AND proyecto = '{$datos['proyecto']}';";
		toba::db()->ejecutar($sql);
		toba::db()->cerrar_transaccion();
		
		//-- Si estamos en produccion guardamos un flag indicando que cambiaron los perfiles y ahora se encarga el proyecto de manejarlos
		$this->actualizar_info_ini();
		
		$this->dep('editor_perfiles')->cortar_arbol();
		$this->set_pantalla('seleccion_perfil');
	}
	
	function evt__agregar()
	{
		$this->dep('editor_perfiles')->set_proyecto($this->s__filtro['proyecto']);
		$this->set_pantalla('edicion_perfil');
	}
	
	function conf__cuadro_grupos_acceso($componente)
	{
		if (isset($this->s__filtro)) {
			$datos = consultas_instancia::get_lista_grupos_acceso_proyecto($this->s__filtro['proyecto']);
			$componente->set_datos($datos);
		}
	}
	
	function evt__cuadro_grupos_acceso__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->dep('editor_perfiles')->set_proyecto($seleccion['proyecto']);
		$this->dep('editor_perfiles')->set_perfil_funcional($seleccion['usuario_grupo_acc']);
		$this->set_pantalla('edicion_perfil');
	}
	
	function conf__form_datos_perfil($componente)
	{
		$datos = array();
		if ($this->datos('accesos')->hay_cursor()) {
			$datos = $this->datos('accesos')->get();
			$componente->set_solo_lectura( array('usuario_grupo_acc') );
		}else{
			$datos['proyecto'] = $this->s__filtro['proyecto'];
		}	
		$componente->set_datos($datos);
	}
	
	function evt__form_datos_perfil__modificacion($datos)
	{
		$this->datos('accesos')->set($datos);
	}
	
	function evt__filtro_proyectos__filtrar($datos)
	{
		$this->s__filtro = $datos;
	}
	
	function evt__filtro_proyectos__cancelar()
	{
		unset($this->s__filtro);
	}
	
	function conf__filtro_proyectos($componente)
	{
		if (isset($this->s__filtro)) {
			$componente->set_datos($this->s__filtro);
		}		
	}
	
}

?>