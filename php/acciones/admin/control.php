<?
	$js_cambiar_color_1 = " onmouseover=\"this.className='listado-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-tabn';\"";
	$js_cambiar_color_2 = " onmouseover=\"this.className='listado-barra-superior-tabn-m';\" ".
                        "  onmouseout=\"this.className='listado-barra-superior-tabn';\"";
	ei_html_cabecera($this->info["item_nombre"], recurso::css(),"control");
?>
<script language='javascript'>
function salir(){
if(confirm('Desea terminar la sesion?')) 
	top.location.href='<? echo $this->hilo->finalizar() ?>';
}

var frame_admin = top.document.getElementById('frameset_admin');
var ancho_frame = frame_admin.cols;
var expandido = true;
function mostrar_ocultar_frame() {
	var imagen = document.getElementById('imagen_manejo_frame');
	if (expandido) {
		imagen.src = '<?echo recurso::imagen_apl("expandir.gif",false);?>';
		frame_admin.cols = '8,*';
		expandido = false;
	} else {
		imagen.src = '<?echo recurso::imagen_apl("contraer.gif",false);?>';
		frame_admin.cols = ancho_frame;
		expandido = true;
	}
}
</script>
<table width='100%'  class='tabla-0' >
<tr><td class='listado-barra-superior'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td  class='listado-barra-superior'>

	<table class='tabla-0' width='100%'>
	<tr> 
		<td class='listado-vacia' width='1%' nowrap valign='middle'>
		<a href="javascript: mostrar_ocultar_frame();"><img src="<? echo recurso::imagen_apl("contraer.gif",false); ?>" id='imagen_manejo_frame' border='0' style='margin: 0px 0px 0px 0px;'></a>
		 <a  target="<? echo  apex_frame_lista  ?>" href="<? echo $this->vinculador->generar_solicitud("toba","/red/organizador") ?>">
		 <? echo recurso::imagen_apl("logo_barra_apex.gif",true,null,null,"Red SIU-Toba")?></a></td>

		<td width='100%'><? echo gif_nulo(3,1) ?></td>

	 <td class='listado-barra-superior-tabi'><? echo recurso::imagen_apl("proyecto.gif",true) ?></td>
<?
	if(apex_pa_proyecto=="multi")
	{
		include_once("nucleo/browser/interface/ef.php");
		//Si estoy en modo MULTIPROYECTO muestro un combo para cambiar a otro proyecto,
		//sino muestro el nombre del proyecto ACTUAL
		echo form::abrir("multiproyecto",$this->hilo->cambiar_proyecto(),"target = '_top'");
		echo "<td class='listado-barra-superior-tabi2'>";
		$parametros["sql"] = "SELECT 	p.proyecto, 
                						p.descripcion_corta
                				FROM 	apex_proyecto p,
                						apex_usuario_proyecto up
                				WHERE 	p.proyecto = up.proyecto
								AND  	listar_multiproyecto = 1 
								AND		up.usuario = '".$this->hilo->obtener_usuario()."'
								ORDER BY orden;";
		$proy =& new ef_combo_db(null,"",apex_sesion_post_proyecto,apex_sesion_post_proyecto,
                                "Seleccione el proyecto en el que desea ingresar.","","",$parametros);
		$proy->cargar_estado($this->hilo->obtener_proyecto());//Que el elemento seteado
		echo $proy->obtener_input(" onchange='multiproyecto.submit();'");
		echo "</td>";
		echo "<td class='listado-barra-superior-tabi'>";
        echo form::image('cambiar',recurso::imagen_apl('cambiar_proyecto.gif',false));
        echo "</td>";
		echo form::cerrar();
	}else{
		$proyecto = ereg_replace("-","_",apex_pa_proyecto);
		echo "<td class='listado-barra-superior-tabn'>";
		echo $proyecto;
		echo "</td>";
	}
?>
		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-barra-superior-tabn' <? echo $js_cambiar_color_2 ?>>
        <a href="<? echo $this->vinculador->generar_solicitud("toba","/actividad/organizador") ?>" class="list-obj"  target="<? echo  apex_frame_lista ?>">
        <? echo recurso::imagen_apl("doc.gif",true,null,null,"Ver LOGS de actividad local") ?></a></td>

		<td><? echo gif_nulo(3,1) ?></td>

