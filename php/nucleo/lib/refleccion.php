<?

class refleccion
{
	
	private $clase;
	private $metodo;
	private $propiedad;
	
	private $datos;
	private $clase_origen;
	private $objeto;
	
	
	public function __construct($clase)
	{
		$this->clase_origen = $clase;
		$this->set_clase($clase);
	}

	//----------------------------------------------------------------------------------------------------------------------------
	// CLASES
	private function set_clase($clase)
	{
		try {
			$this->clase =  new ReflectionClass($clase);
		}catch (ReflectionException $e) {
			$mensaje = "La clase \"{$clase}\" no existe";
			ei_arbol($mensaje, 'No se puede setear la clase!');
		}
	}
	
	private function get_padre($clase)
	{
		$this->set_clase($clase);
		if($this->clase->getParentClass())
		{
			return $this->clase->getParentClass()->getName();
		}
		return false;
	}

	private function obtener_clase($nombre, $tipo)
	{
		// Se puede obtener la clase atravez del metodo o de la propiedad
		switch($tipo)
		{
			case 'm' :
				$metodo = $this->clase->getName();
				$this->set_metodo($metodo, $nombre);
				return $this->metodo->getDeclaringClass()->getName();
			break;
			case 'p' :
				$propiedad = $this->clase->getName();
				$this->set_propiedad($propiedad, $nombre);
				return $this->propiedad->getDeclaringClass()->getName();
			break;
			default : return "El tipo debe ser 'm' para metodo o 'p' para propiedad";
		}
	}
	
	public function obtener_clase_origen($nombre, $tipo)
	{
		$this->set_clase($this->clase_origen);
		return $this->obtener_clase($nombre, $tipo);
	}

	//----------------------------------------------------------------------------------------------------------------------------
	// PROPIEDADES
	private function set_propiedad($clase, $propiedad)
	{
		try{
			return $this->propiedad =  new ReflectionProperty($clase, $propiedad);
		}catch (ReflectionException $e) {
		    	$mensaje = "La propiedad \"{$propiedad}\" no existe";
			ei_arbol($mensaje, 'No se puede setear la propiedad!');
		}
	}
	
	public function obtener_propiedad_tipo ($nombre_propiedad)
	{
		$this->objeto = $this->set_propiedad($this->clase_origen, $nombre_propiedad);
		return $this->obtener_tipo($nombre_propiedad);
	}
	
	public function obtener_propiedades($clase)
	{
		// Puedo obtener todas los metodos de la clase dada.
		$datos = NULL;
		$this->set_clase($clase);
		$propiedades = $this->clase->getProperties();
		foreach($propiedades as $propiedad)
		{
			$clase_propiedad = $this->obtener_clase($propiedad->getName(), 'p');
			if($this->clase->getName() == $clase_propiedad)
			{
				$datos[] = $propiedad->getName();
			}
		}
		return $datos;
	}
	

	//----------------------------------------------------------------------------------------------------------------------------
	// METODOS
	private function set_metodo($clase, $metodo)
	{
		try{
			return $this->metodo =  new ReflectionMethod($clase, $metodo);
		}catch (ReflectionException $e) {
			$mensaje = "El metodo \"{$metodo}\" no existe";
			ei_arbol($mensaje, 'No se puede setear el metodo!');
		}
	}
	
	public function obtener_metodo_tipo ($nombre_metodo)
	{
		$this->objeto = $this->set_metodo($this->clase_origen, $nombre_metodo);
		return $this->obtener_tipo($nombre_metodo);
	}
	
	private function get_metodos($clase)
	{
		$this->set_clase($clase);
		$metodos = $this->clase->getMethods();
		foreach($metodos as $metodo)
		{
			$clase_metodo = $this->obtener_clase($metodo->getName(), 'm');
			if($this->clase->getName() == $clase_metodo)
			{
				$datos[] = $metodo->getName();	
			}
		}
		return $datos;
	}
	
	public function obtener_metodos($clase)
	{
		// Puedo obtener todas los metodos de la clase dada.
		$metodos = $this->get_metodos($clase);
		for($i=0;$i<count($metodos);$i++)
		{
			$matriz_parametros = $this->get_parametros($this->clase->getName(), $metodos[$i]);
			$parametros = NULL;
			for($e=0;$e<count($matriz_parametros);$e++)
			{
				$parametros .= $matriz_parametros[$e]->getName();
				if(isset($matriz_parametros[$e+1]))
				{
					$parametros .= ", ";
				}
			}
			$datos[] = $metodos[$i] . "( ". $parametros ." )";	
		}
		return $datos;
	}
	

	//----------------------------------------------------------------------------------------------------------------------------
	// PARAMETROS
	private function get_parametros($clase, $metodo)
	{
		// Para obtener los parametros necesito saber de que metodo en que clase.
		$this->set_clase($clase);
		return $this->clase->getMethod($metodo)->getParameters();
	}
	
	public function obtener_parametros ($metodo)
	{
		// Para saber los parametros necesito saber el metodo
		$clase = $this->obtener_clase_origen($metodo, 'm');
		$matriz_parametros = $this->get_parametros($clase, $metodo);
		$parametros = NULL;
				for($i=0;$i<count($matriz_parametros);$i++)
				{
					$parametros .= $matriz_parametros[$i]->getName();
					if(isset($matriz_parametros[$i+1]))
					{
						$parametros .= ", ";
					}
				}
				$datos[] = $parametros;	
		return $datos;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// FUNCIONES SOPORTE
	
	public function obtener_tipo ($nombre)
	{
		if($this->objeto->isPublic())
		{
			return $nombre . " = Public";
		}
		if($this->objeto->isPrivate())
		{
			return $nombre . " = Private";
		}
		if($this->objeto->isProtected())
		{
			return $nombre . " = Protected";
		}
		if($this->objeto->isStatic())
		{
			return $nombre . " = Static";
		}
	}
	
	
	function obtener_herencia($nombre, $tipo)
	{
		// Para obtener la herencia necesito:
		//	1 - una clase
		//	2 - un metodo
		
		switch($tipo)
		{
			case 'c' :
				$clase_padre = $this->get_padre($nombre);
				if($clase_padre)
				{
					$this->datos[] = $clase_padre;
					$this->obtener_herencia($clase_padre, $tipo);
				}
				return $this->datos;
			break;
			case 'm' :
				//Borro el contendio de $this->datos ya que voy a utilizar esta funcion con el tipo 'c'
				$this->datos = NULL;
				$clases = $this->obtener_herencia($this->clase_origen, 'c');
				for($i=0;$i<count($clases);$i++)
				{
					$metodos = $this->get_metodos($clases[$i]);
					for($e=0;$e<count($metodos);$e++)
					{
						if($nombre == $metodos[$e])
						{
							$datos[] = Array(	'clase' 	=> 	$clases[$i], 
											'metodo'	=> 	$metodos[$e]);
						}
					}
				}
				return $datos;
			break;
			default : return "El tipo debe ser 'c' para Clase o 'm' para Metodo.";
		}
	}
}	

?>