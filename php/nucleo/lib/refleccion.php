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
	var $clase_origen;
	var $metodo;
	var $propiedad;
	var $datos;
	
	
	function __construct($clase)
	{
		$this->clase_origen = $clase;
		$this->set_clase($clase);
	}
	
	private function set_clase($clase)
	{
		$this->clase =  new ReflectionClass($clase);
	}
	
	private function set_metodo($metodo)
	{
		$this->metodo =  new ReflectionMethod($metodo);
	}
	
	private function set_propiedad($propiedad)
	{
		$this->propiedad =  new ReflectionPropierty($propiedad);
	}

	//----------------------------------------------------------------------------------------------------------------------------
	// FUNCIONES PROPIAS 
	
	public function funciones ()
	{
		return $this->get_metodos($this);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// OBTENER PROPIEDADES
	
	public function obtener_propiedades ()
	{
		$this->set_clase($this->clase_origen);
		$datos = NULL;
		$this->set_clase($this->clase_origen);
		$propiedades = $this->clase->getProperties();
		foreach ($propiedades as $valor)
		{
			$datos[] = $valor->getName();
		}
		return $datos;
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// OBTENER PARAMETROS
	
	private function get_parametros ($metodo)
	{
		$datos=NULL;
		$parametros = $this->clase->getMethod($metodo)->getParameters();
		 for($i=0;$i< count($parametros);$i++)
		{
			$datos .=  $parametros[$i]->getName();
			
			//No funca bien
			if($parametros[$i]->allowsNull())
			{
				//$datos .= "=NULL";
			}
			if( isset($parametros[$i+1]) )
			{
				$datos .= ", ";
			}
		} 
		return $datos;
	}
	
	public function obtener_parametros($metodo)
	{
		$this->set_clase($this->clase_origen);
		return $this->get_parametros($metodo);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// OBTENER METODOS
	
	private function get_metodos ($clase)
	{
		$datos = NULL;
		$this->set_clase($clase);
		$metodos = $this->clase->getMethods();
		foreach ($metodos as $metodo)
		{
			if($metodo->isPublic())
			{
				$nombre = $metodo->getName();
				$datos[] = $nombre . "(" .  $this->get_parametros($nombre) . ")";
			}
		}
		return $datos;
	}

	
	public function obtener_metodos ()
	{
		return $this->get_metodos($this->clase_origen);
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// OBTENER HERENCIA DE CLASES
	
	private function iterar_herencia()
	{
		if($this->clase->getParentClass())
		{
			$this->datos[] = $this->clase->getParentClass()->getName();
			$clase_padre = end($this->datos);
			$this->set_clase($clase_padre);
			$this->iterar_herencia ();
		}
		return $this->datos;
	}
	
	public function obtener_clases ()
	{
		$this->set_clase($this->clase_origen);
		return $this->iterar_herencia();
	}
	
	//----------------------------------------------------------------------------------------------------------------------------
	// OBTENER ORIGEN
	
	public function obtener_origen($valor, $tipo)
	{
		switch($tipo)
		{
			case 'c' :
				$this->set_clase($this->clase_origen);
				$datos['Clase'] = $valor;
				$this->obtener_clases();
				 for($i=0;$i<count($this->datos);$i++)
				 {
				 	if($this->datos[$i] == $valor)
				 	{
				 		$this->set_clase($this->datos[$i]);
				 		$datos['ruta_archivo'] = $this->clase->getFileName();
				 		$datos['linea'] = $this->clase->getStartLine() . " - " . $this->clase->getEndLine();
				 		
				 	}
				 }
			break;
			case 'm' :
				$this->set_clase($this->clase_origen);
				$datos['Metodo'] = $valor . '(' . $this->get_parametros($valor) . ')';
				$metodos = $this->clase->getMethods();
				 for($i=0;$i<count($metodos);$i++)
				 {
				 	if($metodos[$i]->getName() == $valor)
				 	{
				 		$this->set_clase($metodos[$i]->getDeclaringClass()->getName());
				 		$datos['ruta_archivo'] = $this->clase->getFileName();
				 		$datos['linea'] = $this->clase->getMethod($metodos[$i]->getName())->getStartLine() . " - " . $this->clase->getMethod($metodos[$i]->getName())->getEndLine();
				 	}
				 }
			break;
			case 'p' :
				$this->set_clase($this->clase_origen);
				$datos['Propiedad'] = "$" . $valor;
				 $propiedades = $this->clase->getProperties();
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
	//----------------------------------------------------------------------------------------------------------------------------
}

?>