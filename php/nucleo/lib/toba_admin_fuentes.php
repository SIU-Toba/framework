<?php
/**
 * Colección de Fuentes de Datos (toba_fuente_datos)
 * @package Fuentes
 */
class toba_admin_fuentes
{
	static private $instancia;
	private $fuentes;
	
	/**
	 * @return toba_admin_fuentes
	 */
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_admin_fuentes();
		}
		return self::$instancia;		
	}
	
	private function __construct() {}
	
	/**
	 * Retorna el nombre de la fuente marcada en el editor como predeterminada
	 * @param boolean $obligatorio Tira una excepción en caso de no existir
	 * @return string
	 */
	static function get_fuente_predeterminada($obligatorio=false, $proyecto=null)
	{
		$predeterminada = toba::proyecto()->get_parametro('fuente_datos');	
		if( !($predeterminada) && $obligatorio ) {
			throw new toba_error('No existe una fuente de datos predeterminada');
		}
		return $predeterminada;
	}
	
	/**
	 * Retorna una fuente de datos
	 *
	 * @param string $id Id. de la fuente
	 * @param string $proyecto Proyecto al que pertenece la fuente
	 * @return toba_fuente_datos
	 */
	function get_fuente($id, $proyecto=null)
	{
		if (!isset($proyecto)) {
			$proyecto = toba::proyecto()->get_id();
		}
		if(!isset($id)) {
			$id = $this->get_fuente_predeterminada(true, $proyecto);	
		}
		if ( !isset($this->fuentes[$id]) ) {
			$parametros = toba::proyecto()->get_info_fuente_datos($id, $proyecto);
			$clase = (isset($parametros['subclase_nombre'])) ? $parametros['subclase_nombre'] :  'toba_fuente_datos';
			if (isset($parametros['subclase_archivo'])) {
				$pm = $parametros['punto_montaje'];							
				if (toba::proyecto()->get_id() != $proyecto) {
					//Si la fuente esta extendida, puede necesitar otros archivos del proyecto, agregar el include path				
					$path_proyecto = toba::instancia()->get_path_proyecto($proyecto) . '/php';
					agregar_dir_include_path($path_proyecto);
				}				
				$archivo = $parametros['subclase_archivo'];
				 toba_cargador::cargar_clase_archivo($pm, $archivo, $proyecto);
			} 			
			$this->fuentes[$id] = new $clase($parametros);
		}
		return $this->fuentes[$id];
	}
}
?>
