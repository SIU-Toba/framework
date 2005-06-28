<?php
require_once('nucleo/browser/clases/objeto_ci.php');
require_once('test_toba.php');
require_once('reporter_toba.php');
require_once('lista_casos.php');

class casos_web extends objeto_ci
{
	protected $etapa = 1;
	protected $selecciones;
	
	function __construct($id)
	{
		parent::__construct($id);
	}
	
	
	function mantener_estado_sesion()
	{
		$atributos = parent::mantener_estado_sesion();
		$atributos[] = "etapa";
		$atributos[] = "selecciones";
		return $atributos;
	}

	function get_lista_eventos()
	{
		$eventos = parent::get_lista_eventos();
		switch ($this->get_etapa_actual())
		{
			case 1:
				$eventos += eventos::duplicar(eventos::ci_procesar("&Ejecutar -->"), 'ejecutar');
					break;
			case 2:
				$eventos += eventos::duplicar(eventos::ci_cancelar("<-- &Volver"), 'volver');				
				$refrescar = eventos::duplicar(eventos::ci_procesar("R&efrescar"), 'ejecutar');	
				$refrescar['ejecutar']['imagen'] = recurso::imagen_apl('refrescar.gif');
				$eventos += $refrescar;
					break;
		}
		return $eventos;
	}
	
	function get_etapa_actual()
	{
		return $this->etapa;
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
	
	//Ejecutar los casos de test
	function evt__ejecutar()
	{
		$this->etapa = 2;
	}
	
	function evt__volver()
	{
		$this->etapa = 1;
	}	
	
	function obtener_html_contenido__2()
	{
		try {
			//Se construye un suite por categoria que tenga test seleccionados
			foreach (lista_casos::get_categorias() as $categoria) {
				$test = new GroupTest($categoria['nombre']);
				$hay_uno = false;
			    foreach (lista_casos::get_casos() as $caso) {
				    if ($caso['categoria'] == $categoria['id'] && in_array($caso['id'], $this->selecciones['casos'])) {
						$hay_uno = true;
				        require_once($caso['categoria']."/".$caso['id'].".php");
				        $test->addTestCase(new $caso['id']($caso['nombre']));
				    }
				}		
				if ($hay_uno)	
					$test->run(new reporter_toba());			
			}
		} catch (Exception $e) {
			if (method_exists($e, 'mensaje_web'))
				echo ei_mensaje($e->mensaje_web(), 'error');
			else
				echo $e;
		}
	
	}
}

?>