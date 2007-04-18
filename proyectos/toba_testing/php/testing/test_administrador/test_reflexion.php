<?php

class test_reflexion extends test_toba
{
	protected $padre;
	protected $hijo;
	
	function get_descripcion()
	{
		return "Creación de archivos y clases PHP";
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
	function test_creacion_archivo_y_directorio()
	{
		$path = dirname(__FILE__)."/dir1/dir2/archivo.php";
		$archivo = new toba_archivo_php($path);
		$archivo->crear_basico();
		$this->assertTrue(file_exists($path));
		
		//Limpiar el resultado
		unlink($path);
		rmdir(dirname(__FILE__)."/dir1/dir2");
		rmdir(dirname(__FILE__)."/dir1");		
	}
	
	function test_creacion_archivo_y_generacion_clase()
	// El archivo no existe en absoluto
	{
		$this->hijo = "hijo_vacio";
		$this->padre = "padre_hijo_vacio";

		//Se crea el archivo del hijo
		$archivo = new toba_archivo_php($this->path_hijo());
		$archivo->crear_basico(); 

		//Se genera la subclase
		$padre = new toba_archivo_php($this->path_padre());
		$clase = new toba_clase_php($this->hijo, $archivo, $this->padre, $this->path_padre());
		$clase->set_meta_clase( new info_componente(array()));
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
		$archivo = new toba_archivo_php($this->path_hijo());
		$padre = new toba_archivo_php($this->path_padre());
		$clase = new toba_clase_php($this->hijo, $archivo, $this->padre, $this->path_padre());
		$clase->set_meta_clase( new info_componente(array()));		
		$clase->generar($this->generar_todo()); 

		//Se incluyen y se verifica que funcionan correctamente
		$padre->incluir();
		$archivo->incluir();
		$clase = new ReflectionClass($this->hijo);
		$this->AssertEqual($this->hijo, $clase->getName());
	}	


}


?>