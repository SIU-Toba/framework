<?php 
class ei_form_filtro_usuarios extends toba_ei_filtro
{

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__pertenencia__procesar = function(es_inicial)
		{
			var opcion = this.ef('pertenencia').valor();
			if (opcion == 'T' || opcion == 'S') {
				this.ef('proyecto').ocultar();
			}else{
				this.ef('proyecto').mostrar();
			}
		}
		";
	}
}

?>