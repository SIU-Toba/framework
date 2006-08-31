<?php
require_once('modelo/consultas/dao_editores.php');
require_once("zona_editor.php");

class zona_item extends zona_editor
{
	function cargar_info()
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		//Cuando se cargan explicitamente (generalmente desde el ABM que maneja la EXISTENCIA del EDITABLE)
		//Las claves de los registros que los ABM manejan son asociativas
		$sql = 	"	SELECT	i.*,
					p.archivo as 	actividad_patron_archivo
					FROM	apex_item i, apex_patron p
					WHERE	i.actividad_patron = p.patron
                    AND     i.actividad_patron_proyecto = p.proyecto
					AND		i.proyecto='{$this->editable_id[0]}'
					AND		item='{$this->editable_id[1]}';";
		$rs = toba::get_db()->consultar($sql);
		if(empty($rs)) {
			throw new toba_excepcion("No se puede encontrar informacion del item {$this->editable_id[0]},{$this->editable_id[1]}");
		} else {
			$this->editable_info = $rs[0];
			return true;
		}	
	}
	
	function obtener_html_barra_vinculos()
	{
		$parametros = array("proyecto"=> $this->editable_id[0], "item"=> $this->editable_id[1]);
 		echo toba::get_vinculador()->generar_solicitud(toba_editor::get_id(),"/admin/items/catalogo_unificado",
 																$parametros,true, false, false, "", null, null, 'lateral');
		parent::obtener_html_barra_vinculos();		
		
		echo "<a href='" . toba::get_vinculador()->generar_solicitud($this->editable_id[0],$this->editable_id[1]) ."'>";
		echo toba_recurso::imagen_apl("items/instanciar.gif",true,null,null,"Ejecutar el item");
		echo "</a>";
		
	}
	
	function obtener_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		echo "<br>";
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>Elementos referenciados</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		$sql = 	"	SELECT	o.proyecto as				objeto_proyecto,
							o.objeto as					objeto,
							o.nombre as					objeto_nombre,
							o.clase_proyecto as			clase_proyecto,
							o.clase as					clase,
							io.orden as		 			objeto_orden,
							c.icono as					clase_icono,
							c.editor_proyecto as		clase_editor_proyecto,
							c.editor_item as			clase_editor,
							c.instanciador_proyecto as	clase_instanciador_proyecto,
							c.instanciador_item as		clase_instanciador
					FROM	apex_item_objeto io,
							apex_objeto o,
							apex_clase c
					WHERE	io.objeto = o.objeto
					AND		io.proyecto = o.proyecto
					AND		o.clase_proyecto = c.proyecto
					AND		o.clase = c.clase
					AND		io.proyecto='".$this->editable_id[0]."'
					AND		io.item='".$this->editable_id[1]."'
					ORDER BY 4,5,6;";
			$datos = toba::get_db()->consultar($sql);
			if(! empty($datos)){
				echo "<table class='tabla-0'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>OBJETO</td>";
				echo "<td  class='barra-obj-tit'>INVOCACION</td>";
				echo "<td  colspan='3' class='barra-obj-tit'>Editar</td>";
				echo "</tr>\n";
				foreach($datos as $rs){
					if(!isset($contador[$rs["clase"]])){
						$contador[$rs["clase"]] = 0;
					}else{
						$contador[$rs["clase"]] += 1;
					}
					echo "<tr>";
						echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_apl($rs["clase_icono"],true)."</td>";
						echo "<td  class='barra-obj-link' >[".$rs["objeto"]."] ".$rs["objeto_nombre"]."</td>";
						echo "<td  class='barra-obj-link'>\$this->cargar_objeto(\"".$rs["clase"]."\", ".($contador[$rs["clase"]]).")</td>";
						if (!in_array($rs['clase'], dao_editores::get_clases_validas())) { 
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
													toba_editor::get_id(),"/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
														.apex_qs_separador. $rs["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
							echo "</td>\n";
						}
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($rs["clase_editor"])){
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
														$rs["clase_editor_proyecto"],
														$rs["clase_editor"],
														array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
															 .apex_qs_separador. $rs["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
						}
						if(isset($rs["clase_instanciador"])){
							echo "</td>\n";
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
														$rs["clase_instanciador_proyecto"], 
														$rs["clase_instanciador"],
														array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
															.apex_qs_separador. $rs["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"INSTANCIAR el OBJETO"). "</a>";
						}
						echo "</td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n";
			}else{
				echo "El ITEM no consume OBJETOS.";
			}
		echo "</td></tr></table>";
	}
}
?>