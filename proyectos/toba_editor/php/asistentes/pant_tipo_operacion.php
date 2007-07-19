<?php 

class pant_tipo_operacion extends toba_ei_pantalla
{
	function generar_layout()
	{
		$tipos = toba_info_editores::get_lista_tipo_molde();
		$tipos = rs_convertir_asociativo_matriz($tipos, array('operacion_tipo'), array('descripcion'));
		echo toba_js::abrir();
		echo "var tipos_operacion = ".toba_js::arreglo($tipos, true, true)."\n";
		echo toba_js::cerrar();
		$this->dep('form_tipo_operacion')->generar_html();
		echo "<hr><div style='text-align: center; padding-bottom: 100px; padding-top:100px; font-weight:bold;color:gray'>
				Vista previa en GIF animado o FLASH<br>
				Como para dar una idea del flujo de la operación	
			</div>";	
		echo "<hr><div id='operacion_descripcion' style='text-align: center; padding-bottom: 5px; padding-top:5px'>
			</div>";			
	}
	
}


?>