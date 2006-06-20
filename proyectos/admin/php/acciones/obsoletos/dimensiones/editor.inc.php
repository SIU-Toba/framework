<?php
//-----------------------------------------------------------------------
//--- Codigo comun a todas las interfaces de EDICION de RESTRICCIONES ---
//-----------------------------------------------------------------------

	//Controlo los PARAMETROS
	if(!isset($_GET['dim'])){
		echo ei_mensaje("Los PARAMETROS son incorrectos");
		exit();
	}else{
		$parametros = explode(apex_qs_separador,$_GET['dim']);
		$perfil = array($parametros[0], $parametros[1]);
		//ei_arbol($parametros);
	}
	//Preparo un ARRAY para la propagancion de esta ventana
	$param_get = array('dim'=>implode(apex_qs_separador, $parametros));

	
	//-- Cargo la definicion de la RESTRICCION seleccionada
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	d.dimension as 				dim,
					d.proyecto as					dim_proyecto,
					d.fuente_datos_proyecto as	dim_fuente_proyecto,
					d.fuente_datos as 			dim_fuente,
					d.nombre as 					dim_nombre,
					d.descripcion as				dim_descripcion,
					d.tabla_ref as 				dim_tab_ref,
					d.tabla_ref_clave as 		dim_tab_ref_clave,
					d.tabla_ref_desc as	 		dim_tab_ref_desc,
					d.tabla_restric as 			dim_tab_restric,
					d.inicializacion as			inicializacion,
					u.usuario_perfil_datos as 	perfil,
					u.nombre as 					perfil_nombre,
					u.descripcion as				perfil_descripcion,
					t.dimension_tipo as 			tipo,
					t.nombre as 					tipo_nombre,
					t.dimension_tipo_perfil as	tipo_perfil,
					t.item_editor_restric as 	tipo_item_editor
			FROM 	apex_dimension d,
					apex_dimension_perfil_datos p,
					apex_dimension_tipo t,
					apex_usuario_perfil_datos u
			WHERE	d.dimension = p.dimension
			AND		d.proyecto = p.dimension_proyecto
			AND		p.usuario_perfil_datos = u.usuario_perfil_datos
			AND		p.usuario_perfil_datos_proyecto = u.proyecto
			AND		t.dimension_tipo = d.dimension_tipo
			AND		t.proyecto = d.dimension_tipo_proyecto
			AND		p.usuario_perfil_datos_proyecto = '" . $parametros[0] . "'
			AND		p.usuario_perfil_datos = '" . $parametros[1] . "'
			AND		p.dimension_proyecto = '" . $parametros[2] . "'
			AND		p.dimension = '" . $parametros[3] . "'
			ORDER BY 2;";
	//echo $sql;
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	//Controlo que todo este bien
	if(!$rs){
		$this->observar("error","EDITOR DE RESTRICCIONES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg(). " - [sql] $sql",false,true,true);
	}
	if($rs->EOF){
		$this->observar("error","EDITOR DE RESTRICCIONES - No existe la restriccion 
						especificada y por lo tanto es imposible editarla",false,true,true);
	}

	//Cabecera que indica la restriccion que se esta EDITANDO


	$dim_desc = "<b>" . $rs->fields["dim_nombre"] . "</b>";
	if(trim($rs->fields["dim_descripcion"])<>"") $dim_desc .= " - " . $rs->fields["dim_descripcion"];
	$perfil_desc = "<b>" . $rs->fields["perfil_nombre"] . "</b>";
	if(trim($rs->fields["perfil_descripcion"])<>"") $perfil_desc .= " - " . $rs->fields["perfil_descripcion"];
	enter();
	ei_cuadro_vertical(array("dimension"=>$dim_desc,"perfil_de_datos"=>$perfil_desc),
						"Restriccion",null,null,"350");
	enter();
?>