<?php
/**
 * Consultas transversales sobre componentes
 * 
 * @package Componentes
 */
class toba_catalogo
{
	/**
	*	Lista de componentes
	*/
	static function get_lista_tipo_componentes()
	{
		$datos[] = 'toba_item';
		$datos[] = 'toba_ci';
		$datos[] = 'toba_ei_arbol';
		$datos[] = 'toba_ei_archivos';
		$datos[] = 'toba_ei_calendario';
		$datos[] = 'toba_ei_filtro';
		$datos[] = 'toba_ei_formulario';
		$datos[] = 'toba_ei_formulario_ml';
		$datos[] = 'toba_ei_cuadro';
		$datos[] = 'toba_ei_esquema';
		$datos[] = 'toba_cn';
		$datos[] = 'toba_datos_tabla';
		$datos[] = 'toba_datos_relacion';
		return $datos;
	}

	/**
	*	Se agregan los transversales para el DUMP
	*/
	static function get_lista_tipo_componentes_dump()
	{
		return self::get_lista_tipo_componentes();
	}

	static function get_tipo( $componente )
	{
		$sql = " 	SELECT clase
					FROM apex_objeto
					WHERE (objeto = '{$componente['componente']}')
					AND (proyecto = '{$componente['proyecto']}')";
		$datos = toba::instancia()->get_db()->consultar($sql);
		return $datos[0]['clase'];
	}

	/**
	*	La DB como parametro se pasa en el caso de que esto se utilice desde la consola
	*/
	static function get_lista_componentes( $tipo_componente, $proyecto, $db = null )
	{
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = toba::instancia()->get_db();	
		}
		if ($tipo_componente == 'toba_item' ) {
			$sql = "SELECT 	proyecto as 		proyecto,
							item as 			componente
					FROM apex_item 
					WHERE proyecto = '$proyecto'
					ORDER BY 1;";
			$datos = $db->consultar( $sql );
		} else {
			$tipo_componente = $tipo_componente;
			$sql = "SELECT 	proyecto as 		proyecto,
							objeto as 			componente
					FROM apex_objeto 
					WHERE proyecto = '$proyecto'
					AND clase = '$tipo_componente'
					ORDER BY 1;";
			$datos = $db->consultar( $sql );
		}
		return $datos;
	}

	static function control_clave_valida( $clave_componente )
	{
		if(! is_array($clave_componente) 
			|| !isset($clave_componente['componente']) 
			|| !isset($clave_componente['proyecto']) ) {
			throw new toba_error("La clave utilizada para invocar el componente no es valida: ".var_export($clave_componente, true));	
		}
	}
}
?>