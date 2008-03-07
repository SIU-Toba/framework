<?php

class ci_editor extends toba_ci
{	
	const clave_falsa = "xS34Io9gF2JD";					//La clave no se envia al cliente
	
	protected $s__filtro;
	protected $s__usuario;

	function datos($tabla)
	{
		return	$this->controlador->dep('datos')->tabla($tabla);
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
		if (!isset($this->s__filtro)) {
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

	function evt__cuadro_proyectos__eliminar($seleccion)
	{
		$this->datos('proyecto')->eliminar_fila($seleccion);
	}
	
	function conf__cuadro_proyectos($componente)
	{
		$componente->set_datos($this->datos('proyecto')->get_filas());
	}

	function evt__form_proyectos__modificacion($datos)
	{
		if (isset($datos['clave']) && $datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto'=>$this->s__filtro['proyecto']));
		foreach ($id as $clave){
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		
		$fila = array();
		$fila['proyecto'] = $this->s__filtro['proyecto'];
		$fila['usuario'] = $this->s__usuario;
		$fila['usuario_perfil_datos'] = $datos['usuario_perfil_datos'];
		foreach ($datos['usuario_grupo_acc'] as $id=>$grupo_acceso){
			$fila['usuario_grupo_acc'] = $grupo_acceso;
			$this->datos('proyecto')->nueva_fila($fila);
		}
		$this->evt__filtro_proyectos__cancelar();
	}

	function evt__form_proyectos__baja()
	{
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto'=>$this->s__filtro['proyecto']));
		foreach ($id as $clave){
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		$this->evt__filtro_proyectos__cancelar();
	}
	
	function evt__form_proyectos__cancelar()
	{
		$this->evt__filtro_proyectos__cancelar();
	}
	
	function conf__form_proyectos($componente)
	{
		if (isset($this->s__filtro)) {
			$grupo_acc = $this->datos('proyecto')->get_filas( array('usuario'=> $this->s__usuario, 'proyecto'=>$this->s__filtro['proyecto']));
		
			$seleccionados = array();
			foreach ($grupo_acc as $id=>$ga){
				$seleccionados[] = $ga['usuario_grupo_acc'];
			}
			$datos['proyecto'] = $this->s__filtro['proyecto'];
			$datos['usuario_grupo_acc'] = $seleccionados;
			$datos['usuario_perfil_datos'] = $ga['usuario_perfil_datos'];
			$datos['clave'] = self::clave_falsa;
			$componente->set_datos($datos);
		}
	}
	
	//---- Componentes auxiliares --------------------------------------
	
	function  conf__filtro_proyectos()
	{
		if ( isset($this->s__filtro) ) {
			return $this->s__filtro;
		}
	}
	
	function evt__filtro_proyectos__filtrar($filtro)
	{
		$this->s__filtro = $filtro;
	}
	
	function evt__filtro_proyectos__cancelar()
	{
		unset($this->s__filtro);
	}
	
	//---- Consultas ---------------------------------------------------
	
	function get_lista_grupos_acceso_proyecto()
	{
		$sql = "SELECT 	usuario_grupo_acc,
						nombre,
						descripcion
				FROM 	apex_usuario_grupo_acc
				WHERE 	proyecto = '{$this->s__filtro['proyecto']}';";
		return toba::db()->consultar($sql);
	}
	
}
?>