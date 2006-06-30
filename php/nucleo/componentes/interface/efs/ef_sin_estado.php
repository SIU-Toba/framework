<?
//ef_linea_titulo

abstract class ef_sin_estado extends ef
{
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function set_estado()
	//Carga el estado interno
	{
		return false;
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		return null;
	}

	function activado()
	{
		return false;
	}

	function resetear_estado()
	//Devuelve el estado interno
	{
		return false;
	}
	
    function validar_estado()
    //Validacion interna del EF
    {
    	return true;
    }
	
    function obtener_javascript()
    //Devuelve el javascript del elemento
    {
        return "";
    }

	function get_consumo_javascript()
	//Esta funcion permite que un EF declare la necesidad de incluir
	//codigo javascript necesario para su correcto funcionamiento (generalmente javascript:
	//expresiones regulares comunes a varios EF, includes de manejo de fechas, etc...
	{
		return null;
	}
	
	function tiene_etiqueta()
	{
		return false;	
	}		
}
#####################################################################################
#####################################################################################

class ef_barra_divisora extends ef_sin_estado
{
	function get_input()
	{
		echo "<table class='tabla-0' width='100%'>\n";
		echo "<tr><td class='ef-barra_divisoria'>&nbsp;&nbsp;{$this->etiqueta}</td></tr>\n";
		echo "</table>\n";
	}
}

#####################################################################################
#####################################################################################

class ef_fieldset extends ef_sin_estado
{
	protected $fin;
	
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->fin = (isset($parametros['fieldset_fin'])) ? ($parametros['fieldset_fin'] == 1) : false;
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	static function get_parametros()
	{
		$parametros = array();
		$parametros["fieldset_fin"]["descripcion"]="¿Este ef marca el fin del fieldset?";
		$parametros["fieldset_fin"]["opcional"]=1;	
		$parametros["fieldset_fin"]["etiqueta"]="Fin del fieldset";			
		return $parametros;
	}	
	
	function get_input()
	{
		if(! $this->fin){
			echo "<fieldset title='{$this->etiqueta}'>";
		} else {
			echo "</fieldset>";
		}
	}
	
}	
#####################################################################################
#####################################################################################
?>