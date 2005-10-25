<?php
require_once("nucleo/browser/zona/zona.php");
require_once("admin/db/dao_editores.php");

class zona_objeto extends zona
{
	function zona_objeto($id,$proyecto,&$solicitud)
	{
		$this->listado = "objeto";
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
			$clave[1] = $editable['objeto'];
		}
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
							d.fuente_datos as				fuente,
							d.fuente_datos_motor as			fuente_motor,
							d.host as						fuente_host,
							d.usuario as					fuente_usuario,
							d.clave as						fuente_clave,
							d.base as						fuente_base,
							( SELECT count(*) 
								FROM apex_clase_dependencias cd 
								WHERE c.clase = cd.clase_consumidora 
								AND c.proyecto = cd.clase_consumidora_proyecto ) as clase_dep
					FROM	apex_objeto o,
							apex_fuente_datos d,
							apex_clase c
					WHERE	o.fuente_datos = d.fuente_datos
					AND		o.clase = c.clase
					AND		o.proyecto='{$clave[0]}'
					AND		o.objeto='{$clave[1]}'";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","ZONA-OBJETO: NO se pudo cargar el editable $proyecto,$item - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA-OBJETO: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs->getArray());
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $clave[0],$clave[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}
//-----------------------------------------------------

	function obtener_html_barra_superior()
	//Genera el HTML de la BARRA
	{
		//global $cronometro;
		//$cronometro->marcar('basura',apex_nivel_nucleo);

		echo "<table  width='100%'  class='tabla-0'><tr>";

		//INTERFACE que solicta CRONOMETRAR la PAGINA
		if($this->solicitud->vinculador->consultar_vinculo("toba","/basicos/cronometro",true))
		{
			echo "<td  class='barra-0-edit' width='1'>";
			echo "<a href='".$this->solicitud->vinculador->generar_solicitud(null,null,null,true,true)."'>".
					recurso::imagen_apl("cronometro.gif",true,null,null,"Cronometrar la ejecución del ITEM")."</a>";
			echo "</td>";
		}

		echo "<td width='90%' class='barra-obj-tit1'>&nbsp;&nbsp;EDITOR de OBJETOS";
		//echo recurso::imagen_apl("zona/objetos.gif",true);
		echo "</td>";
		$this->obtener_html_barra_vinculos();
		$this->obtener_html_barra_especifico();
		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		echo "</tr></table>\n";

		//Nombre de la operacion
		echo "<table  width='100%'  class='tabla-0'><tr>";
		echo "	<td   width='10' class='barra-item-id'>";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;</td>";
		echo "<td class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
		echo "</tr></table>\n";
		//$cronometro->marcar('ZONA: Barra SUPERIOR',apex_nivel_nucleo);
	}
//-----------------------------------------------------

