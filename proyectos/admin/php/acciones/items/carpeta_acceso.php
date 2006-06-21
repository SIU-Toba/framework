<?
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();//Cargo el editable de la zona
		$this->zona->obtener_html_barra_superior();
//		echo ei_mensaje("ACCESO");
		//Inicializacion de la tabla de ASOCIACIONES
			$tab_param["clave_entrada"]=		$editable;		
			$tab_param["tabla_asoc"]=			"apex_usuario_grupo_acc_item";
			$tab_param["tabla_asoc_fk_p"]=		array("proyecto","item");
			$tab_param["tabla_asoc_fk_r"]=		array("proyecto","usuario_grupo_acc");
			$tab_param["tabla_ref"]=			"apex_usuario_grupo_acc";
			$tab_param["tabla_ref_clave"]=		array("proyecto","usuario_grupo_acc");
			$tab_param["tabla_ref_desc"]=		"nombre";
			$tab_param["tabla_ref_where"]=		"r.proyecto = '".editor::get_proyecto_cargado()."'";
			$tab_param["fuente"]=				"instancia";
			$tab_param["form_prefijo"]=			"grupo_acc_";
			$tab_param["form_submit"]=			"grupo_acc_procesar";
			$tab_param["titulo_referencia"]=	"Grupos de Acceso";
			include_once("nucleo/lib/efs_obsoletos/tabla_asociacion.php");
			$debug = false;
			  $tabla =& new tabla_asociacion($tab_param, $debug);
			$tabla->procesar();
			//$tabla->info();
			//echo $tabla->generar_sql_interface(true);
			echo "<br>\n";
			  $tabla->generar_html();
			$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se especifico que EDITABLE utilizar");
	}
?>