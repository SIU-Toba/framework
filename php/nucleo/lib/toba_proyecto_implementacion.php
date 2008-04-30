<?php

class toba_proyecto_implementacion
{
	/**
	*	Devuelve los grupos de acceso de un usuario para un proyecto
	*/
	function get_grupos_acceso($usuario, $proyecto)
	{
		$sql = "SELECT	up.usuario_grupo_acc as 				grupo_acceso
				FROM 	apex_usuario_proyecto up,
						apex_usuario_grupo_acc ga
				WHERE	up.usuario_grupo_acc = ga.usuario_grupo_acc
				AND		up.proyecto = ga.proyecto
				AND		up.usuario = '$usuario'
				AND		up.proyecto = '$proyecto';";
		$datos = toba::instancia()->get_db()->consultar($sql);
		if($datos){
			$grupos = array();
			foreach($datos as $dato) {
				$grupos[] = $dato['grupo_acceso'];
			}
			return $grupos;
		} else {
			return array();
		}
	}
	
	/**
	*	Devuelve el perfil de datos de un usuario
	*/
	function get_perfil_datos($usuario, $proyecto)
	{
		$sql = "SELECT 	up.usuario_perfil_datos as 		perfil_datos,
					FROM apex_usuario_proyecto up
					WHERE up.usuario = '$usuario'
					AND up.proyecto = '$proyecto';";
		$datos = toba::instancia()->get_db()->consultar_fila($sql);
		if(!$datos) return null;
		return $datos['perfil_datos'];
	}
	

	/**
	*	Devuelve las restricciones correspondientes al perfil de datos del usuario
	*/
	function get_perfil_datos_restricciones($perfil)
	{
		$sql = "SELECT 	d.usuario_perfil_datos as 		perfil_datos,
						d.dimension as 					dimension,
						d.clave as 						clave
					FROM  apex_usuario_perfil_datos_dims d
					WHERE d.usuario_perfil_datos = '$perfil'";
		return toba::instancia()->get_db()->consultar($sql);
	}

}

?>