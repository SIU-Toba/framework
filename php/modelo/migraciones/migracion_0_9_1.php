<?
require_once('migracion_toba.php');

class migracion_0_9_1 extends migracion_toba
{

	//------------------------------------------------------------------------
	//-------------------------- INSTALACION --------------------------
	//------------------------------------------------------------------------
	
	
	/**
	 *	Existe una nueva entrada en la instalacion que define el comando de invocación
	 *  del editor utilizado en el escritorio
	 */
	function instalacion__definir_comando_editor()
	{
		$this->elemento->cambiar_info_basica(array('editor_php' => 'start'));
	}
}
?>
