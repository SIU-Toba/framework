<?
	function refrescar_listado_objeto_apex()
	//Esta funcion refresca el LISTADO de la izquierda cuando se modifico
	//el estado de existencia de un EDITABLE y esta tiene que impactar allado
	{
		echo "<script language'javascript'>";
		echo "if(parent.".apex_frame_lista.".editor == 'objeto') parent.".apex_frame_lista.".location.reload()";
		echo "</script>";
	}
	
	if($editable = $this->zona->obtener_editable_propagado()){
		$this->zona->cargar_editable();
		$this->zona->obtener_html_barra_superior();
		

	//--------------------------------------------------------------------------
	//----------------<  INTERFACE DE PORTAFOLIO de OBJETOS  >--------------------
	//--------------------------------------------------------------------------	

	enter();
	echo "<div align='center'>\n";
	$maximo = 10;//Maximo nivel de anidacion...(Tope maximo estimado, que quede olgado)
	$formulario = "portafolio_items";
	$prefijo_items = "item_";//Prefijo de los checkbox
	$boton_post = "guardar_portafolio_item";
	$boton_post_nombre = "Guargar";
	
	if( ($_SERVER["REQUEST_METHOD"]=="POST") && 
	($_POST[$boton_post]==$boton_post_nombre) )
	//SI hay un POST, y estuvo disparado por este formulario
	{
		//-[1]- Busco los items que tengo que guardar en el portafolio.
		foreach($_POST as $etiqueta => $valor)
		{
			if((substr($etiqueta,0,strlen($prefijo_items)))==$prefijo_items){
				$nodo = trim(substr($etiqueta,strlen($prefijo_items)));
				$claves[] = $nodo;
			}
		}			
		//------------------------------------------------------------------------------

		//-[2]- Realizo la TRANSACCION en la base
		$db["instancia"][apex_db_con]->Execute("BEGIN TRANSACTION");
		//1) Borro los permisos existentes
		$sql = "DELETE FROM apex_et_objeto WHERE usuario = '".
				$this->hilo->obtener_usuario()."' AND objeto_proyecto = '".$this->hilo->obtener_proyecto()."';\n";
		if($db["instancia"][apex_db_con]->Execute($sql) === false)
		{
			echo ei_mensaje("CAMBIAR DESPUES!! - " .$db["instancia"][apex_db_con]->ErrorMsg());
			$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
		}
		else{
			$ok = true;
			if(is_array($claves)){
				foreach($claves as $clave){
					//2) Inserto los permisos ACTUALIZADOS
					$sql = "INSERT INTO apex_et_objeto (usuario, objeto_proyecto, objeto) 
							VALUES ('".$this->hilo->obtener_usuario()."','".$this->hilo->obtener_proyecto()."','$clave');\n";
					if($db["instancia"][apex_db_con]->Execute($sql) === false)
					{
						echo ei_mensaje("Ha ocurrido un error ELIMINANDO los OBJETOS del portafolios - " .$db["instancia"][apex_db_con]->ErrorMsg());
						$rs = $db["instancia"][apex_db_con]->Execute("ROLLBACK TRANSACTION");
						$ok = false;
						break;//Salgo del foreach
					}
				}
			}
			//COMMIT
			if($ok){
				echo ei_mensaje("El portafolios ha sido actualizados correctamente");
				$rs = $db["instancia"][apex_db_con]->Execute("COMMIT TRANSACTION");
			}
		}
		refrescar_listado_objeto_apex();
 	}
	//------------------------------------------------------------------------------
?>
<script languaje='javascript'>
function cascada(item_padre, estado)
{
	var item_actual, regex_item_padre, x, ultimo_elemento;
	formulario = document.<? echo $formulario ?>;
	regex_item_padre = '<? echo $prefijo_items ?>' + item_padre + '/';
	for (x=0 ; x < formulario.elements.length ; x++)	
	{
		if(formulario.elements[x].type=="checkbox")
		{
			item_actual = formulario.elements[x].name;
			if (item_actual.search(regex_item_padre) != -1)
			{
				formulario.elements[x].checked = estado;
			}
		}
	}
}
</script>
<?
	include_once("nucleo/browser/interface/form.php");
			
	//Cuantos items hay?
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT 	COUNT(*) as total
			FROM 	apex_item";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	$total = "NO DEFINIDO";
	if(($rs)&&(!$rs->EOF)){
		$total = $rs->fields["total"];
	}
			
	echo form::abrir($formulario, $this->vinculador->generar_solicitud(null,null,null,true));
?>
<table width="450"  class='cat-item' align='center'>
<? 	
	$clase_tipo = "";
	global $ADODB_FETCH_MODE;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "
			SELECT 	ct.orden			as ct_orden,
					o.proyecto			as proyecto,
					o.objeto	 		as objeto,
					o.clase				as clase,
					o.nombre	 		as nombre,
					ct.descripcion		as clase_tipo,
					g.usuario			as permiso
			FROM 	apex_clase_tipo ct, apex_clase c,apex_objeto o
					LEFT OUTER JOIN apex_et_objeto g
						ON	o.objeto = g.objeto 
						AND o.proyecto = g.objeto_proyecto
						AND g.usuario = '".$this->hilo->obtener_usuario()."'
			WHERE   o.clase = c.clase AND c.clase_tipo= ct.clase_tipo
			AND		o.proyecto = '".$this->hilo->obtener_proyecto()."'
			AND		o.clase <> 'objeto'
			ORDER BY 1,6,5;";
	//dump_sql($sql);
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","ASIGNACION de permisos - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
	if(!$rs->EOF){
?>
        <tr> 
          <td colspan="<? echo (5 + $maximo)?>"  class="cat-item-categ1">
<?
		echo form::submit($boton_post,$boton_post_nombre);
?>
		  </td>
        </tr>

<?
	while(!$rs->EOF)
	{ 
?>
<?	
	if($clase_tipo != $rs->fields["clase_tipo"]){
		$clase_tipo = $rs->fields["clase_tipo"];
	//******************< Corte por tipo >*****************************
?>
        <tr> 
	      <td colspan='4' class='cat-item-dato4' width='2%'>
		  <? 
		  $clase_tipo_mostrar = ($rs->fields["clase_tipo"] != "") ? $rs->fields["clase_tipo"] : "No definido";
		  echo $clase_tipo_mostrar; ?>
		  </td>
		</tr>  
<? }
	//******************< OBJETOS >*************************
?>		<tr>	
          <td  class='cat-arbol-item'  width='2%'>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/objetos/propiedades", array(apex_hilo_qs_zona => $rs->fields["proyecto"] . apex_qs_separador . $rs->fields["objeto"]) ) ?>" class='cat-item'>
			<img src='<? echo recurso::imagen_apl("objetos/objeto.gif") ?>' border='0'></a>
		  </td>
          <td  class='cat-item-botones2'  width='2%'>
<? 		  echo form::checkbox($prefijo_items.$rs->fields["objeto"],$this->hilo->obtener_usuario(),$rs->fields["permiso"]) ?>
		  </td>
          <td  class='cat-item-dato1' width='300' ><? echo $rs->fields["nombre"] ?></td>
          <td  class='cat-item-botones2' width='2%' ><img src='<? echo recurso::imagen_apl("nota.gif") ?>' alt='<? echo "Propietario: ". $rs->fields["usuario"] ?>' border='0'></td>
        </tr>
<?		$rs->movenext();	
	}
?>
        <tr> 
          <td colspan="4" align="center" class="cat-item-categ1">
<?
	echo form::submit($boton_post,$boton_post_nombre);
?>
		  </td>
        </tr>
</table>
</div>
<br>
<br>
<? 
		echo form::cerrar();
	}

		
		
		$this->zona->obtener_html_barra_inferior();
	}else{
		echo ei_mensaje("No se explicito el ELEMENTO a editar","error");
	}
?>