<?php

	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();	

		include_once("nucleo/lib/ef.php");
		$form = "editor_sql";
		$submit = "procesar";
		$submit_nombre = "Ejecutar";


		$input_sql =& new ef_editable_multilinea($form,$form,"sql","SQL","","sql",null,array("filas"=>15,"columnas"=>60));
		$respuesta = false;
		
		if( (acceso_post()) && ($_POST[$submit] == $submit_nombre) )
		{
			$respuesta = true;
			//Obtengo el SQL
			$input_sql->cargar_estado();
			$sql = stripslashes($input_sql->obtener_estado());
			//dump_SQL($sql);

			//Abro la conexion
			abrir_base( 	$this->zona->editable_info["fuente_datos"], array(
							apex_db_motor => $this->zona->editable_info["fuente_datos_motor"],
							apex_db_profile => $this->zona->editable_info["host"],
							apex_db_usuario => $this->zona->editable_info["usuario"],
							apex_db_clave => $this->zona->editable_info["clave"],
							apex_db_base => $this->zona->editable_info["base"],
							apex_db_link => $this->zona->editable_info["link_instancia"])
						);
	
			//Genero el CUADRO que va a mostrar el resultado
			$cuadro_param["titulo"]="";
			$cuadro_param["descripcion"]="";
			$cuadro_param["col_titulos"]=null;
			$cuadro_param["col_formato"]=null;
			$cuadro_param["col_ver"]=null;
			$cuadro_param["ancho"]="";
			$cuadro_param["mensaje_error"]="No se recuperaron registros";
			$cuadro_param["ordenar"]=false;
			$fuente_datos = $this->zona->editable_info["fuente_datos"];

			include_once("nucleo/obsoleto/efs_obsoletos/cuadro.php");
			$cuadro =& new cuadro_db($cuadro_param,$fuente_datos,$sql);
		}
	
		ei_separador("Ejecutar SQL contra el motor");
		echo "<div align='center'>";
		echo form::abrir($form, $this->vinculador->generar_solicitud(null,null,null,true));
		echo $input_sql->obtener_input();
		echo "<br>";
		echo form::submit($submit, $submit_nombre);
		echo form::cerrar();
		echo "</div>";

		if($respuesta){
			echo "<br><div align='center'>";
			$cuadro->generar_html();
			echo "<br></div>";
		}
	}else{
		echo ei_mensaje("No se especifico una FUENTE de DATOS");
	}
?>
