<? 
include("nucleo/browser/interface/ef.php"); 

//Persisto el valor del tipo de solicitud si ya viene seteado, si no viene seteado busco si hay valor persistido 

if (!$tipo_vista = $this->hilo->obtener_parametro("tipo_vista")) { 
	if (!$tipo_vista = $this->hilo->recuperar_dato_global("tipo_vista")) {
		$sql = "SELECT listado_item_pref FROM apex_et_preferencias WHERE usuario = '" . $this->hilo->obtener_usuario() . "'"; 
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			$this->observar("error","Preferencias del usuario - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
		}
		if(!$rs->EOF){
			$tipo_vista = $rs->fields["listado_item_pref"];
		}else{
			$tipo_vista = "general";		
		}			
	}
}
?>
<script language='javascript'>
	editor='item';
</script>
<table width="100%"  class='cat-item'> 
<tr> 
	<td class='lista-obj-titcol'  colspan='3'> 
		<table width="100%"> 
    		<tr><td class='lista-obj-titcol'>ITEMS (Vista <? echo $tipo_vista ?>)</td> 
     		<td class='lista-obj-titcol' width="40%"> 
<? 	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista" =>"general")) . "' class='basico'>"; 
	echo recurso::imagen_apl('solicitudes.gif',true,null,null,"Vista General"); 
	echo "</a>&nbsp;";
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista" =>"menu")) . "' class='basico'>"; 
	echo recurso::imagen_apl('items/menu.gif',true,null,null,"Vista de Menú"); 
	echo "</a>&nbsp;";	
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista" =>"portafolio")) . "' class='basico'>"; 
	echo recurso::imagen_apl('portafolio.gif',true,null,null,"Vista Portafolio"); 
	echo "</a>&nbsp;";
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',"/admin/items/catalogo_unificado",array()) . "' class='basico'>"; 
	echo recurso::imagen_apl('doc.gif',true,null,null,"Vista Unificada (en construcción)"); 
	echo "</a>&nbsp;";	
	
?>          
        </td></tr> 
        </table> 
     </td> 
</tr> 
</table> 
<?     
switch($tipo_vista) 
{ 
    case "portafolio": 
		$this->hilo->persistir_dato_global("tipo_vista","portafolio");
		include("portafolio.php");
        break;       
    case "general": 
		$this->hilo->persistir_dato_global("tipo_vista","general");
		include("listado.php");
        break; 
    case "menu": 
		$this->hilo->persistir_dato_global("tipo_vista","menu");
		include("listado_menu.php");
        break; 
    default: 
		include("listado.php");
        break; 
} 

?> 
