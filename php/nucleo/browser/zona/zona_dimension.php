<?php
require_once("nucleo/browser/zona/zona.php");

class zona_dimension extends zona
{
	function zona_dimension($id,$proyecto,&$solicitud)
	{
		$this->listado = "dimension";
		parent::zona($id,$proyecto,$solicitud);
	}

	function cargar_editable($editable=null)
	//Carga el EDITABLE que se va a manejar dentro de la ZONA
	{
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
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
			$clave[1] = $editable['dimension'];
		}
		$sql = 	"	SELECT	*
					FROM	apex_dimension
					WHERE	proyecto='{$clave[0]}'
					AND		dimension='{$clave[1]}';";
		//echo $sql;
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			monitor::evento("bug","ZONA-DIMENSION: NO se pudo cargar el editable ".$clave[0].",".$clave[1]." - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA-DIMENSION: El editable solicitado no existe","info");
			return false;
		}else{
			$this->editable_info = current($rs->getArray());
			//ei_arbol($this->editable_info,"EDITABLE");
			$this->editable_id = array( $clave[0],$clave[1] );
			$this->editable_cargado = true;
			return true;
		}	
	}

	function obtener_html_barra_info()
	//Muestra la seccion INFORMATIVA (izquierda) de la barra
	{
		echo "	<td width='250' class='barra-item-id'>";
//		echo "&nbsp;".$this->editable_id[0]."&nbsp;";
		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
//		echo "&nbsp;".$this->editable_id[1]."@".$this->editable_id[0]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['nombre']."</td>";
	}

	function obtener_html_barra_especifico()
	//Esto es especifico de cada EDITABLE
	{	
 		echo "<td  class='barra-obj-tit' width='15'>&nbsp;</td>";
		//Acceso al codigo PHP
		if(($this->editable_info['dimension_tipo_proyecto']=="toba") &&
			($this->editable_info['dimension_tipo'])=="combo_db_restric")
		{
			$param['texto'] = 'Administracion de la tabla de asociaciones';
			$param['tipo'] = "normal";
			$param['imagen_recurso_origen'] = "apex";
			$param['imagen'] = 'tabla.gif';
			echo "<td  class='barra-item-link' width='1'>";
			echo $this->solicitud->vinculador->generar_solicitud("toba","/admin/dimensiones/tabla_restric",null,true,false,$param);
			echo "</a>";
			echo "</td>";
		}
	}

	function obtener_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		//La representacion del objeto fantasma no deberia tener barra inferior.
		if(($this->editable_id[1]=="0") && ($this->editable_id[0]=="toba")) return;
		//---------------------------------------------------------
		//---------------- Barra de FILTRO ------------------
		//---------------------------------------------------------
		echo "<br>";
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>FILTROS consumidores</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	o.proyecto as				objeto_proyecto,
							o.objeto as					objeto,
							o.nombre as					objeto_nombre,
							o.clase_proyecto as			clase_proyecto,
							o.clase as					clase,
							c.icono as					clase_icono,
							c.editor_proyecto as		clase_editor_proyecto,
							c.editor_item as			clase_editor,
							c.instanciador_proyecto as	clase_instanciador_proyecto,
							c.instanciador_item as		clase_instanciador
					FROM	apex_dimension d,
							apex_objeto_filtro f,
							apex_objeto o,
							apex_clase c
					WHERE	f.objeto_filtro = o.objeto
					AND		f.objeto_filtro_proyecto = o.proyecto
					AND		f.dimension = d.dimension
					AND		f.dimension_proyecto = d.proyecto
					AND		o.clase_proyecto = c.proyecto
					AND		o.clase = c.clase
					AND		d.proyecto='".$this->editable_id[0]."'
					AND		d.dimension='".$this->editable_id[1]."'
					ORDER BY 4,5,6;";
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor DIMENSIONES: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				echo "<table class='tabla-0' width='400'>";
				echo "<tr>";
				echo "<td  colspan='2' class='barra-obj-tit'>OBJETO</td>";
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
				echo "La dimension no es consumida por ningun FILTRO";
			}
		//---------------------------------------------------------
		//---------------- Barra de  ------------------
		//---------------------------------------------------------
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>PERFILES de DATOS consumidores</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	p.proyecto as 					proyecto,
							p.usuario_perfil_datos as 		perfil_datos,
							p.nombre as 					nombre,
							p.descripcion as 				descripcion
					FROM	apex_usuario_perfil_datos p,
							apex_dimension_perfil_datos d
					WHERE	p.usuario_perfil_datos = d.usuario_perfil_datos
					AND		p.proyecto = d.usuario_perfil_datos_proyecto
					AND		d.dimension_proyecto='".$this->editable_id[0]."'
					AND		d.dimension='".$this->editable_id[1]."'
					ORDER BY nombre;";
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				monitor::evento("bug","BARRA INFERIOR editor DIMENSIONES: NO se pudo cargar definicion. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			}
			if(!$rs->EOF){
				$param_html['texto'] = "Editar PERFIL de DATOS";
				$param_html['tipo'] = "normal";
				$param_html['imagen_recurso_origen'] = "apex";
				$param_html['imagen'] = "usuarios/perfil.gif";
				echo "<table class='tabla-0' width='400'>";
				echo "<tr>";
				echo "<td  class='barra-obj-tit'>Ed</td>";
				echo "<td  class='barra-obj-tit'>Perfil de Datos</td>";
				echo "</tr>\n";
				while(!$rs->EOF){
					echo "<tr>";
						echo "<td  class='barra-obj-link' width='5'>";
						echo $this->solicitud->vinculador->generar_solicitud(
													"toba","/admin/usuarios/perfil",
													array(apex_hilo_qs_zona=>$rs->fields["proyecto"]
													. apex_qs_separador. $rs->fields["perfil_datos"]),
													false, false, $param_html );
						echo "</td>";
						echo "<td  class='barra-obj-link' width='100%' >".$rs->fields["nombre"]."</td>";

					echo "</tr>\n";
					$rs->movenext();
				}
				echo "</table>\n";
			}else{
				echo "La dimension no es consumida por ningun PERFIL de DATOS";
			}
		echo "</td></tr></table>";
 	}
}
?>