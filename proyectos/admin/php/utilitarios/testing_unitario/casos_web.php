<?php
require_once('utilitarios/testing_unitario/reporter_toba.php');
require_once('modelo/lib/testing_unitario/lista_casos.php');
require_once("nucleo/lib/reflexion/archivo_php.php");
require_once("nucleo/lib/manejador_archivos.php");

class casos_web extends objeto_ci
{
	protected $selecciones;
	
	function mantener_estado_sesion()
	{
		$atributos = parent::mantener_estado_sesion();
		$atributos[] = "selecciones";
		return $atributos;
	}
	
	function evt__lista_archivos__carga()
	{
		$this->dependencias['lista_archivos']->colapsar();
		$lista = array();
	    foreach (lista_casos::get_casos() as $caso) {
			if (in_array($caso['id'], $this->selecciones['casos'])) {
				$lista[] = $caso;
			}
		}
		return $lista;
	}
	
	function evt__lista_archivos__abrir($caso_sel)
	{
	    foreach (lista_casos::get_casos() as $caso) {
			if ($caso['id'] == $caso_sel['id']) {
				$archivo = new archivo_php($caso['archivo']);
				$archivo->abrir();
			}	
		}	
	}
	
	function evt__seleccion__modificacion($selecciones)
	{
		$this->selecciones = $selecciones;
	}

	function evt__seleccion__carga()
	{
		if (isset($this->selecciones))
			return $this->selecciones;
	}	
	
	function obtener_html_dependencias()
	{
		$test_js = toba::get_vinculador()->obtener_vinculo_a_item('admin', '/pruebas/testing_automatico_js',
																	 null, true);
		parent::obtener_html_dependencias();
		echo "<br>".$test_js;
	}
	
	function obtener_html_contenido__2()
	{
		echo "<div style='background-color: white; border: 1px solid black; text-align: left; padding: 15px'>";
		try {
			//Se construye un suite por categoria que tenga test seleccionados
			foreach (lista_casos::get_categorias() as $categoria) {
				$test = new GroupTest($categoria['nombre']);
				$hay_uno = false;
			    foreach (lista_casos::get_casos() as $caso) {
				    if ($caso['categoria'] == $categoria['id'] && in_array($caso['id'], $this->selecciones['casos'])) {
						$hay_uno = true;
						require_once($caso['archivo']);
				        $test->addTestCase(new $caso['id']($caso['nombre']));
				    }
				}		
				if ($hay_uno) {
					
					//--- COBERTURA DE CODIGO (OPCIONAL) ----					
					if (function_exists("xdebug_start_code_coverage")) {
					    xdebug_start_code_coverage();
					}
					//-------
										
					$test->run(new reporter_toba());
					
					//--- COBERTURA DE CODIGO (OPCIONAL) ----
					$arch = 'PHPUnit2/Util/CodeCoverage/Renderer.php';
					manejador_archivos::existe_archivo_en_path($arch);
					if (function_exists("xdebug_start_code_coverage") && $existe) {
						require_once($arch);
						$cubiertos = xdebug_get_code_coverage();
						//Se limpian las referencias a simpletest y a librerias de testing en gral.
						$archivos = array();
						foreach (array_keys($cubiertos) as $archivo) {
							if (! strpos($archivo, 'simpletest') 
									&&  ! strpos($archivo, 'PHPUnit')
									&& ! strpos($archivo,'testing_automatico/')
									&& ! strpos($archivo, '/test_')) {
								$archivos[$archivo] = $cubiertos[$archivo];
							}
						}
					    $cc =  PHPUnit2_Util_CodeCoverage_Renderer::factory('HTML',array('tests' => $archivos));
					    $path_temp = toba::get_hilo()->obtener_path_temp_www();
					    $salida = $path_temp['real'] ."/cobertura.html";
					    $cc->renderToFile($salida);
					    echo "<a href='{$path_temp['browser']}/cobertura.html' target='_blank'>Ver cobertura de código</a>";
					}
					//-------
				}
			}
		} catch (Exception $e) {
			if (method_exists($e, 'mensaje_web'))
				echo ei_mensaje($e->mensaje_web(), 'error');
			else
				echo $e;
		}
		echo "</div>";
		$this->obtener_html_dependencias();
	}
}

?>