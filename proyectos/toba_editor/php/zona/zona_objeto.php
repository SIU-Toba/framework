<?php
require_once("zona_editor.php");
require_once('modelo/consultas/dao_editores.php');
require_once('modelo/componentes/info_componente.php');
require_once('admin_util.php');

class zona_objeto extends zona_editor
{

	function cargar_info($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		$sql = 	"	SELECT	o.*,
							o.subclase_archivo as 			archivo,
							c.icono as						clase_icono,
							c.editor_proyecto as			clase_editor_proyecto,
							c.editor_item as				clase_editor,
							c.instanciador_proyecto as		clase_instanciador_proyecto,
							c.instanciador_item as			clase_instanciador,
							c.archivo as					clase_archivo,
							c.plan_dump_objeto as 			clase_plan_sql,
							c.vinculos as					clase_vinculos
					FROM	apex_objeto o,
							apex_clase c
					WHERE	o.clase = c.clase
					AND		o.proyecto='{$this->editable_id[0]}'
					AND		o.objeto='{$this->editable_id[1]}'";
		$rs = consultar_fuente($sql);
		if(empty($rs)){
			echo ei_mensaje("ZONA-OBJETO: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs);
			$this->editable_id = array( $this->editable_id[0],$this->editable_id[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

	function generar_html_barra_vinculos()
	{	
		//Acceso al EDITOR PHP
		if( $this->editable_info['subclase'] && $this->editable_info['subclase_archivo'] ) {
			$componente = $this->get_editable();
			// Apertura del archivo
			if ( !admin_util::existe_archivo_subclase($this->editable_info['subclase_archivo']) ) {
				// Ir al editor
				$ver = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$componente[0],
																			'componente' =>$componente[1] ), null, 'php_inexistente.gif');
				echo "<a href='" . $ver['vinculo'] ."'>" . toba_recurso::imagen($ver['imagen'], null, null, $ver['ayuda']). "</a>\n";
			} else {
				// Ir al editor
				$ver = info_componente::get_utileria_editor_ver_php( array(	'proyecto'=>$componente[0],
																			'componente' =>$componente[1] ) );			
				echo "<a href='" . $ver['vinculo'] ."'>" . toba_recurso::imagen($ver['imagen'], null, null, $ver['ayuda']). "</a>\n";
				// Abrir el archivo
				$abrir = info_componente::get_utileria_editor_abrir_php( array(	'proyecto'=>$componente[0],
																				'componente' =>$componente[1] )  );	
				echo "<a href=\"" . $abrir['vinculo'] ."\">". toba_recurso::imagen($abrir['imagen'], null, null, $abrir['ayuda']). "</a>\n";
			}
		}
		parent::generar_html_barra_vinculos();		

		$editor_item = $this->editable_info['clase_editor'];
		$editor_proyecto = $this->editable_info['clase_editor_proyecto'];
		$vinculo = toba::vinculador()->crear_vinculo( $editor_proyecto, $editor_item, array(), array('zona' => true));
		echo "<a href='$vinculo'>".toba_recurso::imagen_toba('objetos/editar.gif', true, null, null, 'Editar el componente')."</a>\n";
	}

	function generar_html_barra_inferior()	
	{
		$img_min = toba_recurso::imagen_toba('sentido_des_sel.gif', false);
		
		//La representacion del Componente fantasma no deberia tener barra inferior.
		if(($this->editable_id[1]=="0") && ($this->editable_id[0]=="toba")) return;
		echo "<br>";


		//---------------------------------------------------------
		//---------------- Barra de ITEMs consumidores ------------
		//---------------------------------------------------------
		
		$sql = 	"	SELECT	i.proyecto as				proyecto,
							i.item as					item,
							i.nombre as					nombre
					FROM	apex_item_objeto io,
							apex_item i
					WHERE	io.item = i.item
					AND		io.proyecto = i.proyecto
					AND		io.proyecto='".$this->editable_id[0]."'
					AND		io.objeto='".$this->editable_id[1]."'
					ORDER BY 2;";
		$datos = consultar_fuente($sql);			
		if (! empty($datos)) {
			$cant = count($datos);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"cambiar_colapsado($('zona_objeto_item_img'), $('zona_objeto_item'));\" title='Mostrar / Ocultar'";
			echo "<table width='100%' class='tabla-0'>";
			echo "<tr><td  class='barra-obj-io' $colapsado>".
					"<img class='ei-barra-colapsar' id='zona_objeto_item_img' src='$img_min'>".
					" Items Consumidores ($cant)</td></tr>";
			echo "<tr'><td  class='barra-obj-leve'>";
			echo "<table id='zona_objeto_item' style='display:none' class='tabla-0' width='400'>";
			echo "<tr>";
			echo "<td  colspan='2' class='barra-obj-tit'>ITEM</td>";
			echo "<td  colspan='2' class='barra-obj-tit'>Editar</td>";
			echo "</tr>\n";
			foreach($datos as $rs){
				echo "<tr>";
				//echo "<td  class='barra-obj-link' width='1%' >&nbsp;".$rs["proyecto"]."&nbsp;</td>";
				echo "<td  class='barra-obj-link' width='1%' >".toba_recurso::imagen_toba("items/item.gif",true)."</td>";
				echo "<td  class='barra-obj-link' >[".$rs["item"]."] {$rs['nombre']}</td>";

				echo "<td  class='barra-obj-link' width='5'>";
				echo "<a href='" . toba::vinculador()->generar_solicitud(
											toba_editor::get_id(),"/admin/items/editor_items",
											array(apex_hilo_qs_zona=>$rs["proyecto"]
												.apex_qs_separador. $rs["item"]) ) ."'>".
					toba_recurso::imagen_toba("objetos/editar.gif",true,null,null,"Editar propiedades del ITEM consumidor"). "</a>";
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</td></tr></table>";	
		}
		
		//---------------------------------------------------------
		//---------------- OBJETOS consumidores ------------------
		//---------------------------------------------------------

		$sql = 	"	SELECT	o.proyecto as				objeto_proyecto,
							o.objeto as					objeto,
							o.nombre as					objeto_nombre,
							o.clase_proyecto as			clase_proyecto,
							o.clase as					clase,
							d.identificador as 			objeto_identificador,
							c.icono as					clase_icono,
							c.editor_proyecto as		clase_editor_proyecto,
							c.editor_item as			clase_editor,
							c.instanciador_proyecto as	clase_instanciador_proyecto,
							c.instanciador_item as		clase_instanciador
					FROM	apex_objeto_dependencias d,
							apex_objeto o,
							apex_clase c
					WHERE	d.objeto_consumidor = o.objeto
					AND		d.proyecto = o.proyecto
					AND		o.clase_proyecto = c.proyecto
					AND		o.clase = c.clase
					AND		d.proyecto='".$this->editable_id[0]."'
					AND		d.objeto_proveedor='".$this->editable_id[1]."'
					ORDER BY 4,5,6;";
		$datos = consultar_fuente($sql);

		if (! empty($datos)) {
			$cant = count($datos);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"cambiar_colapsado($('zona_objeto_cont_img'), $('zona_objeto_cont'));\" title='Mostrar / Ocultar'";

			echo "<table width='100%' class='tabla-0'>";
			echo "<tr><td  class='barra-obj-io' $colapsado>".
					"<img class='ei-barra-colapsar' id='zona_objeto_cont_img' src='$img_min'>".
					" Controladores ($cant)</td></tr>";
			echo "<tr><td  class='barra-obj-leve'>";
			echo "<table  id='zona_objeto_cont' style='display:none' class='tabla-0'>";
			echo "<tr>";
			echo "<td  colspan='2' class='barra-obj-tit'>Componente</td>";
			echo "<td  class='barra-obj-tit'>ROL</td>";
			echo "<td  colspan='3' class='barra-obj-tit'>Editar</td>";
			echo "</tr>\n";
			foreach($datos as $rs){
				if(!isset($contador[$rs["clase"]])){
					$contador[$rs["clase"]] = 0;
				}else{
					$contador[$rs["clase"]] += 1;
				}
				echo "<tr>";
					echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_toba($rs["clase_icono"],true)."</td>";
					echo "<td  class='barra-obj-link' >[".$rs["objeto"]."] ".$rs["objeto_nombre"]."</td>";
					echo "<td  class='barra-obj-link'>".$rs["objeto_identificador"]."</td>";
					if (!in_array($rs['clase'], dao_editores::get_clases_validas())) { 
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . toba::vinculador()->generar_solicitud(
													toba_editor::get_id(),"/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
														.apex_qs_separador. $rs["objeto"]) ) ."'>".
							toba_recurso::imagen_toba("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del Componente"). "</a>";
						echo "</td>\n";
					}
					echo "<td  class='barra-obj-id' width='5'>";
					if(isset($rs["clase_editor"])){
						echo "<a href='" . toba::vinculador()->generar_solicitud(
													$rs["clase_editor_proyecto"],
													$rs["clase_editor"],
													array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
														 .apex_qs_separador. $rs["objeto"]) ) ."'>".
							toba_recurso::imagen_toba("objetos/editar.gif",true,null,null,"Editar el Componente"). "</a>";
					}
					echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</td></tr></table>";
		}



		//---------------------------------------------------------
		//---------------- Barra de DEPENDENCIAS ------------------
		//---------------------------------------------------------
		$sql = 	"	SELECT	o.proyecto as				objeto_proyecto,
							o.objeto as					objeto,
							o.nombre as					objeto_nombre,
							o.clase_proyecto as			clase_proyecto,
							o.clase as					clase,
							d.identificador as 			objeto_identificador,
							c.icono as					clase_icono,
							c.editor_proyecto as		clase_editor_proyecto,
							c.editor_item as			clase_editor,
							c.instanciador_proyecto as	clase_instanciador_proyecto,
							c.instanciador_item as		clase_instanciador
					FROM	apex_objeto_dependencias d,
							apex_objeto o,
							apex_clase c
					WHERE	d.objeto_proveedor = o.objeto
					AND		d.proyecto = o.proyecto
					AND		o.clase_proyecto = c.proyecto
					AND		o.clase = c.clase
					AND		d.proyecto='".$this->editable_id[0]."'
					AND		d.objeto_consumidor='".$this->editable_id[1]."'
					ORDER BY 4,5,6;";
		$rs = consultar_fuente($sql);
		if (!empty($rs)) {
			$cant = count($rs);
			$colapsado = "style='cursor: pointer; cursor: hand;' onclick=\"cambiar_colapsado($('zona_objeto_dep_img'), $('zona_objeto_dep'));\" title='Mostrar / Ocultar'";
			echo "<table width='100%' class='tabla-0'>";
			echo "<tr><td  class='barra-obj-io' $colapsado>".
					"<img class='ei-barra-colapsar' id='zona_objeto_dep_img' src='$img_min'>".
					" Dependencias ($cant)</td></tr>";
			echo "<tr ><td  class='barra-obj-leve'>";
			echo "<table id='zona_objeto_dep' style='display:none' class='tabla-0'>";
			echo "<tr>";
			echo "<td  colspan='2' class='barra-obj-tit'>COMPONENTE</td>";
			echo "<td  colspan='3' class='barra-obj-tit'>Editar</td>";
			echo "</tr>\n";
			foreach ($rs as $fila) {
				if(!isset($contador[$fila["clase"]])){
					$contador[$fila["clase"]] = 0;
				}else{
					$contador[$fila["clase"]] += 1;
				}
				echo "<tr>";
					echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_toba($fila["clase_icono"],true)."</td>";
					echo "<td  class='barra-obj-link' >[".$fila["objeto"]."] ".$fila["objeto_nombre"]."</td>";
					echo "<td  class='barra-obj-link' width='5'>";
					if(isset($fila["clase_editor"])){
						echo "<a href='" . toba::vinculador()->generar_solicitud(
													$fila["clase_editor_proyecto"],
													$fila["clase_editor"],
													array(apex_hilo_qs_zona=>$fila["objeto_proyecto"]
														 .apex_qs_separador. $fila["objeto"]) ) ."'>".
							toba_recurso::imagen_toba("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del Componente"). "</a>";
					}
					echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
			echo "</td></tr></table>";	
		}		
 	}
}
?>