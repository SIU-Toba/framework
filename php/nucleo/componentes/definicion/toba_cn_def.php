<?php
/**
 * Clase de Negocio
 * @package Componentes
 * @subpackage Negocio
 */
class toba_cn_def extends toba_componente_def
{
	static function get_estructura()
	{
		$estructura = parent::get_estructura();
		$estructura[] = array( 	'tabla' => 'apex_objeto_dependencias',
								'registros' => 'n',
								'obligatorio' => false );
		$estructura[] = array( 	'tabla' => 'apex_objeto_dep_consumo',
								'registros' => 'n',
								'obligatorio' => false );
		return $estructura;		
	}
		
	static function get_vista_extendida($proyecto, $componente=null)
	{
		$sql = parent::get_vista_extendida($proyecto, $componente);
		$sql['_info_dependencias'] = parent::get_vista_dependencias($proyecto, $componente);
		//-- Componentes consumidos
		$sql['_info_consumo']['sql'] = 	"SELECT	d.identificador as		identificador,
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
												apex_objeto_dep_consumo d,
												apex_clase c
										WHERE	o.objeto = d.objeto_proveedor
										AND		o.proyecto = d.proyecto
										AND		o.clase = c.clase
										AND		o.clase_proyecto = c.proyecto
										AND		d.proyecto='$proyecto'";
		if ( isset($componente) ) {
			$sql['_info_consumo']['sql'] .= "	AND		d.objeto_consumidor=$componente ";	
		}
		$sql['_info_consumo']['sql'] .= "	ORDER BY o.objeto;";
		$sql['_info_consumo']['registros']='n';
		$sql['_info_consumo']['obligatorio']=false;
		return $sql;
	}

	static function get_vista_extendida_resumida($proyecto, $componente)
	{
		$estructura = self::get_vista_extendida($proyecto, $componente);
		unset($estructura['_info_relaciones']);
		return $estructura;
	}
}
?>