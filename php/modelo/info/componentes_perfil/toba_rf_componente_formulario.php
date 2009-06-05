<?php 
class toba_rf_componente_formulario extends toba_rf_componente
{
	function inicializar()
	{
		$this->cargar_efs();
	}	

	function cargar_efs()
	{
		$efs = $this->cargar_datos_efs();
		if(count($efs) > 0) {
			$grupo = new toba_rf_grupo_efs('<b>CAMPOS</b>',$this);
			$e = array();
			foreach($efs as $ef) {
				$e[] = new toba_rf_subcomponente_ef($ef['etiqueta'], $grupo, $ef['id'] , $ef['proyecto'], $this->item, 
														$this->restriccion,
														$ef['no_visible'],
														$ef['no_editable'], $ef['formulario'], $ef['deshabilitar_rest_func']);		
			}
			$grupo->set_hijos($e);
			$this->agregar_hijo($grupo);
		}
	}

	function cargar_datos_efs()
	{
		$item = quote($this->item);
		$restriccion = quote($this->restriccion);
		$componente = quote($this->componente);
		$proyecto = quote($this->proyecto);
		$sql = "SELECT 	ef.objeto_ei_formulario as			formulario,
						ef.objeto_ei_formulario_fila as		id,
						ef.objeto_ei_formulario_proyecto as	proyecto,
						ef.etiqueta as						etiqueta, 
						ef.deshabilitar_rest_func,
						ref.no_visible as					no_visible,
						ref.no_editable as					no_editable
				FROM 	apex_objeto_ei_formulario_ef ef
						LEFT OUTER JOIN apex_restriccion_funcional_ef ref
							ON ef.objeto_ei_formulario_fila = ref.objeto_ei_formulario_fila 
								AND ef.objeto_ei_formulario = ref.objeto_ei_formulario
								AND ef.objeto_ei_formulario_proyecto = ref.proyecto
								AND ref.item = $item
								AND ref.restriccion_funcional = $restriccion
				WHERE	(ef.desactivado <> 1 OR ef.desactivado IS NULL)
				AND		( (ef.obligatorio <> 1 OR ef.obligatorio IS NULL) 
						OR ( ef.obligatorio = 1  AND ef.oculto_relaja_obligatorio = 1 ) )
				AND		ef.objeto_ei_formulario = $componente
				AND		ef.objeto_ei_formulario_proyecto = $proyecto
				ORDER BY ef.orden";
		return toba::db()->consultar($sql);
	}
	
	function sincronizar()
	{
		if($this->no_visible_original != $this->no_visible_actual) {
			if ($this->no_visible_actual == 1) {
				$this->agregar_restriccion();
			}else{
				$this->eliminar_restriccion();
			}
		}
		foreach ($this->get_hijos() as $hijo){
			$hijo->sincronizar();
		}
	}
	
}
?>