<?
   	require_once("api/estructura_item.php");

	//Esto se puede llamar en el medio del proceso de una operacion
	$this->hilo->desactivar_reciclado();
   	
   	$parametros = $this->hilo->obtener_parametros();
	$elemento = new estructura_item($parametros['proyecto'],$parametros['item']);
	$elemento->generar_html();
	
?>