<?php
require_once("nucleo/browser/zona/zona.php");
require_once("db/dao_editores.php");

class zona_item extends zona
{
	function zona_item($id,$proyecto,&$solicitud)
	{
		$this->listado = "item";
		parent::zona($id,$proyecto,$solicitud);
	}

	function cargar_editable($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		if(!isset($editable)){
			if(!isset($this->editable_propagado)){
				ei_mensaje("No se especifico el editable a cargar","error");
				return false;
			}else{
				//Los editables se propagan como arrays comunes
				$clave[0] = $this->editable_propagado[0];
				$clave[1] = $this->editable_propagado[1];
			}
		}else{
			//Cuando se cargan explicitamente (generalmente desde el ABM que maneja la EXISTENCIA del EDITABLE)
			//Las claves de los registros que los ABM manejan son asociativas
			$clave[0] = $editable['proyecto'];
			$clave[1] = $editable['item'];
		}
		$sql = 	"	SELECT	i.*,
					p.archivo as 	actividad_patron_archivo
					FROM	apex_item i, apex_patron p
					WHERE	i.actividad_patron = p.patron
                    AND     i.actividad_patron_proyecto = p.proyecto
					AND		i.proyecto='{$clave[0]}'
					AND		item='{$clave[1]}';";
		$rs = toba::get_db('instancia')->consultar($sql);
		if(empty($rs)) {
			throw new excepcion_toba("No se puede encontrar informacion del item {$clave[0]},{$clave[1]}");
		} else {
			$this->editable_info = $rs[0];
			$this->editable_id = array( $clave[0],$clave[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

	function obtener_html_barra_superior()
	//Genera el HTML de la BARRA
	{
		//global $cronometro;
		//$cronometro->marcar('basura',apex_nivel_nucleo);

		echo "<table width='100%' class='tabla-0'><tr>";

		echo "<td width='90%' class='barra-obj-tit1'>&nbsp;&nbsp;EDITOR de ITEMS";
		//echo recurso::imagen_apl("zona/objetos.gif",true);
		echo "</td>";
		
		//Vinculo a la vista lateral
		echo "<td class='barra-item-link' width='1'>";		
		$parametros = array("proyecto"=> $this->editable_id[0], "item"=> $this->editable_id[1]);
 		echo toba::get_vinculador()->obtener_vinculo_a_item_cp('admin',"/admin/items/catalogo_unificado",
 																$parametros,true, false, false, "", null, null, 'lateral');
		echo "</td>";
		
		$this->obtener_html_barra_vinculos();
		$this->obtener_html_barra_especifico();
		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		echo "</tr></table>\n";

		//Nombre de la operacion
		echo "<table  width='100%'  class='tabla-0'><tr>";
		echo "	<td   width='10' class='barra-item-id'>";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
		echo "<td class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
		echo "</tr></table>\n";
		//$cronometro->marcar('ZONA: Barra SUPERIOR',apex_nivel_nucleo);
	}
//-----------------------------------------------------



	function obtener_html_barra_especifico()
	//Esto es especifico de cada EDITABLE
	{	
                
                //ATENCION, solicitud directa
                $id_buffer = array(apex_hilo_qs_zona => $this->editable_info['actividad_buffer_proyecto']
                                         . apex_qs_separador . $this->editable_info['actividad_buffer'] );
                if($vinculo = toba::get_vinculador()->obtener_vinculo_a_item('admin',"/admin/buffers/propiedades",$id_buffer,true))
                {
                        if( !(($this->editable_info['actividad_buffer_proyecto']=="toba" )
                             &&($this->editable_info['actividad_buffer']=="0"))){
         		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
	        	echo "<td  class='barra-item-link' width='1'>";
		        echo $vinculo;
        		echo "</a>";
	        	echo "</td>";
                        }
                }

 		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		echo "<td  class='barra-item-link' width='1'>";
		echo "<a href='" . toba::get_vinculador()->generar_solicitud($this->editable_id[0],$this->editable_id[1]) ."'>";
		echo recurso::imagen_apl("items/instanciar.gif",true,null,null,"Generar una SOLICITUD a este ITEM");
		echo "</a>";
		echo "</td>";
	}

	function obtener_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		echo "<br>";
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>Elementos referenciados</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor item: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				echo "<table class='tabla-0'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>OBJETO</td>";
				echo "<td  class='barra-obj-tit'>INVOCACION</td>";
				echo "<td  colspan='3' class='barra-obj-tit'>Editar</td>";
				echo "</tr>\n";
				while(!$rs->EOF){
					if(!isset($contador[$rs->fields["clase"]])){
						$contador[$rs->fields["clase"]] = 0;
					}else{
						$contador[$rs->fields["clase"]] += 1;
					}
					echo "<tr>";
						echo "<td  class='barra-obj-link' width='5'>".recurso::imagen_apl($rs->fields["clase_icono"],true)."</td>";
						echo "<td  class='barra-obj-link' >[".$rs->fields["objeto"]."] ".$rs->fields["objeto_nombre"]."</td>";
						echo "<td  class='barra-obj-link'>\$this->cargar_objeto(\"".$rs->fields["clase"]."\", ".($contador[$rs->fields["clase"]]).")</td>";
						if (!in_array($rs->fields['clase'], dao_editores::get_clases_validas())) { 
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
													'admin',"/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
								recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
							echo "</td>\n";
						}
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($rs->fields["clase_editor"])){
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
														$rs->fields["clase_editor_proyecto"],
														$rs->fields["clase_editor"],
														array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
															 .apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
								recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
						}
						if(isset($rs->fields["clase_instanciador"])){
							echo "</td>\n";
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . toba::get_vinculador()->generar_solicitud(
														$rs->fields["clase_instanciador_proyecto"], 
														$rs->fields["clase_instanciador"],
														array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
															.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
								recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"INSTANCIAR el OBJETO"). "</a>";
						}
						echo "</td>\n";
					echo "</tr>\n";
					$rs->movenext();
				}
				echo "</table>\n";
			}else{
				echo "El ITEM no consume OBJETOS.";
			}
		echo "</td></tr></table>";
	}
}
?>