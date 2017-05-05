<?php
require_once('zona_editor.php');

class zona_item extends zona_editor
{
	function cargar_info()
	{	//Carga el EDITABLE que se va a manejar dentro de la ZONA
		//Cuando se cargan explicitamente (generalmente desde el ABM que maneja la EXISTENCIA del EDITABLE)
		//Las claves de los registros que los ABM manejan son asociativas
		$sql = '	SELECT	i.*,
							m.molde as 				molde,
							m.operacion_tipo as 	molde_tipo_operacion,
							o.descripcion_corta as	molde_tipo_operacion_nombre,
							(SELECT COUNT(*) FROM apex_item_objeto WHERE item = i.item) as componentes
					FROM	apex_item i
							LEFT OUTER JOIN apex_molde_operacion m 
								INNER JOIN apex_molde_operacion_tipo o 
									ON m.operacion_tipo = o.operacion_tipo 
								ON i.item = m.item AND i.proyecto = m.proyecto
								
					WHERE	i.proyecto='.quote($this->editable_id[0]).'
					AND		i.item='.quote($this->editable_id[1]).';';
		$rs = toba::db()->consultar($sql);
		if (empty($rs)) {
			throw new toba_error("No se puede encontrar informacion del item {$this->editable_id[0]},{$this->editable_id[1]}");
		} else {
			$this->editable_info = $rs[0];
			return true;
		}	
	}
	
	function generar_html_barra_vinculos()
	{	
		$escapador = toba::escaper();
		if ($this->editable_info['molde'] || $this->editable_info['componentes'] == 0) {
			$vinculo = toba::vinculador()->get_url(toba_editor::get_id(), 1000110, null, array('zona'=>true, 'validar'=>false,'menu'=>1));
			echo '<a href="' . $escapador->escapeHtmlAttr($vinculo) .'">'. toba_recurso::imagen_toba('wizard.png', true, null, null, 'Asistente para la generación de Operaciones');
			if ($this->editable_info['molde']) {
				echo $escapador->escapeHtml($this->editable_info['molde_tipo_operacion_nombre']);
			}
			echo "</a>\n";
		}
		//Acceso al EDITOR PHP
		if ($this->editable_info['actividad_accion'] != '') {
			$componente = $this->get_editable();
			$id = array('proyecto'=>$componente[0], 'componente' =>$componente[1]);
			$info = toba_constructor::get_info($id, 'toba_item');
			// Ir al editor
			$ver = $info->get_utileria_editor_ver_php();
			echo "<a href='" . $escapador->escapeHtmlAttr($ver['vinculo']) ."'>" . toba_recurso::imagen($ver['imagen'], null, null, $ver['ayuda']). "</a>\n";
			// Apertura del archivo
			if (admin_util::existe_archivo_subclase($this->editable_info['actividad_accion'])) {
				$abrir = $info->get_utileria_editor_abrir_php();
				echo '<a href="' . $escapador->escapeHtmlAttr($abrir['vinculo']) .'">'. toba_recurso::imagen($abrir['imagen'], null, null, $abrir['ayuda']). "</a>\n";
			}
		}
		parent::generar_html_barra_vinculos();
	}
	
	function generar_html_barra_inferior()	
	{	//Genera la barra especifica inferior del EDITABLE
		echo '<br>';
		echo "<table width='100%' class='tabla-0'>";
		echo "<tr><td  class='barra-obj-io'>Componentes referenciados</td></tr>";
		echo "<tr><td  class='barra-obj-leve'>";
		$sql = '	SELECT	o.proyecto as				objeto_proyecto,
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
					AND		io.proyecto='.quote($this->editable_id[0]).'
					AND		io.item='.quote($this->editable_id[1]).'
					ORDER BY 4,5,6;';
		$datos = toba::db()->consultar($sql);
		if (! empty($datos)) {
			echo "<table class='tabla-0'>";
			foreach ($datos as $rs) {
				if (!isset($contador[$rs['clase']])) {
					$contador[$rs['clase']] = 0;
				} else {
					$contador[$rs['clase']] += 1;
				}
				echo '<tr>';
				echo "<td  class='barra-obj-link' width='5'>".toba_recurso::imagen_toba($rs['clase_icono'], true).'</td>';
				echo "<td  class='barra-obj-link' >[".toba::escaper()->escapeHtml($rs['objeto'].'] '.$rs['objeto_nombre']).'</td>';
				echo "<td  class='barra-obj-id' width='5'>";
				if (isset($rs['clase_editor'])) {
					echo "<a href='" . toba::vinculador()->get_url($rs['clase_editor_proyecto'],
													$rs['clase_editor'],
													array(apex_hilo_qs_zona=>$rs['objeto_proyecto']
															.apex_qs_separador. $rs['objeto'])) ."'>".
							toba_recurso::imagen_toba('objetos/editar.gif', true, null, null, 'Editar propiedades ESPECIFICAS del OBJETO'). '</a>';
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		} else {
			echo 'La OPERACION no contiene COMPONENTES.';
		}
		echo '</td></tr></table>';
	}
}
?>