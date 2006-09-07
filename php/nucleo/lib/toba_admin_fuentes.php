<?php
require_once('nucleo/lib/toba_fuente_datos.php');	
require_once('nucleo/lib/toba_dba.php');

/**
 * Colección de Fuentes de Datos (toba_fuente_datos)
 * @package Librerias
 * @subpackage Fuentes
 */
class toba_admin_fuentes
{
	static private $instancia;
	private $fuentes;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new toba_admin_fuentes();
		}
		return self::$instancia;		
	}
	
	private function __construct() {}
	
	function get_fuente_predeterminada($obligatorio=false)
	{
		$predeterminada = toba::proyecto()->get_parametro('fuente_datos');	
		if( !($predeterminada) && $obligatorio ) {
			throw new toba_error('No existe una fuente de datos predeterminada');
		}
		return $predeterminada;
	}
	
	function get_fuente($id)
	{
		if(!isset($id)) {
			$id = $this->get_fuente_predeterminada(true);	
		}
		if ( !isset($this->fuentes[$id]) ) {
			$parametros = toba_proyecto::get_info_fuente_datos($id);
			if (isset($parametros['subclase_archivo'])) {
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
