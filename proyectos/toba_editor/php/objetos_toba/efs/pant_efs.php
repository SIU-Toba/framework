<?php 
class pant_efs extends toba_ei_pantalla
{
	
	protected function generar_layout()
	{
		$primero = true;
		foreach ($this->_dependencias as $dep) {
			$dep->generar_html();	
			if ($primero) {
				echo '<br>';
				echo '<div id="editor-ef">';				
			}
			$primero = false;
		}
		echo '</div>';
	}	
}

?>