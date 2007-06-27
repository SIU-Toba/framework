<?php
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
	* Genera la metadata necesaria para los exportadores.
	* @gtk_icono exportar.png 
	*/
	function opcion__parsear_ddl()
	{
		$this->get_nucleo()->parsear_ddl();
	}

	/**
	* Exporta las tablas maestras del sistema. 
	* @consola_parametros [-i id_instancia] o variable 'toba_instancia'
	* @gtk_icono exportar.png
	*/
	function opcion__exportar_datos()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->exportar( $instancia );
	}
	
	/**
	 * Actualiza los objetos info en base a los editores de los objetos
	 * @gtk_icono exportar.png 
	 */
	function opcion__parsear_editores()
	{
		//Tomo la referencia a la instancia
		$instancia = $this->get_instancia();
		$this->get_nucleo()->parsear_editores( $instancia );		
	}
	
	/**
	 * Comprime el codigo js
	* @gtk_icono extension_zip.png 
	 */
	function opcion__comprimir_js()
	{
		//Tomo la referencia a la instancia
		$this->get_nucleo()->comprimir_js();
	}
	
	/**
	 * Valida el javascript utilizando jslint
	 * @consola_parametros [-a patron inclusion] [-b patron de exclusion]
	 * @gtk_icono tilde.gif
	 */
	function opcion__validar_js()
	{
		$param = $this->get_parametros();		
		$patron_incl = null;
		$patron_excl = null;
		if ( isset($param['-a']) &&  (trim($param['-a']) != '') ) {
			$patron_incl = $param['-a'];
		}
		if ( isset($param['-b']) &&  (trim($param['-b']) != '') ) {
			$patron_excl = $param['-b'];
		}
		
		//Tomo la referencia a la instancia
		$this->get_nucleo()->validar_js($patron_incl, $patron_excl);
	}		

	/**
	 * Reune las definiciones de los componentes en un solo archivo
	* @gtk_icono compilar.png 
	 */
	function opcion__compilar()
	{
		$this->get_nucleo()->compilar();		
	}
}
?>