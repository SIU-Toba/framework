<?
	include_once("editor.inc.php");

	if(isset($rs->fields["inicializacion"])){
		//echo $rs->fields["inicializacion"];
		$param_x = parsear_propiedades($rs->fields["inicializacion"]);
		//ei_arbol($param_x);

		//Interface de edicion de la restriccion
		$tab_param["clave_entrada"]= $perfil;
		$tab_param["tabla_asoc"]= $param_x["tab_restric"];
		$tab_param["tabla_asoc_fk_p"]= array("usuario_perfil_datos_proyecto","usuario_perfil_datos");
		$tab_param["tabla_asoc_fk_r"]= explode(",",$param_x["tab_ref_clave"]);
		$tab_param["tabla_ref"]= $param_x["tab_ref"];
		$tab_param["tabla_ref_clave"]= explode(",",$param_x["tab_ref_clave"]);
	   $tab_param["tabla_ref_desc"]=  $param_x["tab_ref_des"];
		$tab_param["tabla_ref_where"] = $param_x["tab_ref_where"];;
		$tab_param["fuente"]= $rs->fields["dim_fuente"];
		$tab_param["form_prefijo"]="restric_";
		$tab_param["form_submit"]="restric_procesar";
		$tab_param["titulo_referencia"]= "ELEMENTOS HABILITADOS";

		include_once("nucleo/browser/interface/tabla_asociacion.php");
		$tabla =& new tabla_asociacion($tab_param);
		//$tabla->info();
		$tabla->procesar();
		$tabla->generar_html($param_get);
	}else{
		echo ei_mensaje("La dimension no esta inicializada");
	}
?>