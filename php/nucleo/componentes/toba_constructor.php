<?php
/**
 * Construye los objetos php relacionados con componentes especificos
 * @package Componentes
 */
class toba_constructor
{
	static $objetos_runtime_instanciados = array();		// Referencias a los objetos creados
	static $cache_infos = array();
	static $refresco_forzado = false;

	/**
	 * Retorna el objeto-php que representa un runtime de un componente-toba
	 *
	 * @param array $id Arreglo con dos claves 'componente' y 'proyecto'
	 * @param string $tipo Tipo de componente. Si no se brinda se busca automáticamente, aunque requiere mas toba_recursos
	 * @param boolean $usar_cache Si el componente fue previamente construido en este pedido de página, retorna su referencia, sino lo crea.
	 * @return objeto
	 */
	static function get_runtime( $id, $tipo=null, $usar_cache = false )
	{
		list($tipo, $clase, $datos) = self::get_runtime_clase_y_datos($id, $tipo, $usar_cache);
		return self::get_runtime_objeto($id, $tipo, $clase, $datos);
	}

	static function get_runtime_clase_y_datos( $id, $tipo=null, $usar_cache = false )
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
			$instancia_nro = 0;
			$clase = $tipo;
			if (!$usar_cache || !isset(self::$objetos_runtime_instanciados[ $id['componente'] ])) {
				//Posee una subclase asociada?
				if ( $datos['_info']['subclase']) {
					if(isset($datos['_info']['subclase_archivo'])) { //Puede estar en un autoload
						self::cargar_clase($datos, $id['proyecto']);
					}
					$clase = $datos['_info']['subclase'];
				} else {
					$clase = get_nombre_clase_extendida($clase, $id['proyecto'], toba::proyecto()->get_clases_extendidas());
				}
				//Averiguo cuantas instancias previas de este componente fueron creadas
				if (! isset(self::$objetos_runtime_instanciados[ $id['componente'] ])) {
					$instancia_nro = 0;
					self::$objetos_runtime_instanciados[ $id['componente'] ] = array();
				} else {
					$instancia_nro = count(self::$objetos_runtime_instanciados[$id['componente']]);
				}				
			}
			$datos['_const_instancia_numero'] = $instancia_nro;			
		} elseif (is_null($datos['basica']['carpeta']) || $datos['basica']['carpeta'] != '1')  {					//**** Creacion de ITEMS
			$clase = "toba_solicitud_".$datos['basica']['item_solic_tipo'];
		} else {
			throw new toba_error_seguridad('La operación invocada no existe: ' . var_export($id, true));
		}
		return array($tipo, $clase, $datos);		
	}

	protected static function cargar_clase(&$datos, $id_proyecto)
	{
		toba_cargador::cargar_clase_archivo($datos['_info']['punto_montaje'],  $datos['_info']['subclase_archivo'], $id_proyecto);
	}

	static function get_runtime_objeto($id, $tipo, $clase, $datos)
	{
		$objeto = new $clase( $datos );
		if ($tipo != 'toba_item') {		//**** Creacion de OBJETOS		
			//Controlo que pertenezca a la clase definida
			if (! $objeto instanceof $datos['_info']['clase']) {
				$clase_actual = get_class($objeto);
				$clase_requerida = $datos['_info']['clase'];
				$componente = $datos['_info']['objeto'];
				throw new toba_error_def("La sublcase '$clase_actual' del componente '$componente' debe heredar de la clase '$clase_requerida'");
			}
			self::$objetos_runtime_instanciados[ $id['componente'] ][] = $objeto;
			if (isset($datos['_const_instancia_numero'])) {
				$instancia_nro = $datos['_const_instancia_numero'];
			} else {
				$instancia_nro = 0;
			}
			return self::$objetos_runtime_instanciados[ $id['componente'] ][$instancia_nro];
		} else {
			return $objeto;
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
	static function buscar_runtime($id, $numero_instancia=0) 
	{
		if ( isset(self::$objetos_runtime_instanciados[$id['componente']]) && ! empty(self::$objetos_runtime_instanciados[$id['componente']])) {
			return self::$objetos_runtime_instanciados[$id['componente']][$numero_instancia];
		} else {
			throw new toba_error_def("El objeto '{$id['componente']}' no fue instanciado");	
		}
	}
	
	/**
	*	Retorna la definicion compilada de un componente
	*/
	static function get_metadatos_compilados( $id, $item=false )
	{
		//Chequea si no se redefinieron en runtime
		$extendidos = toba_cargador::instancia()->get_metadatos_redefinidos($id);
		if (isset($extendidos)) {
			return $extendidos;
		}
		if ( $item ) {
			$clase = 'toba_mc_item__' . toba_manejador_archivos::nombre_valido( $id['componente'] );
		} else {
			$clase = 'toba_mc_comp__' . $id['componente'];
		}
		return call_user_func( array( $clase, 'get_metadatos' ) );
	}

	/**
	 * @ignore 
	 */
	static function control_clave_valida( $clave_componente )
	{
		if(! is_array($clave_componente) 
			|| !isset($clave_componente['componente']) 
			|| !isset($clave_componente['proyecto']) ) {
			throw new toba_error_def("La clave utilizada para invocar el componente no es valida: ".var_export($clave_componente, true));	
		}
	}
	
	/**
	 * Fuerza a que todos los componentes requeridos en este pedido de página no surjan de algún cache (util para testeos)
	 */
	static function set_refresco_forzado($refrescar)
	{
		self::$refresco_forzado = $refrescar;
	}
}
?>