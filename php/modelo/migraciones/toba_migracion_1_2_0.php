<?php

class toba_migracion_1_2_0 extends toba_migracion
{
	/*
		Cambios en el codigo del nucleo:
			__raiz__
			uso de los valores de las secuencias.
	
	*/
	
	function instancia__preparar_modelo_para_mysql()
	{
		//1- Cambio los constraints para que la migracion de datos se ejecute en cascada
		$sql[] = 'ALTER  TABLE ONLY apex_item_msg DROP CONSTRAINT  "apex_item_msg_fk_item" CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_nota DROP CONSTRAINT  "apex_item_nota_fk_item" CASCADE;'; 
		$sql[] = 'ALTER  TABLE ONLY apex_molde_operacion DROP CONSTRAINT	"apex_molde_operacion_fk_item" CASCADE;'; 
		$sql[] = 'ALTER  TABLE ONLY apex_item DROP CONSTRAINT	"apex_item_fk_padre"	 CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_info DROP CONSTRAINT	"apex_item_info_fk_item"  CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_item_objeto DROP CONSTRAINT	"apex_item_consumo_obj_fk_item"  CASCADE;';
		$sql[] = 'ALTER  TABLE ONLY apex_usuario_grupo_acc_item DROP CONSTRAINT "apex_usu_item_fk_item" CASCADE;';
		$sql[] = 'ALTER  TABLE apex_item_msg 			ADD CONSTRAINT 	"apex_item_msg_fk_item" FOREIGN KEY ("item", "item_proyecto") REFERENCES "apex_item" ("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_nota 			ADD CONSTRAINT	"apex_item_nota_fk_item" FOREIGN KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_molde_operacion 	ADD CONSTRAINT	"apex_molde_operacion_fk_item" FOREIGN	KEY ("item", "proyecto") REFERENCES	"apex_item"	("item", "proyecto") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item 				ADD CONSTRAINT	"apex_item_fk_padre"	FOREIGN KEY	("padre_proyecto","padre")	REFERENCES "apex_item" ("proyecto","item") ON DELETE NO ACTION	ON	UPDATE CASCADE DEFERRABLE INITIALLY	IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_info 			ADD CONSTRAINT	"apex_item_info_fk_item" FOREIGN	KEY ("item_proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_item_objeto 		ADD CONSTRAINT	"apex_item_consumo_obj_fk_item" FOREIGN KEY ("proyecto","item") REFERENCES	"apex_item"	("proyecto","item") ON DELETE CASCADE ON UPDATE CASCADE	DEFERRABLE	INITIALLY IMMEDIATE;';
		$sql[] = 'ALTER  TABLE apex_usuario_grupo_acc_item ADD CONSTRAINT	"apex_usu_item_fk_item"		FOREIGN KEY	("proyecto","item") REFERENCES "apex_item" ("proyecto","item")	ON	DELETE CASCADE ON UPDATE CASCADE DEFERRABLE INITIALLY IMMEDIATE;';
		$this->elemento->get_db()->ejecutar($sql);
		//2- Elimino la columna 'sql', no permitida como nombre de columna
	}

	function proyecto__migrar_datos_para_mysql()
	{
		/*
			Hacer que el ID de los items sea numerico
		*/
		$sql = "SELECT item FROM apex_item WHERE proyecto = '{$this->elemento->get_id()}'";
		$datos = $this->elemento->get_db()->consultar($sql);
		foreach($datos as $dato) {
			if( (string)(int)$dato['item'] != $dato['item'] ) {
				//-- Cambio en ID del item en la base --
				$id_viejo = $dato['item'];
				$id_nuevo = $this->elemento->get_db()->recuperar_nuevo_valor_secuencia('apex_item_seq');
				$id_nuevo++;
				$sql = "UPDATE apex_item SET item = '$id_nuevo' WHERE item = '$id_viejo';";
				echo "$sql \n";
				$this->elemento->get_db()->ejecutar($sql);
				//-- Cambio el ID del item en el codigo
				
			}
		}
		
		/*
			Renumerar las que poseen 0
			* completar las que no tienen registros con un registro fantasma
				o hacer que las secuencias comiencen si o si desde 1...
		*/
		foreach( toba_db_secuencias::get_lista() as $secuencia => $tabla) {
			$sql = "SELECT '$secuencia' as seq, min({$tabla['campo']}) as minimo FROM {$tabla['tabla']}\n";
			//echo $sql;
			$datos = $this->elemento->get_db()->consultar_fila($sql);
			if($datos['minimo'] === 0){
				echo "TABLA: {$tabla['tabla']} CAMPO {$tabla['campo']}\n";
			}
		}
		//throw new toba_error('no');
	}
	
	/**
	 * El ei_filtro pasa a ser un formulario con un par de campos seteados
	 */
	function proyecto__migrar_filtro_viejo()
	{
		//-- Todos los actuales filtros, se les pone un par de flags en el form.
		$sql = "
			UPDATE apex_objeto_ut_formulario SET no_imprimir_efs_sin_estado = 1, resaltar_efs_con_estado = 1
			FROM  apex_objeto
			WHERE 
					apex_objeto_ut_formulario.objeto_ut_formulario			= apex_objeto.objeto
				AND apex_objeto_ut_formulario.objeto_ut_formulario_proyecto = apex_objeto.proyecto
				AND apex_objeto.clase='toba_ei_filtro' 
				AND apex_objeto_ut_formulario.objeto_ut_formulario_proyecto = '{$this->elemento->get_id()}'
		";
		$cant = $this->elemento->get_db()->ejecutar($sql);
		
		//-- Se cambia la clase
		$sql = "UPDATE apex_objeto SET clase='toba_ei_formulario' WHERE clase='toba_ei_filtro' AND proyecto = '{$this->elemento->get_id()}'";
		$cant += $this->elemento->get_db()->ejecutar($sql);

		//-- Se cambian las extensiones de código
		$editor = new toba_editor_archivos();
		$editor->agregar_sustitucion("|extends\s*toba_ei_filtro|" ,"extends toba_ei_formulario");          
		$archivos = toba_manejador_archivos::get_archivos_directorio( $this->elemento->get_dir(), '|.php|', true);
		$editor->procesar_archivos($archivos);		
		
		return $cant;
	}
	
	/**
	 * Los campos sinc_orden_automatico y sinc_susp_constraints no se estaban leyendo desde los metadatos
	 * Por lo tanto por si algun proyecto destildo/tildo alguno de estos campos, se va a cambiar el estado actual
	 * al comportamiento que tenian las versiones anteriores (el primero en '1' y el segundo en '0')
	 */
	function proyecto__datos_relacion_bug_campos()
	{
		$sql = "UPDATE apex_objeto_datos_rel 
				SET sinc_orden_automatico=1
					sinc_susp_constraints=0
				WHERE
					proyecto = '{$this->elemento->get_id()}'
		";
		return $this->elemento->get_db()->ejecutar($sql);
	}
	
}

?>