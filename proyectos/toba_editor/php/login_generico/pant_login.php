<?php
class pant_login extends toba_ei_pantalla
{
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
		if ($this->existe_dependencia('openid')) {
			echo "<div style='padding-left: 30px; padding-right: 30px;float:right;'>";				
			$this->dep('openid')->generar_html();
			echo '</div>';
		}
		if ($this->existe_dependencia('cas')) {
			echo "<div style='padding-left: 30px; padding-right: 30px;float:right;'>";		
			$this->dep('cas')->generar_html();
			echo '</div>';
		}		
		echo '</div>';

	}

}

?>