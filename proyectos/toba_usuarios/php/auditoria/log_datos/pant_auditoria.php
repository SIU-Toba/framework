<?php 
class pant_auditoria extends toba_ei_pantalla
{
	function generar_layout()
	{
		$existe_previo = 0;
		foreach ($this->_dependencias as $dep) {
			$dep->generar_html();	
			if (! $existe_previo) {
				echo "<hr/><br><br>\n";
			}			
			$existe_previo = 1;
		}
	}
}

?>