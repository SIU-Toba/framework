<?php
require_once('pantalla_perfil_datos.php');

class pantalla_api_alto_nivel extends pantalla_perfil_datos
{
	function generar_layout()
	{
		echo "<pre>";
		foreach($this->sql as $s) {
			echo "SQL: $s<br>";
			$sql2 = toba::perfil_de_datos()->filtrar($s);
			echo "sql 2: $sql2<br><hr>";
		}
		echo "</pre>";
	}
}
?>