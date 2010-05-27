SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('sede_edificio', 'id_edificio'), (SELECT max(id_edificio) FROM sede_edificio), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('institucion', 'id_institucion'), (SELECT max(id_institucion) FROM institucion), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('sede', 'id_sede'), (SELECT max(id_sede) FROM sede), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('ua_tipo', 'id_ua_tipo'),(SELECT max(id_ua_tipo) FROM ua_tipo), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('ua', 'id_ua'), (SELECT max(id_ua) FROM ua), true);