	function obtener_html_barra_especifico()
	//Esto es especifico de cada EDITABLE
	{	
 		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		//Acceso al codigo PHP
		if(isset($this->editable_info['subclase_archivo']))
		{
			echo "<td  class='barra-item-link' width='1'>";
			echo "<a href='" . $this->solicitud->vinculador->generar_solicitud("toba","/admin/objetos/php",null,true) ."'>";
			echo recurso::imagen_apl("php.gif",true,null,null,"Manejo de la subclase");
			echo "</a></td>";
		}
		
		//Acceso a las dependencias del objeto
		//Esto es obsoleto para editores nuevos
		if( $this->editable_info['clase_dep'] > 0 && 
			!in_array($this->editable_info['clase'], dao_editores::get_clases_validas()))
		{
			echo "<td  class='barra-item-link' width='1'>";
			echo "<a href='" . $this->solicitud->vinculador->generar_solicitud("toba","/admin/objetos/dependencias",null,true) ."'>";
			echo recurso::imagen_apl("objetos/asociar_objeto.gif",true,null,null,"Editar DEPENDECIAS del OBJETO");
			echo "</a></td>";
		}

		
		if($this->editable_info['clase_vinculos'] == 1)
		{
			echo "<td  class='barra-item-link' width='1'>";
			echo "<a href='" . $this->solicitud->vinculador->generar_solicitud("toba","/admin/objetos/vinculos",null,true) ."'>";
			echo recurso::imagen_apl("vinculos.gif",true,null,null,"Editar VINCULOS del OBJETO");
			echo "</a></td>";
		}
/*		//Falta implementar SEGURIDAD
		//Acceso al EDITOR de la CLASE
		$zona = array( apex_hilo_qs_zona => $this->editable_info['clase_proyecto']
				 . apex_qs_separador . $this->editable_info['clase']);
		echo "<td  class='barra-item-link' width='1'>";
		echo "<a href='" . $this->solicitud->vinculador->generar_solicitud("toba","/admin/apex/clase_propiedades",$zona) ."'>";
		echo recurso::imagen_apl("clases.gif",true,null,null,"Instanciar el OBJETO");
		echo "</a>";
		echo "</td>";
*/
		//Acceso al EDITOR del objeto
		if(isset($this->editable_info['clase_editor']))
		{
			echo "<td  class='barra-item-link' width='1'>";
			echo "<a href='" . $this->solicitud->vinculador->generar_solicitud($this->editable_info['clase_editor_proyecto'],$this->editable_info['clase_editor'],null,true) ."'>";
			echo recurso::imagen_apl("objetos/editar.gif",true,null,null,"Propiedades ESPECIFICAS del OBJETO");
			echo "</a></td>";
		}
		//Acceso al INSTANCIADOR
		if(isset($this->editable_info['clase_instanciador']))
		{
			echo "<td  class='barra-item-link' width='1'>";
			echo "<a href='" . $this->solicitud->vinculador->generar_solicitud($this->editable_info['clase_instanciador_proyecto'],$this->editable_info['clase_instanciador'],null,true) ."'>";
			echo recurso::imagen_apl("objetos/instanciar.gif",true,null,null,"Instanciar el OBJETO");
			echo "</a>";
			echo "</td>";
		}
	}
//-----------------------------------------------------
	
