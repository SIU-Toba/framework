<?php
require_once("nucleo/lib/zona.php");

class zona_buffer extends zona
{
	function zona_buffer($id,$proyecto,&$solicitud)
	{
		$this->listado = "buffer";
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
			$clave[1] = $editable['buffer'];
		}
		global $ADODB_FETCH_MODE, $db, $cronometro;		
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	*
					FROM	apex_buffer
					WHERE	proyecto ='{$clave[0]}'
					AND		buffer ='{$clave[1]}';";
		$rs =& $db["instancia"][apex_db_con]->Execute($sql);
		if(!$rs){
			throw new excepcion_toba("ZONA-CLASE: NO se pudo cargar el editable $proyecto,$item - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
			return false;
		}elseif($rs->EOF){
			echo ei_mensaje("ZONA-CLASE: El editable solicitado no existe","info");
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
//		echo "&nbsp;".$this->editable_id[1]."&nbsp;";
		echo "&nbsp;".$this->editable_id[0]." - ".$this->editable_id[1]."&nbsp;";
		echo "</td>";
		echo "<td width='60%' class='barra-item-tit'>&nbsp;".$this->editable_info['descripcion_corta']."</td>";
	}

	function obtener_html_barra_inferior()	
	//Genera la barra especifica inferior del EDITABLE
	{
		//---------------------------------------------------------
		//---------------- Barra de ITEMs consumidores ------------
		//---------------------------------------------------------
		echo "<br>";
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>ITEMS Consumidores</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		global $ADODB_FETCH_MODE, $db;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$sql = 	"	SELECT	                i.proyecto as				proyecto,
							i.item as					item,
							i.nombre as					nombre
					FROM		apex_item i
					WHERE	        i.actividad_buffer_proyecto='".$this->editable_id[0]."'
					AND		i.actividad_buffer='".$this->editable_id[1]."'
					ORDER BY 2;";
			$rs =& $db["instancia"][apex_db_con]->Execute($sql);
			if(!$rs){
				throw new excepcion_toba("BARRA INFERIOR editor item: NO se pudo cargar definicion: $this->contexto['elemento']. - [SQL]  $sql - [ERROR] " . $db["instancia"][apex_db_con]->ErrorMsg() );
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
												'admin',"/admin/items/propiedades",
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
		echo "</td></tr>";	
 	}

}
?>