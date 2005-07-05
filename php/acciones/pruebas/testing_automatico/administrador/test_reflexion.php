<?php
require_once('nucleo/lib/reflexion/archivo_php.php');
require_once('nucleo/lib/reflexion/clase_php.php');

class test_reflexion extends test_toba
{
	protected $padre;
	protected $hijo;

	function setUp()
	{
	}
	
	function tearDown()
	{
		$this->borrar_archivo();
	}
	
	function path_hijo()
	{
		return dirname(__FILE__)."/archivo_{$this->hijo}.php";	
	}
	
	function path_padre()
	{
		return dirname(__FILE__)."/archivo_{$this->padre}.php";	
	}
	
	function borrar_archivo()
	{
		if (file_exists($this->path_hijo()))
			unlink($this->path_hijo());
	}
	
	function crear_archivo_hijo($contenido)
	{
		$fp = fopen($this->path_hijo(), 'w');
		fwrite($fp, $contenido);
		fclose($fp);
	}
	
	function generar_todo()
	{
		return array(
			'constructor' => 1,
			'basicos' => 1,
			'eventos' => 2,
			'nivel_comentarios' => 3
		);	
	}
	
	//--------------------------------------------------------------------------------
	//---------------GENERACION DEL ARCHIVO Y SUBCLASE BASICA-------------------------
	//--------------------------------------------------------------------------------
	function test_creacion_archivo_y_generacion_clase()
	// El archivo no existe en absoluto
	{
		$this->hijo = "hijo_vacio";
		$this->padre = "padre_hijo_vacio";

		//Se crea el archivo del hijo
		$archivo = new archivo_php($this->path_hijo());
		$archivo->crear_basico(); 

		//Se genera la subclase
		$padre = new archivo_php($this->path_padre());
		$clase = new clase_php($this->hijo, $archivo, $this->padre, $this->path_padre());
		$clase->generar($this->generar_todo()); 

		//Se incluyen y se verifica que funcionan correctamente
		$padre->incluir();
		$archivo->incluir();
		$clase = new ReflectionClass($this->hijo);
		$this->AssertEqual($this->hijo, $clase->getName());
	}
	
	function test_generacion_clase_archivo_con_codigo_previo()
	// El hijo ya contiene un código, tendría que insertar la subclase sin molestar
	{
		$this->hijo = "hijo_codigo_previo";
		$this->padre = "padre_hijo_codigo_previo";

		$contenido = 
"<?php
	/*
	* 
	*/
	class clase_previa
	{
		function esta_es_una_funcion_previa()
		{
			//Esto trata de engañar al parser del archivo
			?><?php
		}
	}

?>";
		//Se crea el archivo del hijo
		$this->crear_archivo_hijo($contenido);
		
		//Se genera la subclase
		$archivo = new archivo_php($this->path_hijo());
		$padre = new archivo_php($this->path_padre());
		$clase = new clase_php($this->hijo, $archivo, $this->padre, $this->path_padre());
		$clase->generar($this->generar_todo()); 

		//Se incluyen y se verifica que funcionan correctamente
		$padre->incluir();
		$archivo->incluir();
		$clase = new ReflectionClass($this->hijo);
		$this->AssertEqual($this->hijo, $clase->getName());
	}	


	function test_generacion_clase_archivo_con_include_previo()
	// El hijo ya contiene el include, debería obviar insertarlo nuevamente
	{
		$this->hijo = "hijo_include_previo";
		$this->padre = "padre_hijo_include_previo";

		$contenido = 
"<?php
	//Esta inclusión es anterior
	require_once('{$this->path_padre()}');
	//Esta inclusión es anterior	

?>";
		//Se crea el archivo del hijo
		$this->crear_archivo_hijo($contenido);
		
		//Se genera la subclase
		$archivo = new archivo_php($this->path_hijo());
		$padre = new archivo_php($this->path_padre());
		$clase = new clase_php($this->hijo, $archivo, $this->padre, $this->path_padre());
		$clase->generar($this->generar_todo()); 

		//Se incluyen y se verifica que funcionan correctamente
		$padre->incluir();
		$archivo->incluir();
		$clase = new ReflectionClass($this->hijo);
		$this->AssertEqual($this->hijo, $clase->getName());
		$cantidad = null;
		str_replace('once', '', file_get_contents($this->path_hijo()), $cantidad);
		$this->AssertEqual($cantidad, 1);
	}	
	
	//--------------------------------------------------------------------------------
	//---------------CUERPO DE LA SUBCLASE EN BASE AL ELEMENTO-TOBA-------------------
	//--------------------------------------------------------------------------------	
	function test_generacion_ci_con_metodos()
	{
		$nombre_clase = 'mi_ci';
		$clase = new clase_php($nombre_clase, '', 'objeto_ci', '');
		$clase->set_objeto('toba_testing', '1323');
		$opciones = array(
			'constructor' => 1,
			'basicos' => 1,
			'eventos' => 2,
			'nivel_comentarios' => 3
		);
		$codigo = $clase->generar_clase($this->generar_todo());
//		highlight_string("<?php\n $codigo \n");
		eval($codigo);
		
		//Pruebas 
		$mi_ci = new ReflectionClass($nombre_clase);		
		//-- Asegura que se haya heredado el constructor
		$this->AssertEqual($mi_ci->getConstructor()->getDeclaringClass(), $mi_ci);	
		//-- El mantener_estado_sesion debe estar heredado
		$this->AssertEqual($mi_ci->getMethod('mantener_estado_sesion')->getDeclaringClass(), $mi_ci);			
	}
}


?>