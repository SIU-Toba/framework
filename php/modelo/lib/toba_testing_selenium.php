<?php


/**
 * Permite generar casos de test para selenium
 * 
 * Ejemplo de uso
 *  $testing = new toba_testing_selenium('mapuche', 'http://192.168.132.209/mapuche/1.2');
 *  $testing->test_operaciones(true);
 *  $testing->guardar('C:\\SIU\\2009\\testeo2009\\selenium\\generacionAutomatica\\Test1.html');
 *  echo $testing->get_salida();
 *
 */
class toba_testing_selenium
{
	protected $url_base;
	protected $salida;
	protected $titulo = '';
	protected $proyecto;
	protected $volver_inicial = false;
	
	
	function __construct($proyecto, $url_base)
	{
		$this->url_base = $url_base;
		$this->proyecto = $proyecto;
	}
	
	
	//-------------------------------------------------------
	//---- API BASICA
	//-------------------------------------------------------	
	
	function guardar($path)
	{
		if (! file_exists(dirname($path))) {
			toba_manejador_archivos::crear_arbol_directorios(dirname($path));
		}
		$ok = file_put_contents($path, $this->salida);
		if ($ok === false) {
			throw new toba_error("Imposible guardar archivo '$path'");
		}
	}	
	
	function get_salida()
	{
		return $this->salida;
	}
	
	function set_titulo($titulo) 
	{
		$this->titulo = $titulo;
	}
	
	
	//-------------------------------------------------------
	//---- GENERACION
	//-------------------------------------------------------
	
	
	function test_operaciones($volver_inicial=false)
	{
		$this->volver_inicial = $volver_inicial;
		$this->gen_encabezado();
		$this->gen_comando('open', $this->url_base);
		$this->test_operaciones_recorrer($this->get_arbol_completo());
		$this->gen_pie();
	}
	
	protected function test_operaciones_recorrer($arbol)
	{
		if (! empty($arbol)) {
			foreach ($arbol as $hijo) {
				$this->test_operaciones_nodo($hijo);
				$this->test_operaciones_recorrer($hijo['hijos']);
			}
		}
	}
	
	protected function test_operaciones_nodo($hijo)
	{
		if ($hijo['carpeta']===0) {
			if ($hijo['nombre']==='Salir') {
				$this->gen_comando('click','link='.$hijo['nombre']);
			} else {
				$this->gen_comando('clickAndWait','link='.$hijo['nombre']);
			    $this->gen_comando('verifyTitle', 'regexp:.*'.$hijo['nombre'].'.*');
			    $this->gen_comando('assertTextNotPresent', 'Se han encontrado los siguientes problemas');
			    if ($this->volver_inicial) {
				   $this->gen_comando('open', $this->url_base);
			    }			
			}
		}
	}
	
	protected function gen_encabezado()
	{
		$this->salida .= "<html>
					<head>
					<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-2'>
					<title>{$this->titulo}</title>
					</head>
					<body>
						<table border='1'>
							<thead><tr><td rowspan='1' colspan='3'>{$this->titulo}</td></tr></thead>
							<tbody>";		
	}
	
	protected function gen_pie()
	{
		$this->salida .= "\n</tbody>
				</table>
			</body>
			</html>\n";		
	}	
	
	protected function gen_comando($comando, $objeto, $valor='')
	{
		$this->salida .= "<tr><td>$comando</td><td>$objeto</td><td>$valor</td></tr>\n";
	} 
		
	
	//-------------------------------------------------------
	//---- LECTURA METADATOS
	//-------------------------------------------------------	
	
	protected function get_arbol($item)
	{
		$hijos = $this->get_items_hijos($item);
		foreach ($hijos as $nro => $hijo) {
			$hijos[$nro]['hijos'] = $this->get_arbol($hijo['item']);
		}
		return $hijos;		 
	}
	
	protected function get_items_hijos($padre) 
	{
		$proyecto = toba::instancia()->get_db()->quote($this->proyecto);
		$padre = toba::instancia()->get_db()->quote($padre);
		$sql = "SELECT 
					item, 
					nombre,
					carpeta, 
					'' as hijos 
				FROM 
					apex_item 
				WHERE 
						proyecto = $proyecto
					AND padre = $padre
					AND menu=1 
				ORDER BY orden";
		return toba::instancia()->get_db()->consultar($sql);
	}	
	
	protected function get_arbol_completo()
	{
		return $this->get_arbol(toba_info_editores::get_item_raiz($this->proyecto));
	}
		

}


?>
