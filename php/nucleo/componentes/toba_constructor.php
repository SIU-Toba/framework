<?php
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
		self::control_clave_valida( $id );
		if ( !isset( $tipo ) ) {
			$tipo = toba_cargador::get_tipo( $id );	
		}
		// Cargo los metadatos
		if ( toba::nucleo()->utilizar_metadatos_compilados($id['proyecto']) ) {
			$datos = self::get_metadatos_compilados( $id, ($tipo=='toba_item') );
		} else {
			$datos = toba_cargador::instancia()->get_metadatos_extendidos( $id, $tipo );
		}
		//--- INSTANCIACION	---
		if ($tipo != 'toba_item') {		//**** Creacion de OBJETOS
			$clase = $tipo;
			//Posee una subclase asociada?
			if ( $datos['_info']['subclase'] && $con_subclase ) {
				if(isset($datos['_info']['subclase_archivo'])) { //Puede estar en un autoload
					require_once($datos['_info']['subclase_archivo']);
				}
				$clase = $datos['_info']['subclase'];
			}
			//Instancio el objeto
			$objeto = new $clase( $datos );
			self::$objetos_runtime_instanciados[ $id['componente'] ] = $objeto;
			return 	$objeto;
		} else {					//**** Creacion de ITEMS
			$clase = "toba_solicitud_".$datos['basica']['item_solic_tipo'];
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
	 * @param boolean $refrescar_cache Indica que el objeto debe recargarse si ya se habia cargado anteriormente en el request
	 * @param boolean $resumumidos Indica si que se realiza solo la carga basica de datos del componente
	 * @return info_componente
	 */	
	static function get_info($id, $tipo=null, $en_profundidad=true, $datos=null, $refrescar_cache=false, $resumidos=false) 
	{
		$refrescar_cache = ($refrescar_cache || self::$refresco_forzado);
		// Controla la integridad de la clave
		self::control_clave_valida( $id );
		if ( !isset( $tipo ) ) {
			$tipo = toba_cargador::get_tipo( $id );	
		}
		//--- Si esta en el cache lo retorna
		$hash = $id['componente']."-".$id['proyecto']."-".$tipo;
		if (! isset(self::$cache_infos[$hash]) || $refrescar_cache) {
			if (! isset($datos)) {
				if ( toba::nucleo()->utilizar_metadatos_compilados($id['proyecto']) ) {
					$datos = self::get_metadatos_compilados( $id, ($tipo=='toba_item') );
				} else {
					$datos = toba_cargador::instancia()->get_metadatos_extendidos( $id, $tipo, null, $resumidos );
				}
			}
			$clase = $tipo .'_info';
			$obj = new $clase( $datos, $en_profundidad, $resumidos );
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
		if ( $item ) {
			$clase = 'toba_mc_item__' . toba_manejador_archivos::nombre_valido( $id['componente'] );
		} else {
			$clase = 'toba_mc_comp__' . $id['componente'];
		}
		return call_user_func( array( $clase, 'get_metadatos' ) );
	}

	static function control_clave_valida( $clave_componente )
	{
		if(! is_array($clave_componente) 
			|| !isset($clave_componente['componente']) 
			|| !isset($clave_componente['proyecto']) ) {
			throw new toba_error("La clave utilizada para invocar el componente no es valida: ".var_export($clave_componente, true));	
		}
	}
	
	static function set_refresco_forzado($refrescar)
	{
		self::$refresco_forzado = $refrescar;
	}
}
?>