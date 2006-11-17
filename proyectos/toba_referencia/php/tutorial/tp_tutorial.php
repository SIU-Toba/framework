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
			.screenshot {
				display:block;
				margin: 10px 0px 10px 50px;
			}
			.screenshot img {
				border: 1px outset gray;			
			}
			.tutorial-agenda {
				margin-left: 20%;
				margin-right: 20%;				
				display: block;
			}
			.tutorial-agenda li {
				padding-top: 10px;
			}
			.tutorial-agenda a {
				font-size: 20px;			
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