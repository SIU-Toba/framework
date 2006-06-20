<?
include("nucleo/browser/interface/ef.php");
        
$parametros["sql"] = "SELECT proyecto, descripcion_corta FROM apex_proyecto";
$combo_proyecto =& new ef_combo_db("proyecto","",apex_sesion_post_proyecto,apex_sesion_post_proyecto,"Seleccione el proyecto.","","",$parametros);
if ($temp = $this->hilo->recuperar_dato_global("log_proyecto")) {
	$combo_proyecto->cargar_estado($temp);
}

// Adjunto al where el codigo de sesion browser
if($this->hilo->obtener_proyecto() != "toba"){
	$where[] = "s.item_proyecto = '".$this->hilo->obtener_proyecto()."'";
	$proyecto = $this->hilo->obtener_proyecto();
}else{      
	if(acceso_post()){
		$combo_proyecto->cargar_estado();
		$proyecto = $combo_proyecto->obtener_estado();
		if ($proyecto != "NULL"){
			$this->hilo->persistir_dato_global("log_proyecto",$proyecto);
		}else{
			$proyecto = $this->hilo->obtener_proyecto();
		}		
	}else{
		if (!$proyecto=$this->hilo->recuperar_dato_global("log_proyecto")) {
			$proyecto = $this->hilo->obtener_proyecto();
		}
	}
} 

//Persisto el valor del tipo de solicitud si ya viene seteado, si no viene seteado busco si hay valor persistido
if ($tipo_solicitud = $this->hilo->obtener_parametro("opcion")) {
	$this->hilo->persistir_dato_global("tipo_solicitud",$tipo_solicitud);    
} else {
	$tipo_solicitud = $this->hilo->recuperar_dato_global("tipo_solicitud");
}
//Evita un NOTICE
$where=array();
		
switch($tipo_solicitud)
{
	case "wddx":
		$titulo = "Solicitudes WDDX";
        $cuadro = $this->cargar_objeto("objeto_cuadro", 2);
		$where[] = "s.item_proyecto = '$proyecto'";
		break;      
	case "consola":
		$titulo = "Solicitudes de consola";
		$cuadro = $this->cargar_objeto("objeto_cuadro", 1);
		$where[] = "s.item_proyecto = '$proyecto'";
		break;
	case "log_sistema":
		$titulo = "Log Sistema";
		$cuadro = $this->cargar_objeto("objeto_cuadro", 3);
		break;
	case "log_error_login":
		$titulo = "Log Error Login";
		$cuadro = $this->cargar_objeto("objeto_cuadro", 4);
		break;
	case "log_ip_rechazada":
		$titulo = "Log IP Rechazada";
		$cuadro = $this->cargar_objeto("objeto_cuadro", 5);
		break;
	default:
		$titulo = "SESIONES";
		$cuadro = $this->cargar_objeto("objeto_cuadro", 0);
		$where[] = "se.proyecto = '$proyecto'";
		break;
}

?>

<table width="100%"  class='cat-item'>

<tr> 
	 <td class='lista-obj-titcol'  colspan='3'>
		 <table class='cat-item'  width="100%">
		 <tr><td>
<?
    //Muestra los iconos con los distintos tipos de solicitud
    
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$sql = "SELECT		solicitud_tipo,
                        descripcion_corta,
                        icono
			FROM		apex_solicitud_tipo
            WHERE       solicitud_tipo NOT IN ('fantasma','consumidor_html')";
	$rs =& $db["instancia"][apex_db_con]->Execute($sql);
	if(!$rs) 
		$this->observar("error","Lista de sesiones - [error] " . $db["instancia"][apex_db_con]->ErrorMsg()." - [sql]". $sql ,false,true,true);

   	while(!$rs->EOF)
    {
     	echo "<a href='" . $this->vinculador->generar_solicitud("toba",null,array("opcion" => $rs->fields["solicitud_tipo"])) . "' class='basico'>";
        echo recurso::imagen_apl($rs->fields["icono"],true,null,null,$rs->fields["descripcion_corta"]);
        echo "</a>&nbsp;";
        $rs->Movenext();
    }
?>	 	</td>
	 <td  class='lista-obj-titcol' width="40%">
<?
     	echo "<a href='" . $this->vinculador->generar_solicitud("toba",null,array("opcion" =>"log_sistema")) . "' class='basico'>";
        echo recurso::imagen_apl('solicitudes.gif',true,null,null,"Log sistema");
        echo "</a>&nbsp;";
     	echo "<a href='" . $this->vinculador->generar_solicitud("toba",null,array("opcion" =>"log_error_login")) . "' class='basico'>";
        echo recurso::imagen_apl('solicitudes.gif',true,null,null,"Log error login");
        echo "</a>&nbsp;";
     	echo "<a href='" . $this->vinculador->generar_solicitud("toba",null,array("opcion" =>"log_ip_rechazada")) . "' class='basico'>";
        echo recurso::imagen_apl('solicitudes.gif',true,null,null,"Log IP rechazada");
        echo "</a>";
?>	 	
		</td></tr>
		</table>
	 </td>
</tr>

<tr> 
	 <td  class="lista-obj-titulo" width="70%"><? echo $titulo; ?></td>
	 <td  class="lista-obj-titulo" width="30%">
	 <?
   		echo form::abrir("proyecto",$this->vinculador->generar_solicitud());
		$combo_proyecto->cargar_estado();
     	echo $combo_proyecto->obtener_input();
     ?>
	 </td>
	 <td class="lista-obj-titulo" width="3%">
<?		echo form::image('filtrar',recurso::imagen_apl('cambiar_proyecto.gif',false));
		echo form::cerrar();
?>
	 </td>
</tr>
</table>
<?
if($cuadro > -1)
{
	 $this->objetos[$cuadro]->cargar_datos($where);
     $this->objetos[$cuadro]->obtener_html(false);
}else{
     echo ei_mensaje("No se pudo cargar el cuadro asociado");
}
?> 		
