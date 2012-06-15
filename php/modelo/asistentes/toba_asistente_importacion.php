<?php

class toba_asistente_importacion extends toba_asistente
{
	
	function posee_informacion_completa()
	{
		return true;
		/*$datos_molde = $this->dr_molde->tabla('molde')->get();	
		if( isset($datos_molde['carpeta_archivos']) && isset($datos_molde['prefijo_clases']) ) {
			return true;	
		}
		return false;*/
	}	
		
	
	protected function generar_base()
	{
	}	
	
	function generar()
	{
		
	}

	protected function generar_elementos($id_item)
	{
		//--- Carga el item acual
		$item = new toba_item_molde($this);
		$item->cargar($id_item);
					
		//--- Clona el ci actual 
		$datos_destino = $this->dr_molde->tabla('molde')->get();
		$datos_origen = $this->dr_molde->tabla('base')->get();
		$id_origen = array();
		$id_origen['proyecto'] = $datos_origen['origen_proyecto'];
		$id_origen['componente'] = $datos_origen['origen_item'];
		$item_origen = toba_constructor::get_info($id_origen, 'toba_item');

		$opciones = array();
		$opciones['proyecto'] = $this->id_molde_proyecto;
		$opciones['fuente_datos'] = $datos_destino['fuente'];
		$opciones['fuente_datos_proyecto'] = $this->id_molde_proyecto;
		if (isset($datos_destino['punto_montaje'])) {
			$opciones['punto_montaje'] = $datos_destino['punto_montaje'];
			$item->set_punto_montaje($datos_destino['punto_montaje']);		
		}
		foreach ($item_origen->get_hijos() as $hijo) {	
			$id_nuevo_hijo = $hijo->clonar($opciones, $datos_destino['carpeta_archivos'], false);
			$item->asociar_objeto($id_nuevo_hijo['componente']);
		}
		
		//-- Genera el item
		$item->generar();
		$this->generar_archivos_consultas();
		$this->guardar_log_elementos_generados();
	}	

}

?>