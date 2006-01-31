<?

class proceso
{
	protected $elemento;	// Elemento del modelo sobre el que se ejecuta el proceso
	protected $interface;

	function __construct( elemento_modelo $elemento )
	{
		$this->elemento = $elemento;
		$this->interface = $this->elemento->get_manejador_interface();
	}

	function procesar(){}
}
?>