	function obtener_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		//La representacion del objeto fantasma no deberia tener barra inferior.
		if(($this->editable_id[1]=="0") && ($this->editable_id[0]=="toba")) return;
		//---------------------------------------------------------
		//---------------- Barra de DEPENDENCIAS ------------------
		//---------------------------------------------------------
		echo "<br>";
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>OBJETOS utilizados</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor item: NO se pudo cargar definicion: - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
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
						echo "<td  class='barra-obj-link'>\$this->dependencias[\"".$rs->fields["objeto_identificador"]."\"]->metodo()</td>";
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
													"toba","/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
							recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
						echo "</td>\n";
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($rs->fields["clase_editor"])){
							echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
														$rs->fields["clase_editor_proyecto"],
														$rs->fields["clase_editor"],
														array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
															 .apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
								recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
						}
						if(isset($rs->fields["clase_instanciador"])){
							echo "</td>\n";
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
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
		echo "</td></tr>";
		//---------------------------------------------------------
		//---------------- Barra de VINCULOS entrantes ------------
		//---------------------------------------------------------
		echo "<tr><td  class='barra-obj-io'>VINCULOS Entrantes</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	o.proyecto as				objeto_proyecto,
							o.objeto as					objeto,
							o.nombre as					objeto_nombre,
							o.descripcion as			objeto_descripcion,
							o.clase_proyecto as			clase_proyecto,
							o.clase as					clase,
							v.indice as		 			indice,
							c.icono as					clase_icono,
							c.editor_proyecto as		clase_editor_proyecto,
							c.editor_item as			clase_editor,
							c.instanciador_proyecto as	clase_instanciador_proyecto,
							c.instanciador_item as		clase_instanciador
					FROM	apex_vinculo v,
							apex_objeto o,
							apex_clase c
					WHERE	v.origen_objeto = o.objeto
					AND		v.origen_objeto_proyecto = o.proyecto
					AND		o.clase_proyecto = c.proyecto
					AND		o.clase = c.clase
					AND		v.destino_objeto_proyecto= '".$this->editable_id[0]."'
					AND		v.destino_objeto= '".$this->editable_id[1]."'
					ORDER BY 3,4;";
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor item: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				echo "<table class='tabla-0'  width='400'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>OBJETO</td>";
				echo "<td  class='barra-obj-tit'>Descripcion</td>";
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
						echo "<td  class='barra-obj-link' >".$rs->fields["objeto"]."</td>";
						echo "<td  class='barra-obj-link' width='300'>".$rs->fields["objeto_nombre"]. "&nbsp;-&nbsp;" . $rs->fields["objeto_descripcion"]."</td>";
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
													"toba","/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
							recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
						echo "</td>\n";
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
													"toba","/admin/objetos/vinculos",
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
							recurso::imagen_apl("vinculos.gif",true,null,null,"Editar VINCULOS del OBJETO"). "</a>";
						echo "</td>\n";
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
													$rs->fields["clase_editor_proyecto"],
													$rs->fields["clase_editor"],
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														 .apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
							recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
					echo "</tr>\n";
					$rs->movenext();
				}
				echo "</table>\n";
			}else{
				echo "No hay vinculos entrantes";
			}
		echo "</td></tr>";
		//---------------------------------------------------------
		//---------------- Barra de ITEMs consumidores ------------
		//---------------------------------------------------------
		echo "<tr><td  class='barra-obj-io'>ITEMS Consumidores</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor item: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				echo "<table class='tabla-0' width='400'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>ITEM</td>";
				echo "<td  colspan='2' class='barra-obj-tit'>Editar</td>";
				echo "</tr>\n";
				while(!$rs->EOF){
					echo "<tr>";
					echo "<td  class='barra-obj-link' width='1%' >&nbsp;".$rs->fields["proyecto"]."&nbsp;</td>";
					echo "<td  class='barra-obj-link' >&nbsp;".$rs->fields["item"]."&nbsp;</td>";

					echo "<td  class='barra-obj-id' width='5'>";
					echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
												"toba","/admin/items/propiedades",
												array(apex_hilo_qs_zona=>$rs->fields["proyecto"]
													.apex_qs_separador. $rs->fields["item"]) ) ."'>".
						recurso::imagen_apl("items/item.gif",true,null,null,"Editar propiedades del ITEM consumidor"). "</a>";
					echo "</td>\n";
					echo "<td  class='barra-obj-id' width='5'>";
					echo "<a href='" . $this->solicitud->vinculador->generar_solicitud($rs->fields["proyecto"],$rs->fields["item"]) ."'>".
						recurso::imagen_apl("items/instanciar.gif",true,null,null,"Instanciar el ITEM consumidor"). "</a>";
					echo "</td>\n";

					echo "</tr>\n";
					$rs->movenext();
				}
				echo "</table>\n";
			}else{
				echo "No hay ITEMs consumidores";
			}
		echo "</td></tr></table>";	
		//---------------------------------------------------------
		//---------------- OBJETOS consumidores ------------------
		//---------------------------------------------------------
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>Objetos consumidores</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor item: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				echo "<table class='tabla-0'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>OBJETO</td>";
				echo "<td  class='barra-obj-tit'>IDENTIFICADOR</td>";
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
						echo "<td  class='barra-obj-link'>\"".$rs->fields["objeto_identificador"]."\"</td>";
						echo "<td  class='barra-obj-id' width='5'>";
						echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
													"toba","/admin/objetos/propiedades",
													array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
														.apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
							recurso::imagen_apl("objetos/objeto.gif",true,null,null,"Editar propiedades BASICAS del OBJETO"). "</a>";
						echo "</td>\n";
						echo "<td  class='barra-obj-id' width='5'>";
						if(isset($rs->fields["clase_editor"])){
							echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
														$rs->fields["clase_editor_proyecto"],
														$rs->fields["clase_editor"],
														array(apex_hilo_qs_zona=>$rs->fields["objeto_proyecto"]
															 .apex_qs_separador. $rs->fields["objeto"]) ) ."'>".
								recurso::imagen_apl("objetos/editar.gif",true,null,null,"Editar propiedades ESPECIFICAS del OBJETO"). "</a>";
						}
						if(isset($rs->fields["clase_instanciador"])){
							echo "</td>\n";
							echo "<td  class='barra-obj-id' width='5'>";
							echo "<a href='" . $this->solicitud->vinculador->generar_solicitud(
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