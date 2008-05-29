<?php

class pantalla_api_alto_nivel extends toba_ei_pantalla
{
	function generar_layout()
	{
		$sql[] = "SELECT * FROM ref_deportes;";
		$sql[] = "SELECT * FROM ref_deportes WHERE id < 5;";
		$sql[] = "SELECT * FROM ref_deportes d, ref_persona_deportes p WHERE p.deporte = d.id;";
		$sql[] = "SELECT * FROM ref_deportes d, ref_persona_deportes WHERE ref_persona_deportes.deporte = d.id;";
		$sql[] = "SELECT * FROM ref_juegos ORDER BY id;";
		$sql[] = "SELECT * FROM ref_deportes WHERE id > 2;";
		$sql[] = "SELECT * FROM ref_persona_deportes GROUP BY *;";
		$sql[] = "SELECT * FROM ref_persona_deportes WHERE persona = 1;";
		$sql[] = "SELECT * FROM ref_persona_juegos;";
		$sql[] = "SELECT * FROM ref_persona_juegos WHERE persona = 1;";

		echo "<pre>";
		foreach($sql as $s) {
			echo "SQL: $s<br>";
			$sql2 = toba::perfil_de_datos()->filtrar($s);
			echo "<hr>sql 2: $sql2<br><hr>";
		}
		echo "</pre>";

	}
}
?>