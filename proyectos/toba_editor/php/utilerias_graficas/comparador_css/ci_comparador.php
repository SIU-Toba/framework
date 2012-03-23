<?php
require_once(toba_dir() . '/php/3ros/cssparser.php');
//----------------------------------------------------------------
class ci_comparador extends toba_ci
{
	protected $estilos;
	protected $contenidos = array();
	protected $analisis;
	
	function mantener_estado_sesion()
	{
		$propiedades = parent::mantener_estado_sesion();
		$propiedades[] = 'estilos';
		return $propiedades;
	}

	function get_contenido($estilo)
	{
		if (!isset($this->contenidos[$estilo])) {
			$candidato = toba::instalacion()->get_path()."/www/css/$estilo.css";
			if (file_exists($candidato)) {
				$archivo = $candidato;
			} else {
				//Si el archivo no esta en toba, esta en algun proyecto	
				$proyectos = toba_info_instancia::get_proyectos_con_estilo($estilo);
				foreach ($proyectos as $proyecto) {
					$pro = $proyecto['proyecto'];
					$candidato = toba::instancia()->get_path_proyecto($pro)."/www/css/$estilo.css";
					if (file_exists($candidato)) {
						$archivo = $candidato;
					}
				}
			}
			if (! isset($archivo)) {
				throw new toba_error("No se encuentra el archivo del estilo $estilo");
			}
			$this->contenidos[$estilo] = file_get_contents($archivo);
		}
		return $this->contenidos[$estilo];
	} 
	
	function obtener_estilos($plantilla)
	{
		$css = new cssparser();
		@$css->ParseStr($plantilla);
		$res = array_unique(array_keys($css->css));
		sort($res);
		return $res;
	}
	
	function analizar_diferencias()
	{
		if (isset($this->analisis)) {
			return;	
		}	
		$est_origen = $this->obtener_estilos($this->get_contenido($this->estilos['origen']));
		$est_destino = $this->obtener_estilos($this->get_contenido($this->estilos['destino']));
		$this->analisis = array('faltantes' => array(), 'sobrantes' => array());
		//Busca los estilos faltantes
		foreach ($est_origen as $estilo) {
			if (!in_array($estilo, $est_destino)) {
				$this->analisis['faltantes'][] = array('estilo'=>$estilo);
			}
		}
		//Busca la estilos sobrantes
		foreach ($est_destino as $estilo) {
			if (!in_array($estilo, $est_origen)) {
				$this->analisis['sobrantes'][] = array('estilo'=>$estilo);	
			}
		}
		
	}

	//-------------------------------------------------------------------
	//--- Eventos
	//-------------------------------------------------------------------

	//----------------------------- archivos -----------------------------
	function conf__archivos()
	{
		if (isset($this->estilos)) {
			return $this->estilos;	
		}
	}

	function evt__archivos__modificacion($registro)
	{
		$this->estilos = $registro;
		$this->get_contenido($registro['origen']);
		$this->get_contenido($registro['destino']);		
	}

	
	//------------------------------ faltantes -----------------------------------
	function conf__faltantes()
	{
		$this->analizar_diferencias();
		return $this->analisis['faltantes'];
	}
	
	//------------------------------ sobrantes -----------------------------------
	function conf__sobrantes()
	{
		$this->analizar_diferencias();
		return $this->analisis['sobrantes'];
	}	
	
}

?>