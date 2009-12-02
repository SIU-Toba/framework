<?php

class toba_item_perfil_def extends  toba_item_perfil 
{
	
	function get_vista_item($proyecto, $grupo_acceso, $componente=null)
	{
		$proyecto = self::$db->quote($proyecto);
		if (isset($componente)) {
			$componente = self::$db->quote($componente);
		}				
		$sql['basica']['sql'] = "SELECT	i.proyecto as			item_proyecto,	
						i.item as								item,	
						i.nombre	as							item_nombre,
						i.descripcion as						item_descripcion,	
						i.solicitud_tipo as						item_solic_tipo,	
						i.solicitud_registrar as				item_solic_registrar,
						i.imagen_recurso_origen as				item_imagen_recurso_origen,
						i.imagen as								item_imagen,
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
						a.usuario_grupo_acc	as					grupo_acceso,
						
						(SELECT COUNT(*) FROM apex_item_objeto 
							WHERE item = i.item AND proyecto = i.proyecto) as cant_dependencias,
						(SELECT COUNT(*) FROM apex_item 
							WHERE padre = i.item AND proyecto = i.proyecto AND (solicitud_tipo <> 'fantasma' OR solicitud_tipo IS NULL) AND item != i.item) as cant_items_hijos,
						m.molde as molde,
						i.retrasar_headers		as retrasar_headers
				FROM	apex_item i	
							LEFT OUTER JOIN apex_item_info ii ON (i.proyecto = ii.item_proyecto AND i.item = ii.item)
							LEFT OUTER JOIN apex_molde_operacion m ON (i.item = m.item AND i.proyecto = m.proyecto)
							LEFT OUTER JOIN apex_usuario_grupo_acc_item a ON (i.item = a.item AND i.proyecto = a.proyecto AND a.usuario_grupo_acc = '$grupo_acceso')
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
			$sql['objetos']['sql'] .= "	AND		io.item =$componente ";	
		}
		$sql['objetos']['sql'] .= "	ORDER	BY	io.orden;";	
		$sql['objetos']['registros']='n';
		$sql['objetos']['obligatorio']=false;
		
		return $sql;
	}
}

?>