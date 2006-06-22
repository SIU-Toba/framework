<?
//--------------------------------------------------------------------------------------
//------------------------------------<  ELEMENTOS  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >Configuracion del proyecto</td>
</tr>
</table>
<script language='javascript'>
	editor='apex';
</script>
<table width="100%" class='lista-obj'>
<? 	
	$sql = "SELECT 	proyecto, item, nombre, descripcion, imagen
			FROM 	apex_item
			WHERE padre = '/configuracion'
			AND menu = 1
			AND (carpeta <> 1 OR carpeta IS NULL)
			ORDER BY orden";
	$rs = toba::get_db('instancia')->consultar($sql);
?>
<?
	foreach( $rs as $registro ) {
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud($registro["proyecto"],$registro["item"]) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl($registro["imagen"]) ?>" alt="<? echo trim($registro["descripcion"])?>" border="0">
			</a>
	  	  </td>
          <td width="98%" class='lista-obj-dato1'>&nbsp;<? echo $registro["nombre"] ?></td>
        </tr>
<?
	}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  fuentes  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="98%" class="lista-obj-titulo" >FUENTES de DATOS</td>
 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/datos/fuente")?>" class="list-obj" target="<? echo  apex_frame_centro ?>">
	<? echo recurso::imagen_apl("fuente_nueva.gif",true) ?>
	</a>
</td>
</tr>
</table>
<script language='javascript'>
	editor='datos';
</script>
<? 	
	$sql = "	
			SELECT 	m.fuente_datos_motor as 		motor,
					f.proyecto as					fuente_proyecto,
					f.fuente_datos as				fuente,
					f.base as						base,
					f.proyecto as					proyecto,
					f.host as						host,
					f.link_instancia as				link_instancia
			FROM apex_fuente_datos_motor m,
			apex_fuente_datos f
			WHERE m.fuente_datos_motor = f.fuente_datos_motor
            AND     f.proyecto = '".editor::get_proyecto_cargado()."'			
			ORDER BY f.fuente_datos;";
	$rs = toba::get_db('instancia')->consultar($sql);
?>
<table width="100%" class='lista-obj'>
<?
	
	foreach( $rs as $registro ) {
?>
        <tr> 
          <td  class='cat-item-botones2'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/datos/fuente",array( apex_hilo_qs_zona => $registro["fuente_proyecto"] .apex_qs_separador. $registro["fuente"]))?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("fuente.gif",true,null,null,"host/dsn: " . $registro["host"]) ?>
			</a>
		  </td>
          <td  class='cat-item-botones2'>
		<a href='<? echo  $this->vinculador->generar_solicitud('admin',"/admin/datos/fuente_sql",array( apex_hilo_qs_zona => $registro["fuente_proyecto"] .apex_qs_separador. $registro["fuente"])) ?>'  class="cat-item" target="<? echo  apex_frame_centro ?>">
	        <? echo recurso::imagen_apl("sql.gif",true)?> 
			</a>
		  </td>
          <td  class='lista-obj-dato1' width="100%"><? echo $registro["fuente"] ?></td>
          <td  class='lista-obj-dato1'><? echo $registro["motor"] ?></td>
          <td  class='lista-obj-dato1'><? echo $registro["proyecto"] ?></td>
          <td  class='lista-obj-dato1'><? echo $registro["link_instancia"] ?></td>
        </tr>
<?
	}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  CLASES  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >CLASES</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("clases.gif",true) ?>
	</a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	//No agrego los objetos "fantasma" porque no tiene mucho sentido manejarlos desde esta zona
	//(SI bien puede servir para editar algunas cosas, empeora la coherencia general)
	//Es una forma de simplificar algo interno que no es necesario mostrar...
	$sql = "SELECT 	c.*, t.descripcion_corta as tipo_descripcion,
					(SELECT count(*) FROM apex_objeto WHERE clase = c.clase) as objetos,
					(SELECT count(*) FROM apex_objeto o, apex_item_objeto io WHERE o.objeto = io.objeto AND o.clase = c.clase) as objetos_item
			FROM 	apex_clase c,
					apex_clase_tipo t
			WHERE	c.clase_tipo = t.clase_tipo
			AND		proyecto = '".editor::get_proyecto_cargado()."'
			ORDER BY t.orden, clase";
	$rs = toba::get_db('instancia')->consultar($sql);
