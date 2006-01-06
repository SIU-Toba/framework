<?
/*
	Ancestro de todos los procesos toba
*/
class proceso_toba
{
	protected $interface;					// Objeto que maneja la salida de la interface
	protected $dir_raiz;					// Directorio RAIZ
	protected $instancia;					// Instancia actual
	protected $proyecto;					// Proyecto actual
	protected $dir_proyecto;				// Directorio del proyecto actual

	function __construct( $raiz, $instancia=null, $proyecto=null )
	{
		$this->dir_raiz = $raiz;
		$this->instancia = $instancia;
		$this->proyecto = $proyecto;
		if ( isset( $proyecto ) ) {
			if ( $proyecto == 'toba' ) {
				$this->dir_proyecto = $this->dir_raiz . '/php/admin';	
			} else {
				$this->dir_proyecto = $this->dir_raiz . '/proyectos/' . $this->proyecto;	
			}
		}
	}

	function set_interface_usuario( $objeto )
	{
		$this->interface = $objeto;
	}
}
?>