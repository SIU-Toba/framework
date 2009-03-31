<?php

class pantalla_dos_columnas extends toba_ei_pantalla
{
	protected function generar_layout()
	{
		echo "
			<style type='text/css'>
				.ei-form-base {
					border:none;
				}
			</style>";
		echo "<table>";
		$i = 0;
		foreach ($this->get_dependencias() as $dep) {
			$ultimo = ($i == $this->get_cantidad_dependencias());
			if ($i % 2 == 0) {
				echo "<tr>";
			}
			echo "<td>";
			$dep->generar_html();
			echo "</td>";
			$i++;
			if ($i % 2 == 0 || $ultimo) {
				echo "</tr>";
			}
		}
		echo "</table>";
	}
}

?>