?>
<?
	$tipo_previo =null;
	foreach( $rs as $registro ) {
	if( $tipo_previo != $registro["clase_tipo"] ) {
?>
        <tr> 
          <td width='1%'><? echo gif_nulo(6,15); ?></td>
          <td width='99%' colspan='8' class='cat-item-dato4'><?  echo $registro["tipo_descripcion"] ?></td>
        </tr>
<?
	}
?>
         <tr> 
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_propiedades",array(apex_hilo_qs_zona=> $registro["proyecto"] .apex_qs_separador. $registro["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("clases.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_php",array(apex_hilo_qs_zona=> $registro["proyecto"] .apex_qs_separador. $registro["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($registro["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_autodoc",array(apex_hilo_qs_zona=> $registro["proyecto"] .apex_qs_separador. $registro["clase"] ),true). "' target='". apex_frame_centro ."' >";
			echo recurso::imagen_apl("autodoc.gif",true,null,null,"Ver DOCUMENTACION automatica");
			echo "</a>";
		}
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_doc",array(apex_hilo_qs_zona=> $registro["proyecto"] .apex_qs_separador. $registro["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("referencia.gif",true,null,null,"Ver DOCUMENTACION extra");
		echo "</a>";
?>
	  	  </td>
<?
		echo "<td width='90%' class='lista-obj-dato1'>" . trim($registro["clase"]) . "</td>";
?>
          <td width="2%" class='lista-obj-dato3'><? echo recurso::imagen_apl($registro["icono"],true) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($registro["objetos"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($registro["objetos_item"]) ?></td>
        </tr>
<?
		$tipo_previo = $registro["clase_tipo"];
	}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  DIMENSIONES  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="95%" class="lista-obj-titulo" >DIMENSIONES</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/apex/elementos/dimension_tipo") ?>" 
		target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("dimension_tipo.gif",true) ?></a>
	</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades") ?>" 
		target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("dimension.gif",true) ?></a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	$sql = "SELECT 	d.proyecto as				dim_proy,
					d.dimension as 				dim_id,
					d.nombre as 				dim_des,
					d.fuente_datos as 			fue_id,
					t.dimension_tipo_perfil	as	perfil,
					(SELECT COUNT(*) FROM apex_objeto_filtro f WHERE f.dimension = d.dimension AND f.dimension_proyecto = d.proyecto) as filtros,
					(SELECT COUNT(*) FROM apex_dimension_perfil_datos f WHERE f.dimension = d.dimension AND f.dimension_proyecto = d.proyecto) as perfiles
			FROM 	apex_dimension d,
					apex_dimension_tipo t
			WHERE	d.dimension_tipo = t.dimension_tipo
            AND     d.proyecto = '".editor::get_proyecto_cargado()."'
			ORDER BY 2";
	$rs = toba::get_db('instancia')->consultar($sql);
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='2'>e</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
          <td width="2%" class='lista-obj-titcol' >F</td>
          <td width="2%" class='lista-obj-titcol' >Pd</td>
          <td width="10%" class='lista-obj-titcol' >FUENTE</td>
        </tr>
<?
	foreach( $rs as $registro ) {
?>
        <tr> 
<?	if($registro["perfil"]!="no"){ ?>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $registro['dim_proy'] .apex_qs_separador. $registro['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($registro["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $registro['dim_proy'] .apex_qs_separador. $registro['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($registro["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato4'>&nbsp;<? echo "[<b>" . $registro["dim_id"] ."</b>] - ". trim($registro["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($registro["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($registro["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato5'><? echo trim($registro["fue_id"]) ?></td>
<? }else {?>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $registro['dim_proy'] .apex_qs_separador. $registro['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($registro["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $registro['dim_proy'] .apex_qs_separador. $registro['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($registro["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato1'><? echo "[<b>" . $registro["dim_id"] ."</b>] - ". trim($registro["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($registro["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($registro["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato2'>&nbsp;<? echo trim($registro["fue_id"]) ?></td>
<? } ?>
        </tr>
<?		}
?>
</table>
