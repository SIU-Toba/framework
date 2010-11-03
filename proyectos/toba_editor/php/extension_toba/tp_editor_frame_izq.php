<?php 

class tp_editor_frame_izq extends toba_tp_basico
{
	
	protected function estilos_css()
	{
		parent::estilos_css();
		echo "
		<style type='text/css'>
			.ei-barra-sup-sin-tit .ei-botonera {
				background-color: white;
			}
			.ei-barra-sup-sin-tit {
				margin-bottom: auto;
			}			
			form {
				background-color: white;
				padding-top: 5px;
			}			
		</style>			
		";
	}	
}

?>

