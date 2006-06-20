<? 
include("nucleo/browser/interface/ef.php"); 

//Persisto el valor del tipo de solicitud si ya viene seteado, si no viene seteado busco si hay valor persistido 

if (!$tipo_vista = $this->hilo->obtener_parametro("tipo_vista_obj")) {
	if (!$tipo_vista = $this->hilo->recuperar_dato_global("tipo_vista_obj")) {
		$sql = "SELECT listado_obj_pref FROM apex_et_preferencias WHERE usuario = '" . $this->hilo->obtener_usuario() . "'"; 
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			$this->observar("error","Preferencias del usuario - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql] $sql",false,true,true);
		}
		if(!$rs->EOF){
			$tipo_vista = $rs->fields["listado_obj_pref"];
		}else{
			$tipo_vista = "capas";
		}
	}
}
?>
<table width="100%"  class='cat-item'> 
<tr> 
	<td class='lista-obj-titcol'  colspan='3'> 
		<table width="100%"> 
    		<tr><td  class='lista-obj-titcol'>OBJETOS (Vista <? echo $tipo_vista ?>)</td> 
<td class='lista-obj-titcol'> 
<? 	
	//OBJETOS Estaticos
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista_obj" =>"estatico")) . "' class='basico'>"; 
	echo recurso::imagen_apl('objetos/met_estatico.gif',true,null,null,"Objetos estaticos"); 
	echo "</a>";
	echo "</td><td class='lista-obj-titcol'>";
	//OBJETOS para trabajar con CAPAS
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista_obj" =>"capas")) . "' class='basico'>"; 
	echo recurso::imagen_apl('objetos/met_capas.gif',true,null,null,"Desarrollo en CAPAS"); 
	echo "</a>";
	echo "</td><td class='lista-obj-titcol'>";
	//OBJETOS para trabajar con el MODELO
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista_obj" =>"modelo")) . "' class='basico'>"; 
	echo recurso::imagen_apl('objetos/met_modelo.gif',true,null,null,"Desarrollo sobre el MODELO"); 
	echo "</a>";
	echo "</td><td  class='lista-obj-titcol'>";
	//OBJETOS en el PORTAFOLIO
	echo "<a href='" . $this->vinculador->generar_solicitud('admin',null,array("tipo_vista_obj" =>"portafolio")) . "' class='basico'>"; 
	echo recurso::imagen_apl('portafolio.gif',true,null,null,"Vista Portafolio"); 
	echo "</a>";
?>          
</td>
 	<td width="2%" >
	<a href="<? echo $this->vinculador->generar_solicitud('admin',"/admin/objetos/propiedades") ?>" target="<? echo  apex_frame_centro ?>" class="list-obj">
	<? echo recurso::imagen_apl("objetos/objeto_nuevo.gif",true,null,null,"Crear un OBJETO") ?>
		</a>
	</td>        
        
        </tr> 
        </table> 
     </td> 
</tr> 
</table> 
<?     
switch($tipo_vista) 
{ 
    case "portafolio": 
		$this->hilo->persistir_dato_global("tipo_vista_obj","portafolio");
		include("portafolio.php");
        break;       
    case "modelo": 
		$this->hilo->persistir_dato_global("tipo_vista_obj","modelo");
		include("listado_modelo.php");
        break; 
    case "capas": 
		$this->hilo->persistir_dato_global("tipo_vista_obj","capas");
		include("listado_capas.php");
        break; 
    case "estatico": 
		$this->hilo->persistir_dato_global("tipo_vista_obj","estatico");
		include("listado_estatico.php");
        break; 
    default: 
		include("listado_modelo.php");
        break; 
} 

?> 
