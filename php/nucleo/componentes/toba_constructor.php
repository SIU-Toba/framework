<?php
require_once('lib/toba_manejador_archivos.php');

/**
 * Construye los objetos php relacionados con componentes especificos
 * @package Componentes
 */
class toba_constructor
{
	static $objetos_runtime_instanciados;		// Referencias a los objetos creados
	static $cache_infos = array();
	static $refresco_forzado = false;

	/**
	 * Retorna el objeto-php que representa un runtime de un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas toba_recursos
	 * @return objeto
	 * @todo Cuando la arquitectura 1 se acabe sacar el 3er parametro, es para que el ambs pueda crear un ut-form
	 */
	static function get_runtime( $id, $tipo=null, $con_subclase=true )
	{
		// Controla la integridad de la clave
		toba_catalogo::control_clave_valida( $id );
		$tipo = toba_catalogo::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = toba_catalogo::get_tipo( $id );	
		}
		// Cargo los metadatos
		if ( toba::nucleo()->utilizar_metadatos_compilados($id['proyecto']) ) {
			$datos = self::get_metadatos_compilados( $id, ($tipo=='item') );
		} else {
			$datos = toba_cargador::instancia()->get_metadatos_extendidos( $id, $tipo );
		}
		//--- INSTANCIACION	---
		if ($tipo != 'item') {		//**** Creacion de OBJETOS
			$clase = toba_catalogo::get_nombre_clase_runtime( $tipo );
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
			$clase = "toba_solicitud_".$datos['basica']['item_solic_tipo'];
			require_once("nucleo/$clase.php");
			return new $clase($datos);
		}
	}

	/**
	 * Retorna un objeto de consultas sobre un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas toba_recursos
	 * @param boolean $en_profundidad Los componentes cargan los info de sus dependencias
	 * @param array $datos Datos pre-procesados que necesita el objeto-info, si no se especifica se buscan
	 * @return info_componente
	 */	
	static function get_info($id, $tipo=null, $en_profundidad=true, $datos=null, $refrescar_cache=false) 
	{
		$refrescar_cache = ($refrescar_cache || self::$refresco_forzado);
		// Controla la integridad de la clave
		toba_catalogo::control_clave_valida( $id );
		$tipo = toba_catalogo::convertir_tipo( $tipo );
		if ( !isset( $tipo ) ) {
			$tipo = toba_catalogo::get_tipo( $id );	
		}
		//--- Si esta en el cache lo retorna
		$hash = $id['componente']."-".$id['proyecto']."-".$tipo;
		if (! isset(self::$cache_infos[$hash]) || $refrescar_cache) {
			if (! isset($datos)) {
				if ( toba::nucleo()->utilizar_metadatos_compilados($id['proyecto']) ) {
					$datos = self::get_metadatos_compilados( $id, ($tipo=='item') );
				} else {
					$datos = toba_cargador::instancia()->get_metadatos_extendidos( $id, $tipo );
				}
			}
			$clase = toba_catalogo::get_nombre_clase_info( $tipo );
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
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas toba_recursos
	 * @return objeto
	 */
	static function buscar_runtime( $id ) 
	{
		if ( isset( self::$objetos_runtime_instanciados[ $id['componente'] ] ) ) {
			return self::$objetos_runtime_instanciados[ $id['componente'] ];
		} else {
			throw new toba_error("El objeto '{$id['componente']}' no fue instanciado");	
		}
	}
	
	/**
	*	Retorna la definicion compilada de un componente
	*/
	static function get_metadatos_compilados( $id, $item=false )
	{
		$directorio_componentes = toba::proyecto()->get_path() . '/metadatos_compilados';		
		if ( $item ) {
			$nombre = 'item__' . toba_manejador_archivos::nombre_valido( $id['componente'] );
			$archivo = $directorio_componentes . '/items/' . $nombre . '.php';
			toba::logger()->debug("buscar COMPILADO: {$id['componente']} => $archivo",'toba');
		} else {
			$nombre = 'componente__' . $id['componente'];
			$archivo = $directorio_componentes . '/componentes/' . $nombre . '.php';
			toba::logger()->debug("buscar COMPILADO: {$id['componente']} => $archivo",'toba');
		}
		require_once( $archivo );
		return call_user_func( array( $nombre, 'get_metadatos' ) );
	}
	
	static function set_refresco_forzado($refrescar)
	{
		self::$refresco_forzado = $refrescar;
	}
}
?>