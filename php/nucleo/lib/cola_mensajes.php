<?
/*
	Cola de mensajes a mostrar al usuario

	Falta implementar algo asociado a los GRAGOS
	(que implique el color que se le da al mensaje)

*/
class cola_mensajes
{
	private $solicitud;
	private $mensajes = array();
	private $nivel_gravedad="info";		//info, error
	
	function __construct($solicitud)
	{
		$this->solicitud = $solicitud;
	}
	
	//--------------------------------------------------------------

	public function set_nivel_gravedad($nivel_gravedad)
	{
		$this->nivel_gravedad = $nivel_gravedad;
	}

	public function agregar($mensaje)
	{
		$this->mensajes[] = $mensaje;
		//Agrego el mensaje mostrado al usuario al logger como DEBUG
		$this->solicitur->logger->debug("Mensaje al usuario: ".$mensaje);
	}

	public function agregar_id($indice, $parametros=null)
	{
		$this->mensajes[] = mensaje::get($indice, $parametros=null);
	}

	public function verificar_mensajes()
	//Reporta la existencia de mensajes
	{
		if(count($this->mensajes)>0) return true;
	}

	//--------------------------------------------------------------
	
	public function mostrar()
	{
		$temp = "";
		foreach($this->mensaje as $mensaje){
			$temp .= $mensaje . " <br>";
		}
		echo ei_mensaje($temp, $this->nivel_gravedad);
		$this->vaciar();		
	}
	
	public function vaciar()
	{
		$this->mensajes = array();
	}
	//--------------------------------------------------------------
}
?>