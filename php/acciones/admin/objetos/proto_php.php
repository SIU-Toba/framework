<?
	if($editable = $this->zona->obtener_editable_propagado()){
	//--> Estoy navegando la ZONA con un editable...
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();

//-------------------  Busco la informacion ----------------

		$where = array("(objeto_proyecto = '".$editable[0]."') ",
						"(objeto = '".$editable[1]."') ");
		//Busco Lo GENERAL
		$sql = "SELECT * FROM apex_objeto_proto %w%";
		$sql = sql_agregar_clausulas_where($sql, $where);
		$general = recuperar_datos($sql);
		//ei_arbol($metodos);	
		if($general[0]=="ok"){
			//ei_arbol($general[1],"General");
			$general = $general[1];
		}
		//Busco propiedades
		$sql = "SELECT * FROM apex_objeto_proto_propiedad %w% ORDER BY orden";
		$sql = sql_agregar_clausulas_where($sql, $where);
		$propiedades = recuperar_datos($sql);
		//ei_arbol($propiedades);	
		if($propiedades[0]=="ok"){
			//ei_arbol($propiedades[1],"Propiedad");	
			$propiedades = $propiedades[1];
		}
		//Busco METODOS
		$sql = "SELECT * FROM apex_objeto_proto_metodo %w% ORDER BY orden";
		$sql = sql_agregar_clausulas_where($sql, $where);
		$metodos = recuperar_datos($sql);
		//ei_arbol($metodos);	
		if($metodos[0]=="ok"){
			//ei_arbol($metodos[1],"Metodos");	
			$metodos = $metodos[1];
		}

//-------------------  Genero el PHP ----------------

		$php = "\n";
		$php .= "class " . $this->zona->editable_info["subclase"];
		$php .= " extends " . $this->zona->editable_info["clase"] ."\n";
		$php .= "{\n";


		//$php .= "//-------- Propiedades ------\n";
		$php .= "	\n";
		for($a=0;$a<count($propiedades);$a++)
		{
			$php .= "	var " . $propiedades[$a]["propiedad"] .";\n";
		}
		$php .= "	\n";

		//$php .= "//-------- Constructor ------\n";
		$php .= "	function " . $this->zona->editable_info["subclase"] ."()\n";
		$php .= "	{\n";
		$php .= "		parent::".$this->zona->editable_info["clase"]."();\n";
		$php .= "	}\n";			
		$php .= "	//-------------------------------------------------------------\n";			
		$php .= "	\n";			

		//$php .= "//-------- Metodos ------\n";
		for($a=0;$a<count($metodos);$a++)
		{
			$php .= "	function " . $metodos[$a]["metodo"] ."()\n";
			$php .= "	{\n";
			$php .= "	}\n";			
			$php .= "	//-------------------------------------------------------------\n";			
			$php .= "	\n";			
		}
		$php .= "}\n";
		
		highlight_string($php);

		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}
?>