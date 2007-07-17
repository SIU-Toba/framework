<?php 

class pant_crear extends toba_ei_pantalla
{
	function generar_layout()
	{
		echo "<style type='text/css'>
				.ei-form-base {
					border:none;
				}
			</style>
		";
		
		$this->dep('form_tipo_plan')->generar_html();
		echo "<hr><div style='text-align: center; padding-bottom: 100px; padding-top:100px; font-weight:bold;color:gray'>
				Vista previa en GIF animado o FLASH<br>
				Como para dar una idea del flujo de la operación	
			</div>";	
		echo "<hr><div style='text-align: center; padding-bottom: 5px; padding-top:5px;color:gray'>
			Explicación contextual del tipo de operación
			</div>";			
	}
	
}


?>