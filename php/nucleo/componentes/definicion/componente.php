<?php
require_once('interfaces.php');

class componente_toba implements definicion_toba
{
	static function get_estructura()
	{
		$estructura[0]['tabla'] = 'apex_objeto';
		$estructura[0]['registros'] = '1';
		$estructura[0]['obligatorio'] = true;
		$estructura[1]['tabla'] = 'apex_objeto_info';
		$estructura[1]['registros'] = '1';
		$estructura[1]['obligatorio'] = false;
		return $estructura;		
	}

	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql['info']['sql'] = "	SELECT	o.proyecto          		as proyecto,                 
									o.objeto                    	as objeto,                   
									o.anterior                  	as anterior,                 
									o.reflexivo                 	as reflexivo,                
									o.clase_proyecto            	as clase_proyecto,           
									o.clase                     	as clase,                    
									o.subclase                  	as subclase,                 
									o.subclase_archivo          	as subclase_archivo,         
									o.objeto_categoria_proyecto 	as objeto_categoria_proyecto,
									o.objeto_categoria          	as objeto_categoria,         
									o.nombre                    	as nombre,                   
									o.titulo                    	as titulo,                   
									o.colapsable                	as colapsable,               
									o.descripcion               	as descripcion,              
									o.fuente_datos_proyecto     	as fuente_proyecto,    
									o.fuente_datos              	as fuente,             
									o.solicitud_registrar       	as solicitud_registrar,      
									o.solicitud_obj_obs_tipo    	as solicitud_obj_obs_tipo,   
									o.solicitud_obj_observacion 	as solicitud_obj_observacion,
									o.parametro_a               	as parametro_a,              
									o.parametro_b               	as parametro_b,              
									o.parametro_c                	as parametro_c,              
									o.parametro_d               	as parametro_d,              
									o.parametro_e               	as parametro_e,              
									o.parametro_f               	as parametro_f,              
									o.usuario                   	as usuario,                  
									o.creacion                  	as creacion,        
									c.editor_proyecto 				as clase_editor_proyecto,
									c.editor_item 					as clase_editor_item,
									c.archivo 						as clase_archivo,
									c.vinculos 	 					as clase_vinculos,
									c.editor_item 					as clase_editor,
									c.icono 						as clase_icono,
									c.descripcion_corta				as clase_descripcion_corta,
									c.instanciador_proyecto			as clase_instanciador_proyecto,
									c.instanciador_item 			as clase_instanciador_item,
									oi.objeto 						as objeto_existe_ayuda,
									(SELECT COUNT(*) 
										FROM apex_objeto_dependencias 
										WHERE objeto_consumidor = o.objeto
												AND proyecto = o.proyecto) as cant_dependencias
						FROM	apex_objeto o
									LEFT OUTER JOIN apex_objeto_info oi 
										ON (o.objeto = oi.objeto AND o.proyecto = oi.objeto_proyecto),
								apex_clase c
						WHERE	o.clase_proyecto = c.proyecto
						AND			o.clase = c.clase
						AND		o.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['info']['sql'] .= "	AND		o.objeto='$componente';";	
		}
		$sql['info']['registros']='1';	
		$sql['info']['obligatorio']=true;
		return $sql;
	}
		
	function get_vista_dependencias($proyecto, $componente=null)
	{
		$sql['sql'] = 	"	SELECT	d.identificador as		identificador,
							o.proyecto as					proyecto,
							o.objeto as						objeto,
							o.clase as						clase,
							c.archivo as 					clase_archivo,
							o.subclase as					subclase,
							o.subclase_archivo as			subclase_archivo,
							o.fuente_datos as 				fuente,
							d.parametros_a as				parametros_a,
							d.parametros_b as				parametros_b
					FROM	apex_objeto o,
							apex_objeto_dependencias d,
							apex_clase c
					WHERE	o.objeto = d.objeto_proveedor
					AND		o.proyecto = d.proyecto
					AND		o.clase = c.clase
					AND		o.clase_proyecto = c.proyecto
					AND		d.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['sql'] .= "	AND		d.objeto_consumidor='$componente' ";	
		}
		$sql['sql'] .= "			ORDER BY identificador;";
		$sql['registros']='n';
		$sql['obligatorio']=false;
		return $sql;
	}

	static function get_path_clase_runtime()
	{
		return 'nucleo/componentes/runtime';
	}

	static function get_nombre_clase_info()
	{
		return 'info_componente';
	}
}
?>