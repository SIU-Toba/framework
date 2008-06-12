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
	function get_fuente_predeterminada($obligatorio=false)
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
			$id = $this->get_fuente_predeterminada(true);	
		}
		if ( !isset($this->fuentes[$id]) ) {
			$parametros = toba::proyecto()->get_info_fuente_datos($id, $proyecto);
			if (isset($parametros['subclase_archivo'])) {
				if ( toba_editor::activado() ) {
					//Si la fuente esta extendida, puede necesitar otros archivos del proyecto, agregar el include path
					toba_editor::incluir_path_proyecto_cargado();					
				}
				$archivo = $parametros['subclase_archivo'];
			} else {
				$archivo = "nucleo/lib/toba_fuente_datos.php";
			}
			if (isset($parametros['subclase_nombre'])) {
				$clase = $parametros['subclase_nombre'];
			} else {
				$clase = "toba_fuente_datos";
			}		
			require_once($archivo);
			$this->fuentes[$id] = new $clase($parametros);
		}
		return $this->fuentes[$id];
	}
}
?>
