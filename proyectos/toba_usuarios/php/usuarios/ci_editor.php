<?php

class ci_editor extends toba_ci
{	
	const clave_falsa = "xS34Io9gF2JD";					//La clave no se envia al cliente
	
	protected $s__proyecto;
	protected $s__usuario;

	function datos($tabla)
	{
		return	$this->controlador->dep('datos')->tabla($tabla);
	}
	
	function limpiar_datos()
	{
		unset($this->s__proyecto);
	}
	
	function conf()
	{
		$usuario = $this->datos('basica')->get();
		$this->s__usuario = $usuario['usuario'];
		$desc = 'Usuario: <strong>' . $usuario['nombre'] . '</strong>';
		$this->pantalla()->set_descripcion($desc);
	}
	
	function conf__proyecto()
	{
		if (!isset($this->s__proyecto)) {
			$this->pantalla('proyecto')->eliminar_dep('form_proyectos');	
		}
	}

	//---- Info BASICA -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		if ($datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->datos('basica')->set($datos);
	}

	function conf__basica()
	{
		$datos = $this->datos('basica')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
		}
		return $datos;
	}

	//---- Asociacion a PROYECTOS -------------------------------------------------

	function evt__proyecto__salida()
	{
		$this->datos('proyecto')->resetear_cursor();		
	}

	function evt__cuadro_proyectos__seleccion($seleccion)
	{
		$this->s__proyecto = $seleccion['proyecto'];
	}
	
	function conf__cuadro_proyectos($componente)
	{	
		$proyectos = consultas_instancia::get_lista_proyectos();
		foreach ($proyectos as $id => $proyecto){
			$grupos_acceso = $this->datos('proyecto')->get_filas(array('proyecto' => $proyecto['proyecto']));
			$grupos = array();
			$perfil = '';
			foreach ($grupos_acceso as $ga){
				$grupos[] = $ga['grupo_acceso'];
				if (isset($ga['perfil_datos_nombre'])) {
					$perfil = $ga['perfil_datos_nombre'];	
				}
			}
			//truchada... capaz la forma de cargar el cuadro ya es una truchada :S
			$proyectos[$id]['grupos_acceso'] = empty($grupos) ? 'Sin Acceso' : implode(', ', $grupos);
			$proyectos[$id]['perfil_datos'] = empty($perfil) ? '&nbsp;' : $perfil;
		}
		$componente->set_datos($proyectos);
	}

	function evt__form_proyectos__modificacion($datos)
	{
		if (isset($datos['clave']) && $datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto'=>$this->s__proyecto));
		foreach ($id as $clave){
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		
		$fila = array();
		$fila['proyecto'] = $this->s__proyecto;
		$fila['usuario'] = $this->s__usuario;
		$fila['usuario_perfil_datos'] = isset($datos['usuario_perfil_datos']) ? $datos['usuario_perfil_datos'] : '';	
		foreach ($datos['usuario_grupo_acc'] as $id=>$grupo_acceso){
			$fila['usuario_grupo_acc'] = $grupo_acceso;
			$this->datos('proyecto')->nueva_fila($fila);
		}
		$this->limpiar_datos();
	}

	function evt__form_proyectos__baja()
	{
		$id = $this->datos('proyecto')->get_id_fila_condicion( array('proyecto' => $this->s__proyecto) );
		foreach ($id as $clave){
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		$this->limpiar_datos();
	}
	
	function evt__form_proyectos__cancelar()
	{
		unset($this->s__proyecto);
	}
	
	function conf__form_proyectos($componente)
	{
		if (isset($this->s__proyecto)) {
			$grupo_acc = $this->datos('proyecto')->get_filas( array('usuario'=> $this->s__usuario, 'proyecto'=>$this->s__proyecto));
		
			$perfil_datos = '';
			$ga_seleccionados = array();
			foreach ($grupo_acc as $i=>$ga){
				$ga_seleccionados[] = $ga['usuario_grupo_acc'];
				$perfil_datos = $ga['usuario_perfil_datos'];
			}
			$datos['proyecto'] = $this->s__proyecto;
			$datos['usuario_grupo_acc'] = $ga_seleccionados;
			$datos['usuario_perfil_datos'] = $perfil_datos;
			$datos['clave'] = self::clave_falsa;
			$componente->set_datos($datos);
		}
	}
		
	//---- Consultas ---------------------------------------------------
	
	function get_lista_grupos_acceso_proyecto()
	{
		$sql = "SELECT 	usuario_grupo_acc,
						nombre,
						descripcion
				FROM 	apex_usuario_grupo_acc
				WHERE 	proyecto = '{$this->s__proyecto}';";
		return toba::db()->consultar($sql);
	}
	
}
?>