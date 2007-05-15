<?php

class toba_migracion_1_0_3 extends toba_migracion
{
	function instancia__creacion_skins()
	{
		$sql[] = "INSERT INTO apex_estilo (estilo,descripcion) VALUES ('cubos','cubos');";
		$sql[] = "
			CREATE TABLE apex_objeto_db_registros_ext
			(
				objeto_proyecto    			   	varchar(15)		NOT NULL,
				objeto 		                	int4       		NOT NULL,
				externa_id						int4			NOT NULL, 
				tipo							varchar(3)		NOT NULL,
				sincro_continua					smallint		NULL,
				metodo							varchar(100)	NULL,
				clase							varchar(100)	NULL,
				include							varchar(255)	NULL,
				sql								varchar			NULL
			);

			CREATE TABLE apex_objeto_db_registros_ext_col
			(
				objeto_proyecto    			   	varchar(15)		NOT NULL,
				objeto 		                	int4       		NOT NULL,
				externa_id						int4			NOT NULL,
				col_id							int4			NOT NULL,
				es_resultado					smallint		NULL
			);
		";
		$this->elemento->get_db()->ejecutar($sql);
	}	
	
	
	function proyecto__estilo_filtro()
	{
		$cant = 0;
		$sql = "
			UPDATE apex_objeto_eventos SET 
				estilo = 'ei-boton-filtrar',
				imagen_recurso_origen = 'apex',
				imagen = 'filtrar.png'
			WHERE
				proyecto = '{$this->elemento->get_id()}' AND
				identificador = 'filtrar'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);
		
		//--- Actualiza el Cancelar
		$sql = "
			UPDATE apex_objeto_eventos
			SET 
				estilo = 'ei-boton-limpiar',
				imagen_recurso_origen = 'apex',
				imagen = 'limpiar.png',
				etiqueta = '&Limpiar'
			FROM
				apex_objeto as obj
			WHERE
				obj.proyecto = '{$this->elemento->get_id()}' AND
				obj.clase = 'objeto_ei_filtro' AND
				obj.proyecto = apex_objeto_eventos.proyecto AND
				obj.objeto = apex_objeto_eventos.objeto AND
				apex_objeto_eventos.identificador = 'cancelar'
		";
		$cant += $this->elemento->get_db()->ejecutar($sql);
		return $cant;
	}
	
	
	function proyecto__skins()
	{
		$sql = "
			UPDATE apex_proyecto
				SET estilo = 'cubos'
				WHERE 
					proyecto='{$this->elemento->get_id()}' AND
					estilo = 'toba'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}
}	


?>