<?
require_once('nucleo/lib/fuente_de_datos.php');	
require_once('nucleo/lib/dba.php');

class administrador_fuentes
{
	static private $instancia;
	private $fuentes;
	
	static function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new administrador_fuentes();
		}
		return self::$instancia;		
	}
	
	private function __construct() {}
	
	function get_fuente_predeterminada($obligatorio=false)
	{
		$predeterminada = info_proyecto::instancia()->get_parametro('fuente_datos');	
		if( !($predeterminada) && $obligatorio ) {
			throw new excepcion_toba('No existe una fuente de datos predeterminada');
		}
		return $predeterminada;
	}
	
	function get_fuente($id)
	{
		if(!isset($id)) {
			$id = $this->get_fuente_predeterminada(true);	
		}
		if ( !isset($this->fuentes[$id]) ) {
			$parametros = info_proyecto::get_info_fuente_datos($id);
			if (isset($parametros['subclase_archivo'])) {
				$archivo = $parametros['subclase_archivo'];
			} else {
				$archivo = "nucleo/lib/fuente_de_datos.php";
			}
			if (isset($parametros['subclase_nombre'])) {
				$clase = $parametros['subclase_nombre'];
			} else {
				$clase = "fuente_de_datos";
			}		
			require_once($archivo);
			$this->fuentes[$id] = new $clase($parametros);
		}
		return $this->fuentes[$id];
	}
}
?>
