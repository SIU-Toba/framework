<?
//ef_linea_titulo
class ef_linea_titulo extends ef
{
	function ef_linea_titulo($padre,$nombre_formulario,$id,$etiqueta,$descripcion,$dato,$obligatorio,$parametros)
	{
		$this->padre = $padre;
		$this->nombre_formulario = $nombre_formulario;
		$this->id = $id;
		$this->etiqueta = $etiqueta;
		$this->descripcion = $descripcion;
		//Color de la linea
		$this->linea_color = (isset($parametros["color"]))? $parametros["color"] : "000000";
		//Grosor de la linea
		$this->linea_grosor = (isset($parametros["ancho"]))? $parametros["ancho"] : 2;
		//Título
		$this->titulo = (isset($parametros["titulo"]))? $parametros["titulo"] : "";		
		//El identificador del EF en el cliente es nombre_formulario + codigo del padre + codigo del EF
		$this->id_form = $this->nombre_formulario . $this->padre[1] . $this->id;
        $this->dato = $dato;
	}
	
//-------------- ACCESO al ESTADO -----------------

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

//-------------------- VALIDAR ESTADO --------------------------
	
    function validar_estado()
    //Validacion interna del EF
    {
		$this->validacion = true;
        return array(true,"");
    }
	
//-------------------- INTERFACE --------------------------
    
    function obtener_javascript()
    //Devuelve el javascript del elemento
    {
        return "";
    }

	function envoltura_std()
	//Envoltura normal
	{
		global $solicitud;
		$estilo = "ef-etiqueta";
	   
		echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
		echo "<tr><td>".gif_nulo(3,3)."</td></tr>\n";
		echo "<tr><td bgcolor='#{$this->linea_color}' height='$this->linea_grosor'>".gif_nulo($this->linea_grosor,1)."</td></tr>\n";
		if ($this->titulo != "") {
			echo "<tr><td class='$estilo'>{$this->titulo}</td></tr>\n";
			echo "<tr><td bgcolor='#{$this->linea_color}' height='$this->linea_grosor'>".gif_nulo($this->linea_grosor,1)."</td></tr>\n";
		}
		echo "<tr><td>".gif_nulo(3,3)."</td></tr>\n";
		echo "</table>\n";
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
?>