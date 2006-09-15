<?php
require_once("zona_editor.php");
require_once('modelo/consultas/dao_editores.php');

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
							c.vinculos as					clase_vinculos,
							( SELECT count(*) 
								FROM apex_clase_dependencias cd 
								WHERE c.clase = cd.clase_consumidora 
								AND c.proyecto = cd.clase_consumidora_proyecto ) as clase_dep
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
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $this->editable_id[0],$this->editable_id[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

	function generar_html_barra_vinculos()
	//Esto es especifico de cada EDITABLE
	{	
		parent::generar_html_barra_vinculos();
		//Acceso al EDITOR PHP
		if( $this->editable_info['subclase'] && $this->editable_info['subclase_archivo'] )
		{
			// Ir al editor
			echo "<a href='" . toba::vinculador()->generar_solicitud(toba_editor::get_id(),'/admin/objetos/php',null,true) ."'>";
			echo toba_recurso::imagen_apl("php.gif",true,null,null,"Editar el PHP de la clase");
			echo "</a>";
			// Apertura del archivo
			$opciones = array('servicio' => 'ejecutar', 'zona' => true, 'celda_memoria' => 'ajax');
			$vinculo = toba::vinculador()->crear_vinculo(toba_editor::get_id(),"/admin/objetos/php", null, $opciones);
			$js = "toba.comunicar_vinculo('$vinculo')";
			echo "<a href='#' onclick=\"$js\">";
			echo toba_recurso::imagen_apl("reflexion/abrir.gif",true,null,null,'Abrir extensión PHP en el editor del escritorio.');
			echo "</a>";
		}
	}

	function generar_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		//La representacion del Componente fantasma no deberia tener barra inferior.
		if(($this->editable_id[1]=="0") && ($this->editable_id[0]=="toba")) return;
		echo "<br>";

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

			if($datos){
				echo "<table width='100%' class='tabla-0'>";
				echo "<tr><td  class='barra-obj-io'>Componentes CONSUMIDORES</td></tr>";
				echo "<tr><td  class='barra-obj-leve'>";
				echo "<table class='tabla-0'>";
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
						echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_apl($rs["clase_icono"],true)."</td>";
						echo "<td  class='barra-obj-link' >[".$rs["objeto"]."] ".$rs["objeto_nombre"]."</td>";
						echo "<td  class='barra-obj-link'>".$rs["objeto_identificador"]."</td>";
						if (!in_array($rs['clase'], dao_editores::get_clases_validas())) { 
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . toba::vinculador()->generar_solicitud(
														toba_editor::get_id(),"/admin/objetos/propiedades",
														array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
															.apex_qs_separador. $rs["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del Componente"). "</a>";
							echo "</td>\n";
						}
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($rs["clase_editor"])){
							echo "<a href='" . toba::vinculador()->generar_solicitud(
														$rs["clase_editor_proyecto"],
														$rs["clase_editor"],
														array(apex_hilo_qs_zona=>$rs["objeto_proyecto"]
															 .apex_qs_separador. $rs["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del Componente"). "</a>";
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
			if(!empty($rs)) {
				echo "<table width='100%' class='tabla-0'>";
				echo "<tr><td  class='barra-obj-io'>Componentes UTILIZADOS</td></tr>";
				echo "<tr><td  class='barra-obj-leve'>";
				echo "<table class='tabla-0'>";
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
						echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_apl($fila["clase_icono"],true)."</td>";
						echo "<td  class='barra-obj-link' >[".$fila["objeto"]."] ".$fila["objeto_nombre"]."</td>";
								//Si es un objeto viejo mostrar el el link a las propiedades básicas
						if (!in_array($fila['clase'], dao_editores::get_clases_validas())) { 
							echo "<td  class='barra-obj-id' width='5'>";		
							echo "<a href='" . toba::vinculador()->generar_solicitud(
													toba_editor::get_id(),"/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$fila["objeto_proyecto"]
														.apex_qs_separador. $fila["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del Componente"). "</a>";
							echo "</td>\n";
						}							
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($fila["clase_editor"])){
							echo "<a href='" . toba::vinculador()->generar_solicitud(
														$fila["clase_editor_proyecto"],
														$fila["clase_editor"],
														array(apex_hilo_qs_zona=>$fila["objeto_proyecto"]
															 .apex_qs_separador. $fila["objeto"]) ) ."'>".
								toba_recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del Componente"). "</a>";
						}
						echo "</td>\n";
					echo "</tr>\n";
				}
				echo "</table>\n";
				echo "</td></tr></table>";	
			}

		//---------------------------------------------------------
		//---------------- Barra de ITEMs consumidores ------------
		//---------------------------------------------------------
		if ($this->editable_info['clase'] == 'objeto_ci' || $this->editable_info['clase'] == 'objeto_cn' ) {
			echo "<table width='100%' class='tabla-0'>";
			echo "<tr><td  class='barra-obj-io'>ITEMS Consumidores</td></tr>";
			echo "<tr><td  class='barra-obj-leve'>";
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
				if($datos){
					echo "<table class='tabla-0' width='400'>";
					echo "<tr>";
					echo "<td  colspan='2' class='barra-obj-tit'>ITEM</td>";
					echo "<td  colspan='2' class='barra-obj-tit'>Editar</td>";
					echo "</tr>\n";
					foreach($datos as $rs){
						echo "<tr>";
						echo "<td  class='barra-obj-link' width='1%' >&nbsp;".$rs["proyecto"]."&nbsp;</td>";
						echo "<td  class='barra-obj-link' >&nbsp;".$rs["item"]."&nbsp;</td>";
	
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . toba::vinculador()->generar_solicitud(
													toba_editor::get_id(),"/admin/items/editor_items",
													array(apex_hilo_qs_zona=>$rs["proyecto"]
														.apex_qs_separador. $rs["item"]) ) ."'>".
							toba_recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM consumidor"). "</a>";
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
				}else{
					echo "No hay ITEMs consumidores";
				}
			echo "</td></tr></table>";	
		}
 	}
}
?>