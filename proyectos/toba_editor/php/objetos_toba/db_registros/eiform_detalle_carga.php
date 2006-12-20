<?php 
class eiform_detalle_carga extends toba_ei_formulario
{

	function extender_objeto_js()
	{
		echo "
			{$this->objeto_js}.evt__estatico__procesar = function(inicial) {
				var cheq = this.ef('estatico').chequeado();
				this.ef('include').mostrar(cheq, true);
				this.ef('clase').mostrar(cheq, true);
			}

		";
	}	
	
}

?>