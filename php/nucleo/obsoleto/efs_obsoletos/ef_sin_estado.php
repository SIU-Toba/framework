<?
//ef_linea_titulo

class ef_sin_estado extends ef
{
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		parent::ef($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function cargar_estado()
	//Carga el estado interno
	{
		return false;
	}

	function obtener_estado()
	//Devuelve el estado interno
	{
		return 'NULL';
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
	
	function obtener_info()
	//INFORMACION por DEFECTO: Valor simple
	{
		return "{$this->etiqueta}";
	}

    function validar_estado()
    //Validacion interna del EF
    {
		$this->validacion = true;
        return array(true,"");
    }
	
    function obtener_javascript()
    //Devuelve el javascript del elemento
    {
        return "";
    }

	function obtener_consumo_javascript()
	//Esta funcion permite que un EF declare la necesidad de incluir
	//codigo javascript necesario para su correcto funcionamiento (generalmente javascript:
	//expresiones regulares comunes a varios EF, includes de manejo de fechas, etc...
	{
		return null;
	}

	function obtener_interface()
	{
		$this->envoltura_std();
	}

    function obtener_html()
    {
        $this->envoltura_std();
    }

}
#####################################################################################
#####################################################################################

class ef_barra_divisora extends ef_sin_estado
{
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->linea_color = (isset($parametros["color"]))? $parametros["color"] : "000000";
		$this->linea_grosor = (isset($parametros["ancho"]))? $parametros["ancho"] : 2;
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}
	

	function envoltura_std()
	//Envoltura normal
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
	function __construct($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->tipo = (isset($parametros["tipo"]))? $parametros["tipo"] : "desconocido";
		parent::__construct($padre,$nombre_formulario, $id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros);
	}

	function envoltura_std()
	//Envoltura normal
	{
		if($this->tipo=="i"){
			echo "<fieldset title='{$this->etiqueta}'>";
		}elseif($this->tipo=="f"){
			echo "</fieldset>";
		}else{
			echo "ERROR! el tipo debe ser [i] o [f] (inicio, fin)";
		}
	}
	
}	
#####################################################################################
#####################################################################################
?>