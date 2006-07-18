<?
require_once('comando_toba.php');
/**
*	Publica los servicios de la clase NUCLEO a la consola toba
*/
class comando_nucleo extends comando_toba
{
	function mostrar_observaciones()
	{
		$this->consola->mensaje("INVOCACION: toba nucleo OPCION");
		$this->consola->enter();
	}
		
	static function get_info()
	{
		return 'Administracion de la informacion perteneciente al nucleo del sistema';
	}

	//-------------------------------------------------------------
	// Opciones
	//-------------------------------------------------------------

	/**
	*	Genera la metadata necesaria para los exportadores.
	*/
	function opcion__parsear_ddl()
	{
		$this->get_nucleo()->parsear_ddl();
	}

	/**
	*	Exporta las tablas maestras del sistema. 
	*	PARAMETROS: [-i id_instancia] o variable 'toba_instancia'
	*/
	function opcion__exportar_datos()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->exportar( $instancia );
	}
	
	/**
	 * Actualiza los objetos info en base a los editores de los objetos
	 */
	function opcion__parsear_editores()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->parsear_editores( $instancia );		
	}
	
	/**
	 * Comprime el codigo js
	 */
	function opcion__comprimir_js()
	{
		//Tomo la referencia a la instancia
		$this->get_nucleo()->comprimir_js();
	}
	
	/**
	 * Valida el javascript utilizando jslint
	 */
	function opcion__validar_js()
	{
		$param = $this->get_parametros();		
		$patron = null;
		if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
			$patron = $param['-a'];
		}		
		//Tomo la referencia a la instancia
		$this->get_nucleo()->validar_js($patron);
	}		
}
?>