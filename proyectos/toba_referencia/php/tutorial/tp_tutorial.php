<?php
require_once("tp_referencia.php");

class tp_tutorial extends tp_referencia 
{

	protected function estilos_css()
	{
		parent::estilos_css();
		?>
		<style type="text/css">
			.ci-cuerpo, .ci-wiz-cont {
				border: none;
				background-color: white;
				font-size: 12px;
			}
		</style>			
		<?php
	}	
	
	function titulo_item()
	{
		return 'Tutorial';	
	}
	
}
?>