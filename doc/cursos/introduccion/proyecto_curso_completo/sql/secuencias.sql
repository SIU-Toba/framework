SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_edificios', 'edificio'), (SELECT max(edificio) FROM soe_edificios), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_instituciones', 'institucion'), (SELECT max(institucion) FROM soe_instituciones), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_sedes', 'sede'), (SELECT max(sede) FROM soe_sedes), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_tiposua', 'tipoua'),(SELECT max(tipoua) FROM soe_tiposua), true);
SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('soe_unidadesacad', 'unidadacad'), (SELECT max(unidadacad) FROM soe_unidadesacad), true);
