<?

class catalogo_toba
{
	/**
	*	Lista de componentes
	*/
	static function get_lista_tipo_componentes()
	{
		$datos[] = 'item';
		$datos[] = 'ci';
		$datos[] = 'ei_arbol';
		$datos[] = 'ei_archivos';
		$datos[] = 'ei_calendario';
		$datos[] = 'ei_filtro';
		$datos[] = 'ei_formulario';
		$datos[] = 'ei_formulario_ml';
		$datos[] = 'ei_cuadro';
		$datos[] = 'ei_esquema';
		$datos[] = 'cn';
		$datos[] = 'datos_tabla';
		$datos[] = 'datos_relacion';
		return $datos;
	}

	/**
	*	Se agregan los transversales para el DUMP
	*/
	static function get_lista_tipo_componentes_dump()
	{
		$datos = self::get_lista_tipo_componentes();
		$datos[] = 'cuadro';
		$datos[] = 'cuadro_reg';
		$datos[] = 'filtro';
		$datos[] = 'hoja';
		$datos[] = 'mt_abms';
		$datos[] = 'html';
		$datos[] = 'lista';
		$datos[] = 'mt';
		$datos[] = 'mt_mds';
		$datos[] = 'mt_s';
		$datos[] = 'ut_formulario';
		return $datos;
	}
	
	static function get_nombre_clase_definicion( $tipo )
	{
		require_once('nucleo/componentes/definicion/componente_' .$tipo. '.php');
		return 'componente_'. $tipo;
	}

	static function get_nombre_clase_runtime( $tipo )
	{
		$clase_definicion = self::get_nombre_clase_definicion( $tipo );
		$path = call_user_func( array( $clase_definicion, 'get_path_clase_runtime') );
		require_once( $path .'/objeto_'. $tipo. '.php');
		return 'objeto_'. $tipo;
	}

	static function get_nombre_clase_info( $tipo )
	{
		$clase_definicion = self::get_nombre_clase_definicion( $tipo );
		$nombre = call_user_func( array( $clase_definicion, 'get_nombre_clase_info') );
		require_once('modelo/componentes/' .$nombre. '.php');
		return $nombre;
	}

	static function get_tipo( $componente )
	{
		$sql = " 	SELECT clase
					FROM apex_objeto
					WHERE (objeto = '{$componente['componente']}')
					AND (proyecto = '{$componente['proyecto']}')";
		$datos = info_instancia::get_db()->consultar($sql);
		return self::convertir_tipo( $datos[0]['clase'] );
	}

	/**
	*	La DB como parametro se pasa en el caso de que esto se utilice desde la consola
	*/
	static function get_lista_componentes( $tipo_componente, $proyecto, $db = null )
	{
		if (!isset($db)) {
			//Estoy entrando por el nucleo
			$db = info_instancia::get_db();	
		}
		if ($tipo_componente == 'item' ) {
			$sql = "SELECT 	proyecto as 		proyecto,
							item as 			componente
					FROM apex_item 
					WHERE proyecto = '$proyecto'
					ORDER BY 1;";
			$datos = $db->consultar( $sql );
		} else {
			$tipo_componente = 'objeto_'.$tipo_componente;
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
			throw new excepcion_toba("La clave utilizada para invocar el componente no es valida: ".var_export($clave_componente, true));	
		}
	}

	/*
		FUNCTION TEMPORAL:
			Conversor necesario porque la definicion de APEX_CLASE tiene mal
			el nombre de clase que tiene que tener ( no necesita 'objeto_')
	*/
	static function convertir_tipo( $tipo )
	{
		if(!isset($tipo)) return null;
		if($tipo=='item') return 'item';
		$no_mas = 'objeto_';
		if( strrpos($tipo, $no_mas) !== false ) {
			return substr($tipo, strlen($no_mas) );
		} else {
			return $tipo;	
		}
	}	
}
?>