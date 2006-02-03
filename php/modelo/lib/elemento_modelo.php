<?
require_once('gui.php');

class elemento_modelo
{
	protected $manejador_interface;	
	protected $dir_raiz;	

	function __construct()
	{
		$this->dir_raiz = toba_dir();
		if( ! is_dir( $this->dir_raiz ) ) {
			//Esto no puede pasar nunca, di toba_dir funciona, es porque el include_path existe
			throw new excepcion_toba("El directorio raiz '{$this->dir_raiz}' no es valido!");
		}
	}

	function get_dir_raiz()
	{
		return $this->dir_raiz;	
	}

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