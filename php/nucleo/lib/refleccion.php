<?
/*
//------------------------------------------------------//
//				FUNCIONES				//
//------------------------------------------------------//

	obtener_origen($valor, $tipo)
		c = Clase
		m = Metodo
		p = Propiedad
					ej: obtener_origen('nombre_metodo', 'm');
	
*/

class refleccion
{
	
	var $clase;
	var $metodo;
	var $propiedad;
	var $datos;
	
	
	function __construct($clase)
	{
		$this->set_clase($clase);
	}
	
	function set_clase($clase)
	{
		$this->clase =  new ReflectionClass($clase);
	}
	
	function set_metodo($metodo)
	{
		$this->metodo =  new ReflectionMethod($metodo);
	}
	
	function set_propiedad($propiedad)
	{
		$this->propiedad =  new ReflectionPropierty($propiedad);
	}
	
	//------------------------------------------------------//
	//				capacidad				//
	//------------------------------------------------------//
	
	function exportar ()
	{
		return $this->clase->export($this->clase);
	}
	
	function get_padre()
	{
		if ($this->clase->getParentClass()){
			return $this->clase->getParentClass()->getName();
		}else{
			return false;
		}
	}
	
	function get_metodos()
	{
		return $this->clase->getMethods();
	}
	
	function get_propiedades ()
	{
		return $this->clase->getProperties();
	}

	function get_propiedades_d ()
	{
		return $this->clase->getDefaultProperties();
	}

	function get_archivo ()
	{
		return $this->clase->getFileName();
	}
	

	
	
	//---------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------	
	//---------------------------------------------------------------------------------------	
	function imprimir ($valor)
	{
		print "<pre>";
		print_r ($valor);
		print "</pre>";
	}
	
	public function obtener_herencia_clases ()
	{
		if($this->get_padre())
		{
			$this->datos[] = $this->get_padre();
			$clase_padre = end($this->datos);
			$this->set_clase($clase_padre);
			$this->obtener_herencia_clases ();
		}
		return $this->datos;
	}
	
	public function obtener_origen($valor, $tipo)
	{
		switch($tipo)
		{
			case 'c' :
				$datos['Clase'] = $valor;
				$this->obtener_herencia_clases();
				 for($i=0;$i<count($this->datos);$i++)
				 {
				 	if($this->datos[$i] == $valor)
				 	{
				 		$this->set_clase($this->datos[$i]);
				 		$datos['ruta_archivo'] =  $this->clase->getFileName();
				 	}
				 }
			break;
			case 'm' :
				$datos['Metodo'] = $valor . '()';
				$metodos = $this->get_metodos();
				 for($i=0;$i<count($metodos);$i++)
				 {
				 	if($metodos[$i]->getName() == $valor)
				 	{
				 		$this->set_clase($metodos[$i]->getDeclaringClass()->getName());
				 		$datos['ruta_archivo'] = $this->clase->getFileName();
				 		$datos['linea'] = $this->clase->getMethod($metodos[$i]->getName())->getStartLine();
				 	}
				 }
			break;
			case 'p' :
				$datos['Propiedad'] = $valor;
				 $propiedades = $this->get_propiedades();
				 for($i=0;$i<count($propiedades);$i++)
				 {
				 	if($propiedades[$i]->getName() == $valor)
				 	{
				 		$this->set_clase($propiedades[$i]->getDeclaringClass()->getName());
				 		$datos['ruta_archivo'] = $this->clase->getFileName();
				 		
				 	}
				 }
			break;
		}
		return $datos;
	}
}

?>