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
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	proyecto, item, nombre, descripcion, imagen
			FROM 	apex_item
			WHERE padre = '/configuracion'
			AND menu = 1
			AND (carpeta <> 1 OR carpeta IS NULL)
			ORDER BY orden";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de CLASES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
?>
         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin','/admin/proyectos/propiedades',array(apex_hilo_qs_zona => editor::get_proyecto_cargado())) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl('proyecto.gif') ?>" alt="Parametros Basicos" border="0">
			</a>
	  	  </td>
          <td width="98%" class='lista-obj-dato1'>&nbsp;Parametros Basicos</td>
        </tr>
<?
	if(!$rs->EOF){
?>
<?
while(!$rs->EOF)
{
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud($rs->fields["proyecto"],$rs->fields["item"]) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl($rs->fields["imagen"]) ?>" alt="<? echo trim($rs->fields["descripcion"])?>" border="0">
			</a>
	  	  </td>
          <td width="98%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["nombre"] ?></td>
        </tr>
<?		$rs->movenext();	
		}
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
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de sesiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);
	if(!$rs->EOF){
?>
<table width="100%" class='lista-obj'>
<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
          <td  class='cat-item-botones2'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/datos/fuente",array( apex_hilo_qs_zona => $rs->fields["fuente_proyecto"] .apex_qs_separador. $rs->fields["fuente"]))?>"  class="cat-item" target="<? echo  apex_frame_centro ?>">
			  <? echo recurso::imagen_apl("fuente.gif",true,null,null,"host/dsn: " . $rs->fields["host"]) ?>
			</a>
		  </td>
          <td  class='cat-item-botones2'>
		<a href='<? echo  $this->vinculador->generar_solicitud('admin',"/admin/datos/fuente_sql",array( apex_hilo_qs_zona => $rs->fields["fuente_proyecto"] .apex_qs_separador. $rs->fields["fuente"])) ?>'  class="cat-item" target="<? echo  apex_frame_centro ?>">
	        <? echo recurso::imagen_apl("sql.gif",true)?> 
			</a>
		  </td>
          <td  class='lista-obj-dato1' width="100%"><? echo $rs->fields["fuente"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["motor"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["proyecto"] ?></td>
          <td  class='lista-obj-dato1'><? echo $rs->fields["link_instancia"] ?></td>
        </tr>
<?
		$rs->movenext();	
	}
?>
</table>
<?
}
?>
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
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de CLASES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
<?
while(!$rs->EOF)
{
	$tipo = $rs->fields["clase_tipo"];
?>
        <tr> 
          <td width='1%'><? echo gif_nulo(6,15); ?></td>
          <td width='99%' colspan='8' class='cat-item-dato4'><?  echo $rs->fields["tipo_descripcion"] ?></td>
        </tr>
<?
	while( (!$rs->EOF) && ($rs->fields["clase_tipo"] == $tipo ) )
	{
?>
         <tr> 
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("clases.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
			echo recurso::imagen_apl("autodoc.gif",true,null,null,"Ver DOCUMENTACION automatica");
			echo "</a>";
		}
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/clase_doc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("referencia.gif",true,null,null,"Ver DOCUMENTACION extra");
		echo "</a>";
?>
	  	  </td>
<?
		echo "<td width='90%' class='lista-obj-dato1'>" . trim($rs->fields["clase"]) . "</td>";
?>
          <td width="2%" class='lista-obj-dato3'><? echo recurso::imagen_apl($rs->fields["icono"],true) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["objetos"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["objetos_item"]) ?></td>
        </tr>
<?
		$rs->movenext();	
		}
	}
	}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  NUCLEO  >--------------------------------------
//--------------------------------------------------------------------------------------
/*
	//Cuantos elementos del nucleo estan declarados
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_nucleo";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}
// [<? echo $total ?>]
*/
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >Info NUCLEO</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/apex/nucleo_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("nucleo.gif",true) ?>
	</a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	//No agrego lo especifico por lo mismo que se explica de los objetos fantasma
	$sql = "SELECT 	n.*, t.descripcion_corta as tipo_descripcion
			FROM 	apex_nucleo n,
					apex_nucleo_tipo t
			WHERE	n.nucleo_tipo = t.nucleo_tipo
			AND		proyecto = '".editor::get_proyecto_cargado()."'
			ORDER BY nucleo_tipo, orden";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de NUCLEO - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='3'>e</td>
          <td width="80%" class='lista-obj-titcol' >Archivo</td>
        </tr>
