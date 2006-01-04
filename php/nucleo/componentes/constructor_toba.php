<?
require_once('nucleo/lib/manejador_archivos.php');
define('apex_componentes_compilados',false);
define('apex_componentes_compilados__error_buscar_db',true);
/*
*	Creacion de componentes
*/
class constructor_toba
{
	/**
	 * Retorna el objeto-php que representa un runtime de un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas recursos
	 * @return objeto
	 */
	static function get_runtime( $id, $tipo=null )
	{
		// Controla la integridad de la clave
		catalogo_toba::control_clave_valida( $id );
		$tipo = catalogo_toba::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $id );	
		}
		// Cargo los metadatos
		if ( apex_componentes_compilados ) {
			$datos = self::get_metadatos_compilados( $id, $tipo );
		} else {
			$datos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
		}
		$clase = catalogo_toba::get_nombre_clase_runtime( $tipo );
		//Posee una subclase asociada?
		if ( $datos['info']['subclase'] ) {
			require_once($datos['info']['subclase_archivo']);
			$clase = $datos['info']['subclase'];
		}
		//Instancio el objeto
		return new $clase( $datos );		
	}

	/**
	 * Retorna el objeto-php que representa un runtime de un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas recursos
	 * @return info_componente
	 */	
	static function get_info($id, $tipo=null) 
	{
		// Controla la integridad de la clave
		catalogo_toba::control_clave_valida( $id );
		$tipo = catalogo_toba::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $id );	
		}
		if ( apex_componentes_compilados ) {
			$datos = self::get_metadatos_compilados( $id, $tipo );
		} else {
			$datos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
		}
		$clase = catalogo_toba::get_nombre_clase_info( $tipo );
		return new $clase( $datos );		
	}	
	
	/*
	*	Este proceso necesita optimizacion
	*/
	static private function get_metadatos_compilados( $id, $tipo )
	{
		$raiz = toba::get_hilo()->obtener_path();
		$directorio_componentes = $raiz . '/php/admin/metadatos_compilados/componentes';		
		$prefijo = 'php_';
		if ( $tipo == 'item' ) {
			$nombre = $prefijo . manejador_archivos::nombre_valido( $id['componente'] );
		} else {
			$nombre = $prefijo . $id['componente'];
		}
		$archivo = $directorio_componentes . '/' . $tipo  . '/' . $nombre . '.php';
		if ( file_exists( $archivo )) {
			require_once( $archivo );
			return call_user_func( array( $nombre, 'get_metadatos' ) );
		} else {
			if( apex_componentes_compilados__error_buscar_db ){
				return cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
			} else {
				throw new excepcion_toba("No existe el componente compilado solicitado . CLASE: $tipo, ID: '{$id['componente']}'");
			}
		}
	}

/*	
	static function get_objeto($id, $parametros=null, $clase=null, $archivo=null)
	{
		if(!isset($archivo) || !isset($clase))
		{
			//Busco la informacion que necesaria para crearlo
			//ATENCION: Esto es ineficiente pero rapido de programar
			//Hay que optmizar la cantidad de consultas y nunca repetirlas en un pedido de pagina
			require_once("admin/db/dao_editores.php");
			if (!isset($clase)) {
				$clase = dao_editores::get_clase_de_objeto($id);
			}
			$archivo = dao_editores::get_archivo_de_clase($id[0], $clase);
		}
		require_once($archivo);
		$objeto = new $clase($id, $parametros);
		return $objeto;
	}
*/
}
?>