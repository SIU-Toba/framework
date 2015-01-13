<?php

class toba_item_def implements toba_componente_definicion
{
	static protected $db;
		
	static function get_estructura()
	{
		$estructura[] = array( 	'tabla' => 'apex_item',
								'registros' => '1',
								'obligatorio' => true );
		$estructura[] = array( 	'tabla' => 'apex_item_info',
								'registros' => '1',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_item_objeto',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_item_permisos_tablas',
								'registros' => 'n',
								'obligatorio' => false );								
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}
		$sql['basica']['sql'] = "SELECT	i.proyecto as			item_proyecto,	
						i.item as								item,	
						i.nombre	as							item_nombre,
						i.descripcion as						item_descripcion,	
						i.actividad_buffer_proyecto as			item_act_buffer_proyecto,
						i.actividad_buffer as					item_act_buffer,	
						i.actividad_patron_proyecto as			item_act_patron_proyecto,
						i.actividad_patron as					item_act_patron,	
						i.actividad_accion as					item_act_accion_script,	
						i.solicitud_tipo as						item_solic_tipo,	
						i.solicitud_registrar as				item_solic_registrar,
						i.solicitud_obs_tipo_proyecto	as		item_solic_obs_tipo_proyecto,	
						i.solicitud_obs_tipo	as				item_solic_obs_tipo,	
						i.solicitud_observacion	as				item_solic_observacion,	
						i.solicitud_registrar_cron	as			item_solic_cronometrar,	
						i.parametro_a as						item_parametro_a,	
						i.parametro_b as						item_parametro_b,	
						i.parametro_c as						item_parametro_c,
						i.imagen_recurso_origen as				item_imagen_recurso_origen,
						i.imagen as								item_imagen,
						i.punto_montaje as						punto_montaje,
						pt.punto_montaje as						tipo_pagina_punto_montaje,
						pt.clase_nombre	as						tipo_pagina_clase,
						pt.clase_archivo as						tipo_pagina_archivo,
						pt.include_arriba	as					item_include_arriba,	
						pt.include_abajo as						item_include_abajo,	
						i.zona_proyecto as						item_zona_proyecto,
						i.zona as								item_zona,
						z.punto_montaje as						zona_punto_montaje,
						z.archivo as							item_zona_archivo,
						z.consulta_archivo as 					zona_cons_archivo,
						z.consulta_clase as						zona_cons_clase,
						z.consulta_metodo as					zona_cons_metodo,
						i.publico as							item_publico,
						ii.item as								item_existe_ayuda,
						i.carpeta as							carpeta,
						i.menu as								menu,
						i.orden as								orden,
						i.publico as							publico,
						i.redirecciona as						redirecciona,
						i.solicitud_registrar_cron as			crono,
						i.solicitud_tipo as						solicitud_tipo,
						i.padre	as 								item_padre,
						(SELECT COUNT(*) FROM apex_item_objeto 
							WHERE item = i.item AND proyecto = i.proyecto) as cant_dependencias,
						(SELECT COUNT(*) FROM apex_item 
							WHERE padre = i.item AND proyecto = i.proyecto AND (solicitud_tipo <> 'fantasma' OR solicitud_tipo IS NULL) AND item != i.item) as cant_items_hijos,
						m.molde as molde,
						i.retrasar_headers		as retrasar_headers
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_zona z	ON	( i.zona_proyecto	= z.proyecto AND i.zona	= z.zona	)
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item)
							LEFT OUTER JOIN	apex_pagina_tipo pt	ON (pt.pagina_tipo	= i.pagina_tipo	AND	pt.proyecto	= i.pagina_tipo_proyecto)
							LEFT OUTER JOIN apex_molde_operacion m ON (i.item = m.item AND i.proyecto = m.proyecto)
				WHERE	i.proyecto = $proyecto";
		if ( isset($componente) ) {
			$sql['basica']['sql'] .= "	AND		i.item =$componente ";	
		}
		$sql['basica']['sql'] .= "	ORDER BY i.item; ";		
		$sql['basica']['registros']='1';	
		$sql['basica']['obligatorio']=true;
		$sql['objetos']['sql'] =	"SELECT	o.proyecto as		objeto_proyecto,
						o.objeto	as						objeto,
						o.nombre	as						objeto_nombre,
						o.subclase as						objeto_subclase,
						o.subclase_archivo as				objeto_subclase_archivo,
						io.orden	as						orden,	
						c.proyecto as					  	clase_proyecto,	
						c.clase as						  	clase,	
						c.archivo as					  	clase_archivo,
						d.proyecto as						fuente_proyecto,
						d.fuente_datos	as				  	fuente,
						d.fuente_datos_motor	as			fuente_motor,
						d.host as						  	fuente_host,	
						d.usuario as					  	fuente_usuario,	
						d.clave as						  	fuente_clave,
						d.base as						  	fuente_base
				FROM	apex_item_objeto io,	
						apex_objeto	o LEFT OUTER JOIN apex_fuente_datos d ON (
									o.fuente_datos	= d.fuente_datos AND
									o.fuente_datos_proyecto	= d.proyecto),
						apex_clase c
				WHERE	
							io.objeto =	o.objeto	
					AND		io.proyecto	= o.proyecto
					AND		o.clase = c.clase	
					AND		o.clase_proyecto = c.proyecto	
					AND		io.proyecto	= $proyecto";
		if ( isset($componente) ) {
			$sql['objetos']['sql'] .= "	AND		io.item =$componente";	
		}
		$sql['objetos']['sql'] .= "	ORDER	BY	io.orden;";	
		$sql['objetos']['registros']='n';
		$sql['objetos']['obligatorio']=false;
		return $sql;
	}

	static function get_vista_extendida_resumida($proyecto, $componente=null)
	{
		return self::get_vista_extendida($proyecto, $componente);
	}
	
	static function set_db($db)
	{
		self::$db = $db;
	}		
}
?>