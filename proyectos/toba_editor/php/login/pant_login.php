<?php
class pant_login extends toba_ei_pantalla
{	
	function generar_html()
	{
		// si se da un timeout, esta pagina puede cargarse en un frame...
		// esta funcion detecta este caso y lo soluciona
		$codigo_js = "
			if(self.name!=top.name)	{
				top.location.href='". toba::escaper()->escapeJs($_SERVER['PHP_SELF'])."';
			}
		";
		echo toba_js::ejecutar($codigo_js);
		echo "
			<style type='text/css'>
			.ci-barra-sup {
				-moz-border-radius:6px 6px 0 0;
				border-radius:6px 6px 0 0;
				-webkit-border-radius:6px 6px 0 0;
				padding: 3px;
					background-image: -webkit-gradient(
				    linear,
				    left top,
				    left bottom,
				    color-stop(0.5, #7485b3),
				    color-stop(0.5, #5368a1)
				);
				background-image: -moz-linear-gradient(
				    center top,
				    #7485b3 50%,
				    #5368a1 50%
				);
				margin-bottom: 3px;
								
								
			}
			.cuerpo {
				border-top: 2px solid black;

			}
			</style>
		";
		parent::generar_html();	
	}	
	
	function generar_layout()
	{
		if ($this->existe_dependencia('seleccion_usuario')) {
			$this->dep('seleccion_usuario')->generar_html();
		}
		echo '<div>';		
		if ($this->existe_dependencia('datos')) {
			echo "<div style='float:left;'>";
			$this->dep('datos')->generar_html();
			echo '</div>';
		} 
		if ($this->existe_dependencia('form_solo_proyecto')) {
			echo "<div style='float:left;'>";
			$this->dep('form_solo_proyecto')->generar_html();
			echo '</div>';
		} 
		
		if ($this->existe_dependencia('openid')) {
			echo "<div style='margin-left: 30px; float:right;'>";			
			$this->dep('openid')->generar_html();
			echo '</div>';
		}
		if ($this->existe_dependencia('cas')) {
			echo "<div style='margin-left: 30px; float:right;'>";			
			$this->dep('cas')->generar_html();
			echo '</div>';
		}		
		echo '</div>';

	}

}

?>