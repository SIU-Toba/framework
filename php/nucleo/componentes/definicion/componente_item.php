<?php
require_once('interfaces.php');

class componente_item implements definicion_toba
{
	static function get_estructura()
	{
		$estructura[0]['tabla'] = 'apex_item';
		$estructura[0]['registros'] = '1';
		$estructura[0]['obligatorio'] = true;
		$estructura[1]['tabla'] = 'apex_item_info';
		$estructura[1]['registros'] = '1';
		$estructura[1]['obligatorio'] = false;
		$estructura[2]['tabla'] = 'apex_item_objeto';
		$estructura[2]['registros'] = 'n';
		$estructura[2]['obligatorio'] = false;
		return $estructura;		
	}
	
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql['basica']['sql'] = "SELECT	i.proyecto as			item_proyecto,	
						i.item as								item,	
						i.nombre	as							item_nombre,
						i.descripcion as						item_descripcion,	
						i.actividad_buffer_proyecto as			item_act_buffer_proyecto,
						i.actividad_buffer as					item_act_buffer,	
						i.actividad_patron_proyecto as			item_act_patron_proyecto,
						i.actividad_patron as					item_act_patron,	
						p.archivo as							item_act_patron_script,	
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
						pt.clase_nombre	as						tipo_pagina_clase,
						pt.clase_archivo as						tipo_pagina_archivo,
						pt.include_arriba	as					item_include_arriba,	
						pt.include_abajo as						item_include_abajo,	
						i.zona_proyecto as						item_zona_proyecto,
						i.zona as								item_zona,
						z.archivo as							item_zona_archivo,
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
							WHERE padre = i.item AND proyecto = i.proyecto AND solicitud_tipo <> 'fantasma' AND item != '') as cant_items_hijos						
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_zona z	ON	( i.zona_proyecto	= z.proyecto AND i.zona	= z.zona	)
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item),
						apex_patron	p,	
						apex_pagina_tipo pt	
				WHERE		pt.pagina_tipo	= i.pagina_tipo
				AND		pt.proyecto	= i.pagina_tipo_proyecto
				AND		i.actividad_patron =	p.patron	
				AND		i.actividad_patron_proyecto =	p.proyecto
				AND		i.proyecto = '$proyecto'";
		if ( isset($componente) ) {
			$sql['basica']['sql'] .= "	AND		i.item ='$componente' ";	
		}
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
						apex_objeto	o,	
						apex_fuente_datos	d,	
						apex_clase c
				WHERE	io.objeto =	o.objeto	
					 AND		io.proyecto	= o.proyecto
				AND		o.clase = c.clase	
				AND		o.clase_proyecto = c.proyecto	
				AND		o.fuente_datos	= d.fuente_datos
				AND		o.fuente_datos_proyecto	= d.proyecto
				AND		io.proyecto	= '$proyecto'";
		if ( isset($componente) ) {
			$sql['objetos']['sql'] .= "	AND		io.item ='$componente' ";	
		}
		$sql['objetos']['sql'] .= "	ORDER	BY	io.orden;";	
		$sql['objetos']['registros']='n';
		$sql['objetos']['obligatorio']=false;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo';
	}

	static function get_nombre_clase_info()
	{
		return 'info_item';
	}	
}
?>