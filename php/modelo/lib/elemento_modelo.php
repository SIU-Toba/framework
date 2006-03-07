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
}
?>