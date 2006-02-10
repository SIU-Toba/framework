<?
require_once("componente.php");

class componente_filtro extends componente_toba
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[2]['tabla'] = 'apex_objeto_filtro';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = true;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$consumo_web = defined('apex_solicitud_tipo');
		$sql = parent::get_vista_extendida($proyecto, $componente);	
		$sql["info_dimensiones"]["sql"] = "SELECT	g.dimension_grupo as	grupo,
						g.nombre as		 				grupo_nombre,
						g.descripcion as 				grupo_des,
						d.dimension as 				dimension,
						d.fuente_datos as 			fuente,
						d.nombre as 					nombre,
						d.descripcion as				descripcion,
						d.dimension_tipo as		 	tipo,
						d.inicializacion as			inicializacion,
						f.etiqueta as					etiqueta,
						f.tabla as 						tabla,
						f.columna as 					columna,
						f.requerido as 				obligatorio,
						f.no_interactivo as			no_interactivo,
						f.predeterminado as 			predeterminado,
						u.usuario_perfil_datos as	perfil
				FROM 	apex_objeto_filtro f,
						apex_dimension d
						LEFT OUTER JOIN apex_dimension_grupo g ON d.dimension_grupo = g.dimension_grupo ";
		if ( $consumo_web ) {
			// Filtrar dimensiones por perfil
			$perfil = toba::get_hilo()->obtener_usuario_perfil_datos();
			$sql["info_dimensiones"]["sql"] .= " LEFT OUTER JOIN apex_dimension_perfil_datos u 
													ON (d.dimension = u.dimension)
														AND (u.usuario_perfil_datos = '$perfil') ";
		}
		$sql["info_dimensiones"]["sql"] .= " WHERE	f.dimension = d.dimension
				AND		f.dimension_proyecto = d.proyecto
				AND		objeto_filtro_proyecto = '$proyecto' ";
		if ( isset($componente) ) {
			$sql["info_dimensiones"]["sql"] .= " AND     objeto_filtro = '$componente' ";
		}
		$sql["info_dimensiones"]["sql"] .= " ORDER BY g.orden, f.orden; ";
		$sql["info_dimensiones"]["registros"]='n';
		$sql["info_dimensiones"]['obligatorio']=true;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime/transversales';
	}
}
?>