<?php
class ci_editor_perfiles extends toba_ci
{
	protected $s__proyecto;
	protected $s__grupo_acceso = '';
	protected $s__arbol_cargado = false;
	
	function datos($tabla)
	{
		return	$this->controlador->dep('datos')->tabla($tabla);
	}
	
	function set_proyecto($proyecto)
	{
		$this->s__proyecto = $proyecto;
	}
	
	function get_proyecto()
	{
		return $this->s__proyecto;
	}
	
	function set_perfil_funcional($perfil_funcional)
	{
		$this->s__grupo_acceso = $perfil_funcional;
	}
	
	function cortar_arbol()
	{
		$this->s__grupo_acceso = '';
		unset($this->s__arbol_cargado);
	}
	
	function guardar_arbol_items($alta)
	{
		$raices = $this->dep('arbol_perfiles')->get_datos();
		$datos = $this->datos('accesos')->get();
		if (isset($datos) && !is_null($raices)) {
			foreach ($raices as $raiz) {
				if ($alta) {
					$raiz->set_grupo_acceso($datos['usuario_grupo_acc']);
				}
				$raiz->sincronizar();	
			}
		}
		unset($this->s__arbol_cargado);
	}
	
	//- Dependencias -
	
	function conf__arbol_perfiles($arbol) 
	{
		if (! isset($this->s__arbol_cargado) || !$this->s__arbol_cargado) {
			$catalogador = new toba_catalogo_items_perfil($this->s__proyecto, $this->s__grupo_acceso);
			$catalogador->cargar_todo();
			$raiz = $catalogador->buscar_carpeta_inicial();
			$arbol->set_datos(array($raiz), true);
			$this->s__arbol_cargado = true;
		}
	}
	
	function conf__form_restricciones($componente)
	{
		$datos = $this->datos('restricciones')->get_filas();
		$datos['restriccion'] = rs_convertir_asociativo($datos, array('restriccion_funcional'), 'restriccion_funcional');
		$componente->set_datos($datos);
	}
	
	function evt__form_restricciones__modificacion($datos)
	{
		$this->datos('restricciones')->eliminar_filas();
		$restricciones = $datos['restriccion'];
		$fila = array();
		foreach ($restricciones as $id=>$restriccion) {
			$fila['proyecto'] = $this->s__proyecto;
			$fila['restriccion_funcional'] = $restriccion;
			$fila['usuario_grupo_acc'] = $this->s__grupo_acceso;
			$this->datos('restricciones')->nueva_fila($fila);
		}
	}
	
	function conf__form_permisos($componente)
	{
		$datos = $this->datos('permisos')->get_filas();
		$datos['permiso'] = rs_convertir_asociativo($datos, array('permiso'), 'permiso');
		$componente->set_datos($datos);
	}
	
	function evt__form_permisos__modificacion($datos)
	{
		$this->datos('permisos')->eliminar_filas();
		$permisos = $datos['permiso'];
		$fila = array();
		foreach ($permisos as $id=>$permiso) {
			$fila['proyecto'] = $this->s__proyecto;
			$fila['permiso'] = $permiso;
			$fila['usuario_grupo_acc'] = $this->s__grupo_acceso;
			$this->datos('permisos')->nueva_fila($fila);
		}
	}
	
	
	//-----------------------------------------------------------------------------------
	//---- form_membresia ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_membresia(toba_ei_formulario $form)
	{
		$filas = $this->datos('membresia')->get_filas();
		$datos = array();
		foreach ($filas as $fila) {
			$datos[] = $fila['usuario_grupo_acc_pertenece'];
		}
		return array('perfiles' => $datos);
	}

	function evt__form_membresia__modificacion($datos)
	{
		//Elimina todas las membresias y agrega las nuevas
		$this->datos('membresia')->eliminar_filas();
		foreach ($datos['perfiles'] as $fila) {
			$this->datos('membresia')->nueva_fila(array('usuario_grupo_acc_pertenece' => $fila));
		}
	}

	//- Consultas -
	
	function get_lista_restricciones_proyecto()
	{
		return toba_info_permisos::get_restricciones_proyecto($this->s__proyecto);
	}
	
	function get_lista_permisos_proyecto()
	{
		return toba_info_permisos::get_lista_permisos(array('proyecto' => $this->s__proyecto));
	}
	
	function get_lista_perfiles_disponibles()
	{
		//Permitir seleccionar sólo aquellos que no creen ciclos
		$datos = $this->datos('accesos')->get();
		$perfil = null;
		if (isset($datos) && !is_null($datos['usuario_grupo_acc'])) {
			$perfil = $datos['usuario_grupo_acc'];
		}		
		return toba_info_permisos::get_perfiles_funcionales_pueden_ser_miembros($this->s__proyecto, $perfil);
	}
	


}
?>