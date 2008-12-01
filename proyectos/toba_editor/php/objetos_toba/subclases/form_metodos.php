<?php
 
class form_metodos extends toba_ei_formulario
{
	function extender_objeto_js()
	{
		echo "
			function cambiar_grupo(id_grupo, estado) {
				var form = {$this->objeto_js};
				var efs = form.efs();
				for (var i in efs) {
					if (i.indexOf(id_grupo) != -1) {
						efs[i].chequear(estado);
					}	
				}				
			}
		";
	}
}

?>