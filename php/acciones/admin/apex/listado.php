<?
include("nucleo/browser/interface/ef.php"); 
//--------------------------------------------------------------------------------------
//------------------------------------<  CLASES  >------------------------------------
//--------------------------------------------------------------------------------------
	
/*
	//Cuantas clases definidas hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_clase
			WHERE 	proyecto = '".$this->hilo->obtener_proyecto()."'";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}
	
//---------> [<? echo $total ?>]
*/
?>
<table width="100%"  class='cat-item'>
<tr> 
	 <td width="2%"  class='lista-obj-titulo'>
<?
	$url =  $this->vinculador->generar_solicitud("toba","/admin/apex/esquema_clases");

	echo form::abrir("tipo_objeto",$url, " target='frame_centro'");
	echo form::submit('esquema','Esquema');
	echo form::cerrar();
	
?>
	</td>
    <td width="98%" class="lista-obj-titulo" >CLASES</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/apex/clase_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
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
			AND		proyecto = '".$this->hilo->obtener_proyecto()."'
			ORDER BY t.orden, clase";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de CLASES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol' colspan='4'>e</td>
          <td width="80%" class='lista-obj-titcol' >clase</td>
		  <td width="10%" class='lista-obj-titcol' >G</td>
		  <td width="10%" class='lista-obj-titcol' >O</td>
          <td width="10%" class='lista-obj-titcol' >OI</td>
        </tr>
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
		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/clase_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("clases.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/clase_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/clase_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
			echo recurso::imagen_apl("autodoc.gif",true,null,null,"Ver DOCUMENTACION automatica");
			echo "</a>";
		}
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/clase_doc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["clase"] ),true). "' target='". apex_frame_centro ."' >";
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
	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/apex/nucleo_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
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
			AND		proyecto = '".$this->hilo->obtener_proyecto()."'
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
		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/nucleo_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("nucleo.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/nucleo_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/nucleo_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["nucleo"] ),true). "' target='". apex_frame_centro ."' >";
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
    <td width="98%" class="lista-obj-titulo" >PATRONES</td>
	 <td width="2%"  class='lista-obj-titulo'>
	<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/apex/patron_propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
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
			AND		proyecto = '".$this->hilo->obtener_proyecto()."'
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
		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/patron_propiedades",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("patrones.gif",true,null,null,"Editar PROPIEDADES");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?		echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/patron_php",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
		echo recurso::imagen_apl("php.gif",true,null,null,"Ver el CODIGO FUENTE");
		echo "</a>";
?>
	  	  </td>
          <td width="2%" class='lista-obj-botones'>
<?
		if($rs->fields["autodoc"]==1){
			echo "<a href='". $this->vinculador->generar_solicitud("toba","/admin/apex/patron_autodoc",array(apex_hilo_qs_zona=> $rs->fields["proyecto"] .apex_qs_separador. $rs->fields["patron"] ),true). "' target='". apex_frame_centro ."' >";
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
//------------------------------------<  ELEMENTOS  >------------------------------------
//--------------------------------------------------------------------------------------
?>
<table width="100%"  class='cat-item'>
<tr> 
    <td width="98%" class="lista-obj-titulo" >Elementos Basicos</td>
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
			WHERE padre = '/admin/apex/elementos'
			AND menu = 1
			ORDER BY nombre";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de CLASES - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td width="2%" class='lista-obj-titcol'></td>
          <td width="80%" class='lista-obj-titcol' ></td>
        </tr>
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
          <td width="2%" class='lista-obj-dato1'>&nbsp;<? echo $rs->fields["nombre"] ?></td>
        </tr>
<?		$rs->movenext();	
		}
	}
?>
</table>