<? 	$centro = $this->hilo->obtener_item_inicial(); ?>

         <td class='listado-barra-superior-tabn' <? echo $js_cambiar_color_2 ?>>
        <a href="<? echo $this->vinculador->generar_solicitud($centro[0], $centro[1], $centro[2]) ?>" class="list-obj"  target="<? echo  apex_frame_centro ?>">
		 <? echo recurso::imagen_apl("home.gif",true,null,null,"Pagina inicial") ?></a></td>


		<td><? echo gif_nulo(3,1) ?></td>
         <td class='listado-barra-superior-tabn' <? echo $js_cambiar_color_2 ?>><a href="#" class="list-obj"  onclick='javascript:salir();return false;'>
		 <? echo recurso::imagen_apl("finalizar_sesion.gif",true,null,null,"Finalizar SESION") ?></a></td>

		<td><? echo gif_nulo(3,1) ?></td>

	</tr>
	</table>
</td></tr>
<tr><td  ><? echo gif_nulo(1,2) ?></td></tr>
<tr><td  class='listado-linea' ><? echo gif_nulo(1,1) ?></td></tr>
<tr><td  class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td class='listado-normal'>
	<table class='tabla-0' width='100%'>
	<tr > 

		<td><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("items/item.gif",true) ?></td>
		 <td class='listado-tabn' <? echo $js_cambiar_color_1 ?> >
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/items/catalogo_unificado",null,false,false,null,true,'lateral') ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">ITEMS</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("objetos/objeto.gif",true) ?></td>
		<td class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/objetos/organizador") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">OBJETOS</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi'><? echo recurso::imagen_apl("usuarios/usuario.gif",true) ?></td>
		<td class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/usuarios/listado") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">USUARIOS</a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

		<td class='listado-tabi' <? echo $js_cambiar_color_1 ?>>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/dimensiones/listado") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">&nbsp;<? echo recurso::imagen_apl("dimension.gif",true) ?></a>
		</td>
		
		<td class='listado-tabi' <? echo $js_cambiar_color_1 ?>>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/pruebas/testing_automatico_web") ?>" class="list-obj" target="<? echo  apex_frame_centro ?>">&nbsp;<? echo recurso::imagen_apl("testing.gif",true,null,null,"Testing automático") ?></a>
		</td>
		<td><? echo gif_nulo(3,1) ?></td>

		</tr>
	</table>
</td></tr>
<tr><td  class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td class='listado-normal'>
<table class='tabla-0' width='100%'>
	<tr > 

		 <td ><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("clases.gif",true) ?></td>
		<td class='listado-tabi' width='2%'><? echo recurso::imagen_apl("patrones.gif",true) ?></td>
		<td class='listado-tabi' width='2%'><? echo recurso::imagen_apl("nucleo.gif",true) ?></td>
		 <td  class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/apex/listado") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">EXT</a>
		</td>

		 <td ><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("fuente.gif",true) ?></td>
		 <td  class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/datos/organizador") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">DATOS</a>
		</td>

		<td ><? echo gif_nulo(3,1) ?></td>

		 <td class='listado-tabi'><? echo recurso::imagen_apl("proyecto.gif",true) ?></td>
		 <td  class='listado-tabn' <? echo $js_cambiar_color_1 ?>>
		<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/proyectos/organizador") ?>" class="list-obj" target="<? echo  apex_frame_lista ?>">PROYECTO</a>
		</td>
		<td><? echo gif_nulo(3,1) ?></td>
		<td class='listado-tabi' <? echo $js_cambiar_color_1 ?>>
			<a href="<? echo $this->vinculador->generar_solicitud("toba","/admin/objetos_toba/crear",null,false,false,null,true) ?>" class="list-obj" target="<? echo  apex_frame_centro ?>">&nbsp;<? echo recurso::imagen_apl("objetos/objeto_nuevo.gif",true,null,null,"Crear Objeto") ?></a>
		</td>

		<td><? echo gif_nulo(3,1) ?></td>

	</tr>
	</table>
</td></tr>
<tr><td colspan=2 class='listado-normal'><? echo gif_nulo(1,4) ?></td></tr>
<tr><td colspan=2 class='listado-linea' ><? echo gif_nulo(1,1) ?></td></tr>
</table>