<?php 
class pantalla_perfil_datos extends toba_ei_pantalla
{
	protected $sql = array(		"SELECT * FROM ref_deportes;",
								"SELECT * FROM ref_deportes WHERE id < 5;",
								"SELECT * FROM ref_deportes d, ref_persona_deportes p WHERE p.deporte = d.id;",
								"SELECT * FROM ref_deportes d, ref_persona_deportes WHERE ref_persona_deportes.deporte = d.id;",
								"SELECT * FROM ref_persona_deportes, ref_deportes d WHERE ref_persona_deportes.deporte = d.id;",
								"SELECT * FROM ref_juegos ORDER BY id;",
								"SELECT * FROM ref_deportes WHERE id > 2;",
								"SELECT * FROM ref_persona_deportes GROUP BY *;",
								"SELECT * FROM ref_persona_deportes WHERE persona = 1;",
								"SELECT * FROM ref_persona_juegos;",
								"SELECT * FROM ref_persona_juegos WHERE persona = 1;"
							);
}
?>