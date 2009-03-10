<?php

/**
 * @package Fuentes
 */
class toba_info_relacion_entre_tablas
{
	static protected $relaciones = array();

	static function cargar_relaciones($fuente_datos, $proyecto=null)
	{
		self::$relaciones[$fuente_datos] = toba_proyecto::get_info_relacion_entre_tablas($fuente_datos, $proyecto);
	}

	/**
	*	Indica las columnas por las que se relacionan dos tablas de un modelo de datos
	*	retorna un array asociativo con un indice por tabla y un array con el listado de columnas en la segunda dimension.
	*/
	static function get_relacion($tabla_1, $tabla_2, $fuente_datos=null, $proyecto=null)
	{
		if(!$fuente_datos) $fuente_datos = toba_admin_fuentes::instancia()->get_fuente_predeterminada(true, $proyecto);
		if(!isset(self::$relaciones[$fuente_datos]) ) {
			self::cargar_relaciones($fuente_datos, $proyecto);	
		}
		//Busco la relacion
		if(isset(self::$relaciones[$fuente_datos][$tabla_1][$tabla_2])) {
			$respuesta[$tabla_1]=self::$relaciones[$fuente_datos][$tabla_1][$tabla_2]['cols_1'];
			$respuesta[$tabla_2]=self::$relaciones[$fuente_datos][$tabla_1][$tabla_2]['cols_2'];
		} else {
			//La busco con el indice invertido
			if(isset(self::$relaciones[$fuente_datos][$tabla_2][$tabla_1])) {
				$respuesta[$tabla_1]=self::$relaciones[$fuente_datos][$tabla_2][$tabla_1]['cols_2'];
				$respuesta[$tabla_2]=self::$relaciones[$fuente_datos][$tabla_2][$tabla_1]['cols_1'];
			} else {
				throw new toba_error('Informacion del modelo de datos de la fuente: '. $fuente_datos . ". No existe la relacion: $tabla_1 - $tabla_2" );
			}
		}
		return $respuesta;
	}

	/**
	 * Controla que un grupo de tablas esten vinculadas por Fks
	 * @param Array de tablas ordenadas segun su relacion
	 * @todo IMPLEMENTAR!
	 */
	static function validar_camino($camino)
	{
		
	}
}

?>