<?
	//Esta pagina prueba el funcionamiento de la dimension ACTUAL (El editable de la zona)
	if($editable = $this->zona->get_editable()){
		$this->zona->obtener_html_barra_superior();
		//$this->zona->info();
		include("nucleo/obsoleto/efs_obsoletos/dimensiones.php");//Dimensiones
		include("nucleo/obsoleto/efs_obsoletos/dimensiones_restric.php");//Dimensiones

		//Creo el ARRAY que inicializa la DIMENSION
        $parametros = parsear_propiedades($this->zona->editable_info["inicializacion"]);
		$parametros["fuente"] = $this->zona->editable_info["fuente_datos"];
		//Es posible que la fuente de datos de la dimension no este disponible
		abrir_fuente_datos($parametros["fuente"]);
		//---- Creo las dimensiones
		$sentencia_creacion_dim = "\$dimension =& new ".
									"dimension_".$this->zona->editable_info['dimension_tipo'].
									"(	null, 'formulario' , '". 
                                    $this->zona->editable_info["dimension"] ."', '". 
                                    $this->zona->editable_info["nombre"] ."', '". 
                                    $this->zona->editable_info["descripcion"] ."',". 
									"'columna','1',\$parametros);";
		//echo $sentencia_creacion_dim . "<br>";
		eval($sentencia_creacion_dim);

		//cosas relacionadas con probar la dimension (POST)
		$nombre_submit = "probar_dimension";
		$valor_submit = "Probar";
		if( acceso_post() ){
			$dimension->cargar_estado();
			$where = $dimension->obtener_where();
			echo ei_mensaje($where,"WHERE");
		}
		
		//Formulario
		$vinculo = $this->vinculador->generar_solicitud(null,null,null,true);
		echo form::abrir("probar_dim",$vinculo);
		echo "<br><br><div  align='center'><table class='tabla-0' widht='500'>";
		echo "<tr><td>";
		$dimension->obtener_interface();
		echo "</tr></td>";
		echo "<tr><td>";
		echo form::submit($nombre_submit,$valor_submit);
    	echo form::button("limpiar","Resetear","onclick=\"javascript:document.location.href='$vinculo'\"","abm-input");
		echo "</tr></td>";
		echo "</table></div>";
		echo form::cerrar();
						
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito la CLASE a editar","error");
	}
?>
