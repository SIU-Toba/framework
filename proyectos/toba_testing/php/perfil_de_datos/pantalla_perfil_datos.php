<?php 

/*
-- PK1 directa
SELECT persona, p.descripcion
FROM persona_extra p,
dependencia d
WHERE p.dependencia = d.dependencia;

SELECT persona, descripcion FROM persona_extra;

-- PK1 indirecta 

SELECT p.persona, p.descripcion
FROM persona p, persona_extra pp
WHERE p.persona = pp.persona;

SELECT persona, descripcion FROM persona;

-- PK2 directa
SELECT categoria_1, categoria_2, c.descripcion
FROM escalafon e,
categoria c
WHERE c.escalafon_1 = e.escalafon_1
AND c.escalafon_2 = e.escalafon_2;

SELECT categoria_1, categoria_2 FROM categoria;

-- PK2 indirecta

SELECT c.cargo, c.descripcion
FROM cargo c, categoria cc
WHERE c.categoria_1 = cc.categoria_1
AND c.categoria_2 = cc.categoria_2;


*/

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