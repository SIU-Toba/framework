<?
require_once('nucleo/lib/manejador_archivos.php');

class constructor_toba
{
	static $objetos_runtime_instanciados;		// Referencias a los objetos creados
	static $cache_infos = array();
	static $refresco_forzado = false;

	/**
	 * Retorna el objeto-php que representa un runtime de un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas recursos
	 * @return objeto
	 * @todo Cuando la arquitectura 1 se acabe sacar el 3er parametro, es para que el ambs pueda crear un ut-form
	 */
	static function get_runtime( $id, $tipo=null, $con_subclase=true )
	{
		// Controla la integridad de la clave
		catalogo_toba::control_clave_valida( $id );
		$tipo = catalogo_toba::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $id );	
		}
		// Cargo los metadatos
		if ( defined('apex_pa_componentes_compilados') && apex_pa_componentes_compilados ) {
			$datos = self::get_metadatos_compilados( $id, $tipo );
		} else {
			$datos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
		}
		//--- INSTANCIACION	---
		if ($tipo != 'item') {		//**** Creacion de OBJETOS
			$clase = catalogo_toba::get_nombre_clase_runtime( $tipo );
			//Posee una subclase asociada?
			if ( $datos['info']['subclase'] && $con_subclase ) {
				require_once($datos['info']['subclase_archivo']);
				$clase = $datos['info']['subclase'];
			}
			//Instancio el objeto
			$objeto = new $clase( $datos );
			self::$objetos_runtime_instanciados[ $id['componente'] ] = $objeto;
			return 	$objeto;
		} else {					//**** Creacion de ITEMS
			$clase = "solicitud_".$datos['basica']['item_solic_tipo'];
			require_once("nucleo/$clase.php");
			return new $clase($datos);
		}
	}

	/**
	 * Retorna un objeto de consultas sobre un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas recursos
	 * @param boolean $en_profundidad Los componentes cargan los info de sus dependencias
	 * @param array $datos Datos pre-procesados que necesita el objeto-info, si no se especifica se buscan
	 * @return info_componente
	 */	
	static function get_info($id, $tipo=null, $en_profundidad=true, $datos=null, $refrescar_cache=false) 
	{
		$refrescar_cache = ($refrescar_cache || self::$refresco_forzado);
		// Controla la integridad de la clave
		catalogo_toba::control_clave_valida( $id );
		$tipo = catalogo_toba::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = catalogo_toba::get_tipo( $id );	
		}
		//--- Si esta en el cache lo retorna
		$hash = $id['componente']."-".$id['proyecto']."-".$tipo;
		if (! isset(self::$cache_infos[$hash]) || $refrescar_cache) {
			if (! isset($datos)) {
				if ( defined('apex_pa_componentes_compilados') && apex_pa_componentes_compilados ) {
					$datos = self::get_metadatos_compilados( $id, $tipo );
				} else {
					$datos = cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
				}
			}
			$clase = catalogo_toba::get_nombre_clase_info( $tipo );
			$obj = new $clase( $datos, $en_profundidad );
			self::$cache_infos[$hash] = $obj;
		}
		return self::$cache_infos[$hash];
	}	

	/**
	 * Retorna el objeto-php que representa un runtime YA INSTANCIADO previamente con
	 *	con get_runtime()
	 *	
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas recursos
	 * @return objeto
	 */
	static function buscar_runtime( $id ) 
	{
		if ( isset( self::$objetos_runtime_instanciados[ $id['componente'] ] ) ) {
			return self::$objetos_runtime_instanciados[ $id['componente'] ];
		} else {
			throw new excepcion_toba("El objeto '{$id['componente']}' no fue instanciado");	
		}
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
		// Si el proceso esta bien, esto deberia andar...
		require_once( $archivo );
		return call_user_func( array( $nombre, 'get_metadatos' ) );
/*
		if ( file_exists( $archivo )) {
			require_once( $archivo );
			return call_user_func( array( $nombre, 'get_metadatos' ) );
		} else {
			if( defined(apex_pa_componentes_compilados__error_buscar_db) 
					&& apex_pa_componentes_compilados__error_buscar_db ){
				return cargador_toba::instancia()->get_metadatos_extendidos( $id, $tipo );
			} else {
				throw new excepcion_toba("No existe el componente compilado solicitado . CLASE: $tipo, ID: '{$id['componente']}'");
			}
		}
*/
	}
	
	static function set_refresco_forzado($refrescar)
	{
		self::$refresco_forzado = $refrescar;
	}
}
?>