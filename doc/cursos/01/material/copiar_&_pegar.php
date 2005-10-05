<?
//##### 1 #

    $instancia["curso"][apex_db_motor] = "postgres7";
    $instancia["curso"][apex_db_profile] = "192.168.0.1";
    $instancia["curso"][apex_db_usuario] = "dba";
    $instancia["curso"][apex_db_clave] = "*dba-";
    $instancia["curso"][apex_db_base] = "curso_toba";
    
//##### 2 #

define('fuente_datos_defecto','curso');
require_once('lib/consulta.php');
require_once('lib/excepcion_curso.php');

//##### 3 #

		ei_arbol( consulta::get_jurisdicciones() );
		ei_arbol( consulta::get_paises() );
		ei_arbol( consulta::get_provincias('AR') );
		ei_arbol( consulta::get_localidades('U') );

//##### 3 #

	private $tabla;
	
	private function get_tabla() 
	{
		if(!isset($this->tabla)) {
			$this->cargar_dependencia("datos");
			$this->tabla = $this->dependencias["datos"];			
		}
		return $this->tabla;		
	}
	
//##### 4 #

	private $relacion;

	private function get_relacion()
	{
		if(!isset($this->relacion)) {
			$this->cargar_dependencia("datos");
			$this->relacion = $this->dependencias["datos"];			
		}
		return $this->relacion;		
	}
		
?>