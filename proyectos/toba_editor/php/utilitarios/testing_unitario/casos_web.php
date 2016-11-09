<?php
	
class casos_web extends toba_ci
{
	protected $selecciones;
	static private $path_autoload_sel = '/php/testing/selenium/test_selenium_autoload.php';
	
	function ini()
	{		
		$proyecto = toba_editor::activado() ? toba_editor::get_proyecto_cargado() : toba::proyecto()->get_id();
		$path = toba::instancia()->get_path_proyecto($proyecto);
		if (file_exists($path. self::$path_autoload_sel)) {
			require_once($path. self::$path_autoload_sel);					
			spl_autoload_register(array('test_selenium_autoload', 'cargar' ));
		}
	}
	
	function mantener_estado_sesion()
	{
		$atributos = parent::mantener_estado_sesion();
		$atributos[] = 'selecciones';
		return $atributos;
	}
	
	function conf__lista_archivos($cuadro)
	{
		$cuadro->colapsar();
		$lista = array();
		foreach (toba_test_lista_casos::get_casos() as $caso) {
			if (in_array($caso['id'], $this->selecciones['casos'])) {
				$lista[] = $caso;
			}
		}
		return $lista;
	}
	
	function evt__lista_archivos__abrir($caso_sel)
	{
		foreach (toba_test_lista_casos::get_casos() as $caso) {
			if ($caso['id'] == $caso_sel['id']) {
				$archivo = new toba_archivo_php($caso['archivo']);
				$archivo->abrir();
			}	
		}	
	}
	
	function evt__seleccion__modificacion($selecciones)
	{
		$this->selecciones = $selecciones;
	}

	function conf__seleccion()
	{
		if (isset($this->selecciones)) {
			return $this->selecciones;
		}
	}	
	
	function get_selecciones()
	{
		return $this->selecciones;	
	}

}

####################################################################################

class pantalla_testing extends toba_ei_pantalla 
{
	function generar_layout()
	{
		$selecciones = $this->controlador->get_selecciones();
		echo "<div style='background-color: white; border: 1px solid black; text-align: left; padding: 15px'>";
		try {
			//Se construye un suite por categoria que tenga test seleccionados
			foreach (toba_test_lista_casos::get_categorias() as $categoria) {
				$test = new GroupTest($categoria['nombre']);
				$hay_uno = false;
				foreach (toba_test_lista_casos::get_casos() as $caso) {
					if ($caso['categoria'] == $categoria['id'] && in_array($caso['id'], $selecciones['casos'])) {
						$hay_uno = true;
						require_once($caso['archivo']);
						$test->addTestCase(new $caso['id']($caso['nombre']));
					}
				}		
				if ($hay_uno) {
					
					//--- COBERTURA DE CODIGO (OPCIONAL) ----					
					if (function_exists('xdebug_start_code_coverage')) {
					    xdebug_start_code_coverage();
					}
					//-------
										
					$test->run(new toba_test_reporter());
					
					//--- COBERTURA DE CODIGO (OPCIONAL) ----
					$arch = 'PHPUnit2/Util/CodeCoverage/Renderer.php';
					$existe = toba_manejador_archivos::existe_archivo_en_path($arch);
					if (function_exists('xdebug_start_code_coverage') && $existe) {
						require_once($arch);
						$cubiertos = xdebug_get_code_coverage();
						//Se limpian las referencias a simpletest y a librerias de testing en gral.
						$archivos = array();
						foreach (array_keys($cubiertos) as $archivo) {
							if (! strpos($archivo, 'simpletest') 
									&& ! strpos($archivo, 'PHPUnit')
									&& ! strpos($archivo, 'testing_automatico/')
									&& ! strpos($archivo, '/test_')) {
								$archivos[$archivo] = $cubiertos[$archivo];
							}
						}
						$cc = PHPUnit2_Util_CodeCoverage_Renderer::factory('HTML', array('tests' => $archivos));
						$path_temp = toba::proyecto()->get_path_temp_www();
						$salida = $path_temp['real'] .'/cobertura.html';
						$cc->renderToFile($salida);
						echo "<a href='". toba::escaper()->escapeHtmlAttr($path_temp['browser'] . '/cobertura.html') ."' target='_blank'>Ver cobertura de código</a>";
					}
					//-------				
				}
			}
		} catch (Exception $e) {
			if (method_exists($e, 'mensaje_web')) {
				echo ei_mensaje($e->mensaje_web(), 'error');
			} else {
				echo $e;
			}
		}
		echo '</div><br>';
		$this->dep('lista_archivos')->generar_html();		
	}	
}

####################################################################################

class pantalla_seleccion  extends toba_ei_pantalla
{
	function generar_layout()
	{
		parent::generar_layout();
	
		$opciones = array('param_html' => array('texto' => 'Testing Selenium'));
		$test_selenium = toba::vinculador()->get_url('toba_editor', 30000025, null, $opciones);		
		echo '<br>'.$test_selenium;
	}
	
}
?>