<?
while(!$rs->EOF)
{
	$tipo = $rs->fields["nucleo_tipo"];
?>
        <tr> 
          <td width='1%'><? echo gif_nulo(6,15); ?></td>
          <td width='99%' colspan='8' class='cat-item-dato4'><?  echo $rs->fields["tipo_descripcion"] ?></td>
        </tr>
<?
	while( (!$rs->EOF) && ($rs->fields["nucleo_tipo"] == $tipo ) )
	{
?>
         <tr> 
         <tr> 
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/nucleo_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("nucleo.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/nucleo_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/nucleo_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
			echo recurso::imagen_apl("autodoc.gif",true,null,null,"Ver DOCUMENTACION automatica");
			echo "</a>";
		}
?>
	  	  </td>

          <td width="90%" class='lista-obj-dato1'><? echo trim($rs->fields["archivo"]) ?></td>
        </tr>
<?		$rs->movenext();	
		}
	}
}
?>
</table>
<?
//--------------------------------------------------------------------------------------
//------------------------------------<  PATRONES  >------------------------------------
//--------------------------------------------------------------------------------------
/*
	//Cuantos patrones hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_patron";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}
//[<? echo $total ?>]
*/

?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >COMPORTAMIENTOS</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/apex/patron_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("patrones.gif",true) ?>
	</a>
	</td>
</tr>
</table>
<table width="100%" class='lista-obj'>
<? 	
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	//No agrego lo especifico por lo mismo que se explica de los objetos fantasma
	$sql = "SELECT 	*,
					(SELECT count(*) FROM apex_item i WHERE i.actividad_patron = p.patron AND i.actividad_patron_proyecto = p.proyecto) as items
			FROM 	apex_patron p
			WHERE patron <> 'especifico'
			AND		proyecto = '".editor::get_proyecto_cargado()."'
			ORDER BY descripcion_corta";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de PATRONES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol'  colspan='3'>e</td>
          <td width="80%" class='lista-obj-titcol' >Descripcion</td>
          <td width="10%" class='lista-obj-titcol' >I</td>
        </tr>
<?
while(!$rs->EOF)
{
?>

         <tr> 
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/patron_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("patrones.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/patron_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud('admin',"/admin/apex/patron_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
			echo recurso::imagen_apl("autodoc.gif",true,null,null,"Ver DOCUMENTACION automatica");
			echo "</a>";
		}
?>


          <td width="90%" class='lista-obj-dato1'><? echo trim($rs->fields["descripcion_corta"]) ?></td>
          <td width="2%" class='lista-obj-dato3'><? echo trim($rs->fields["items"]) ?></td>
        </tr>
<?		$rs->movenext();	
		}
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
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de dimensiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='2'>e</td>
          <td width="80%" class='lista-obj-titcol' >NOMBRE</td>
          <td width="2%" class='lista-obj-titcol' >F</td>
          <td width="2%" class='lista-obj-titcol' >Pd</td>
          <td width="10%" class='lista-obj-titcol' >FUENTE</td>
        </tr>
<?
	while(!$rs->EOF)
	{ 
?>
        <tr> 
<?	if($rs->fields["perfil"]!="no"){ ?>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-dato5'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato4'>&nbsp;<? echo "[<b>" . $rs->fields["dim_id"] ."</b>] - ". trim($rs->fields["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($rs->fields["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato4'>&nbsp;<? echo trim($rs->fields["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato5'><? echo trim($rs->fields["fue_id"]) ?></td>
<? }else {?>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/propiedades",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("dimension.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="2%" class='lista-obj-botones'>
		 	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/dimensiones/probar",array( apex_hilo_qs_zona => $rs->fields['dim_proy'] .apex_qs_separador. $rs->fields['dim_id'] )) ?>" target="<? echo  apex_frame_centro ?>">
				<img src="<? echo recurso::imagen_apl("items/instanciar.gif") ?>" alt="<? echo trim($rs->fields["dim_id"])?>" border="0">
			</a>
  	  </td>
          <td width="90%" class='lista-obj-dato1'><? echo "[<b>" . $rs->fields["dim_id"] ."</b>] - ". trim($rs->fields["dim_des"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($rs->fields["filtros"]) ?></td>
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo trim($rs->fields["perfiles"]) ?></td>
          <td width="2%" class='lista-obj-dato2'>&nbsp;<? echo trim($rs->fields["fue_id"]) ?></td>
<? } ?>
        </tr>
<?		$rs->movenext();	
		}
	}
?>
</table>
