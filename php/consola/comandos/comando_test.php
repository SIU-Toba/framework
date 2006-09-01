<?
require_once('comando_toba.php');
require_once('modelo/lib/comparador_instancias.php');

class comando_test extends comando_toba
{
	static function get_info()
	{
		return 'Ejecucion de baterias de TEST';
	}

	/**
	*	<I_1> <I_2> Compara dos Instancias
	*/
	function opcion__ci()
	{
		if ( !isset( $this->argumentos[1] ) || !isset( $this->argumentos[2] ) ) {
			throw new toba_error("Es necesario indicar el nombre de las dos instancias");
		}
		$ci = new comparador_instancias( $this->argumentos[1], $this->argumentos[2] );
		$datos = $ci->procesar();
		$titulos = array( 'TABLA', $this->argumentos[1], $this->argumentos[2], 'diff');
		$this->consola->tabla( $datos, $titulos );
		$ci->finalizar();
	}
	
	/**
	 * Ejecuta la batería de test automáticos de un proyecto
	 * Argumentos: -p Proyecto [-c Cat] [-t Caso] 
	 */
	function opcion__automaticos()
	{
		require_once('acciones/pruebas/testing_automatico/lista_casos.php');
		require_once('3ros/simpletest/reporter.php');
		
		$param = $this->get_parametros();		
		$proyecto = isset($param["-p"]) ? $param["-p"] : "toba";
		
		lista_casos::$proyecto = $proyecto;
		
		//Selecciono una categoria
		if (isset($param["-c"])) {
			$seleccionados = lista_casos::get_casos($param["-c"]);
		} else {
			$seleccionados = lista_casos::get_casos();
		}
					
		if(isset($param["-t"])) {
			//Seleccion de un test particular
			if (isset($param["-t"])) {
				$particular = false;
				foreach ($seleccionados as $caso) {
					if ($caso['id'] == $param["-t"]) {
						$particular = $caso;
					}
				}
				if ($particular)
					$seleccionados = array($particular);
				else
					$seleccionados = array();
			}	
		} 
		
		try {
			$test = new GroupTest('Casos de TEST');
		    foreach ($seleccionados as $caso) {
		        require_once($caso['archivo']);
		        $test->addTestCase(new $caso['id']($caso['nombre']));
			}
			
			//Termina la ejecución con 0 o 1 para que pueda comunicarse con al consola
			exit ($test->run(new TextReporter()) ? 0 : 1);
			
		} catch (Exception $e) {
			if (method_exists($e, 'mensaje_consola'))
				echo $e->mensaje_consola();
			else
				echo $e;
		}
		
	}
	
}
?>