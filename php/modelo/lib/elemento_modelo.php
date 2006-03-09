<?
require_once('gui.php');

class elemento_modelo
{
	protected $manejador_interface;	

	function set_manejador_interface( gui $manejador_interface )
	{
		$this->manejador_interface = $manejador_interface;
	}
	
	function get_manejador_interface()
	{
		if( ! isset( $this->manejador_interface ) ) {
			return new gui_mock();	
		} else {
			return $this->manejador_interface;
		}
	}
	

	function migrar_rango_versiones($desde, $hasta, $recursivo)
	{
		$versiones = $desde->get_secuencia_migraciones($hasta);
		foreach ($versiones as $version) {
			$this->manejador_interface->titulo("Versión ".$version->__toString());
			$this->migrar_version($version, $recursivo);
		}
	}	
}
?>