<?php

class ci_editor_simple extends toba_ci
{	
	const clave_falsa = "xS34Io9gF2JD";					//La clave no se envia al cliente
	
	protected $s__proyecto;
	protected $s__usuario;

	function datos($tabla)
	{
		return	$this->controlador->dep('datos')->tabla($tabla);
	}
	
	function conf()
	{
		if( toba::sesion()->proyecto_esta_predefinido() ) {
			$this->s__proyecto = toba::sesion()->get_id_proyecto();
		}
		$usuario = $this->datos('basica')->get();
		$this->s__usuario = $usuario['usuario'];
		$desc = '';
		$desc .= 'Proyecto: <strong>' . $this->s__proyecto . '</strong><br>';		
		if ($this->controlador->dep('datos')->esta_cargada()) {
			$this->dep('basica')->ef('usuario')->set_solo_lectura(true);
			$desc .= 'Usuario:&nbsp;&nbsp; <strong>' . $usuario['nombre'] . '</strong><br>';
		} else {
			$this->controlador->pantalla()->eliminar_evento('eliminar');
		}
		$this->pantalla()->set_descripcion($desc);
	}
	
	
	//---- Info BASICA -------------------------------------------------------

	function evt__basica__modificacion($datos)
	{
		if (isset($datos['clave']) && $datos['clave'] == self::clave_falsa ) {
			unset($datos['clave']);	
		}
		$this->datos('basica')->set($datos);
		$id = $this->datos('proyecto')->get_id_fila_condicion(array('proyecto'=>$this->s__proyecto));
		foreach ($id as $clave){
			$this->datos('proyecto')->eliminar_fila($clave);
		}
		
		$fila = array();
		$fila['proyecto'] = $this->s__proyecto;
		$fila['usuario'] = $this->s__usuario;

		// Perfil Funcional		
		foreach ($datos['usuario_grupo_acc'] as $id=>$grupo_acceso){
			$fila['usuario_grupo_acc'] = $grupo_acceso;
			$this->datos('proyecto')->nueva_fila($fila);
		}

		// Perfil de datos
		if ( isset($datos['usuario_perfil_datos']) ) {
			$fila_pd['usuario'] = $this->s__usuario;
			$fila_pd['proyecto'] = $this->s__proyecto;
			$fila_pd['usuario_perfil_datos'] = $datos['usuario_perfil_datos'];
			$this->datos('proyecto_pd')->set($fila_pd);
		} else {
			$this->datos('proyecto_pd')->set(null);
		}
	}

	function conf__basica($componente)
	{
		$datos = $this->datos('basica')->get();
		if (isset($datos)) {
			$datos['clave'] = self::clave_falsa;
	
			// Perfil Funcional		
			$grupo_acc = $this->datos('proyecto')->get_filas( array('usuario'=> $this->s__usuario, 'proyecto'=>$this->s__proyecto));
			$ga_seleccionados = array();
			foreach ($grupo_acc as $i=>$ga){
				$ga_seleccionados[] = $ga['usuario_grupo_acc'];
			}
			$datos['usuario_grupo_acc'] = $ga_seleccionados;

			// Perfil de datos
			$datos_pd = $this->datos('proyecto_pd')->get();
			$datos['usuario_perfil_datos'] = $datos_pd['usuario_perfil_datos'];
		}
		
		$datos['proyecto'] = $this->s__proyecto;
		$componente->set_datos($datos);
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