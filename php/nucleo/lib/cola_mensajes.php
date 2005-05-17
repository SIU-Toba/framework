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
	
	function __construct($solicitud)
	{
		$this->solicitud = $solicitud;
	}
	
	//--------------------------------------------------------------

	public function agregar($mensaje, $nivel='error')
	{
		$this->mensajes[] = array($mensaje, $nivel);
		//Agrego el mensaje mostrado al usuario al logger como DEBUG
		$this->solicitud->log->debug("[usuario] ".$mensaje);
	}

	public function agregar_id($indice, $parametros=null, $nivel='error')
	{
		$this->agregar(mensaje::get($indice, $parametros), $nivel);
	}

	public function verificar_mensajes()
	//Reporta la existencia de mensajes
	{
		if(count($this->mensajes)>0) return true;
	}

	//--------------------------------------------------------------
	
	public function mostrar()
	{
		echo js::abrir();
		foreach($this->mensajes as $mensaje){
			echo "cola_mensajes.agregar('{$mensaje[0]}' + '\\n', '{$mensaje[1]}');\n";
		}
		echo js::cerrar();
	}
	
	public function vaciar()
	{
		$this->mensajes = array();
	}
	//--------------------------------------------------------------
}
?>