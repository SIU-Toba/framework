<?php
class agrego_tabla_relacion  extends tester_caso
{
	protected $sql = array(
		"INSERT INTO apex_objeto_dependencias (proyecto,dep_id,objeto_consumidor,objeto_proveedor,identificador,orden) VALUES ('ejemplo','33000035','33000068','33000069','ua','2');",
		"INSERT INTO apex_objeto_datos_rel_asoc (proyecto,objeto,asoc_id,padre_proyecto,padre_objeto,padre_id,hijo_proyecto,hijo_objeto,hijo_id,orden) VALUES ('ejemplo','33000068','33000009','ejemplo','33000067','institucion','ejemplo','33000069','ua','1');",
		"INSERT INTO apex_objeto_rel_columnas_asoc (proyecto,objeto,asoc_id,padre_objeto,padre_clave,hijo_objeto,hijo_clave) VALUES ('ejemplo','33000068','33000009','33000067','33000067','33000069','33000072');",
		"INSERT INTO apex_objeto (proyecto,objeto,clase_proyecto,clase,nombre,fuente_datos_proyecto,fuente_datos,creacion) VALUES ('ejemplo','33000069','toba','toba_datos_tabla','ua','ejemplo','ejemplo','2011-07-04 16:01:14');",
		"INSERT INTO apex_objeto_db_registros (objeto_proyecto,objeto,ap,tabla,fuente_datos_proyecto,fuente_datos,permite_actualizacion_automatica) VALUES ('ejemplo','33000069','1','ua','ejemplo','ejemplo','1');",
		"INSERT INTO apex_objeto_db_registros_col (objeto_proyecto,objeto,col_id,columna,tipo,pk,secuencia,no_nulo_db) VALUES ('ejemplo','33000069','33000071','id_ua','E','1','soe_unidadesacad_unidadacad_seq','1');",
		"INSERT INTO apex_objeto_db_registros_col (objeto_proyecto,objeto,col_id,columna,tipo,pk,no_nulo_db) VALUES ('ejemplo','33000069','33000072','id_institucion','E','0','0');",
		"INSERT INTO apex_objeto_db_registros_col (objeto_proyecto,objeto,col_id,columna,tipo,pk,largo,no_nulo_db) VALUES ('ejemplo','33000069','33000073','nombre','C','0','255','1');",
		"INSERT INTO apex_objeto_db_registros_col (objeto_proyecto,objeto,col_id,columna,tipo,pk,no_nulo_db) VALUES ('ejemplo','33000069','33000074','id_ua_tipo','E','0','0');"
		);


	/**
	 * Descripción del caso de test
	 */
	function get_descripcion()
	{
		return 'Agrego un datos_tabla a una relacion pre-existente';
	}
}
?>
