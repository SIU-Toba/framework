<?php 
class pantalla_api_bajo_nivel extends toba_ei_pantalla
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
			$tablas = toba::perfil_de_datos()->buscar_tablas_gatillo_en_sql($s);
			ei_arbol($tablas);
			echo "<br>";
		}
		echo "</pre>";

	}
}

?>