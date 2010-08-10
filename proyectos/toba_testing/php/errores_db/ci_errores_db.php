<?php 
class ci_errores_db extends toba_testing_pers_ci
{
	protected $sentencias =  array(
		//PK
		"INSERT INTO apex_elemento_formulario (elemento_formulario, descripcion, proyecto) VALUES ('ef', 'ef base', 'toba')",
		"UPDATE apex_elemento_formulario SET elemento_formulario = 'ef' WHERE elemento_formulario = 'ef_editable_fecha'",
		"INSERT INTO apex_clase (proyecto, clase, clase_tipo, descripcion, icono, editor_proyecto, editor_item, objeto_dr_proyecto, objeto_dr)
				VALUES ('toba_testing', 'toba_ci', 8, 'desc', 'icono', 'toba_editor', '3286', 'toba_editor', '1507')",
		
		//FK
		"INSERT INTO apex_elemento_formulario (elemento_formulario, descripcion, proyecto) VALUES ('ef_algo', 'ef base', 'toba_blabla')",
		"DELETE FROM apex_elemento_formulario WHERE elemento_formulario = 'ef'",
		"UPDATE apex_elemento_formulario SET elemento_formulario = 'pepe' WHERE elemento_formulario = 'ef_editable_fecha'",
		"UPDATE apex_elemento_formulario SET proyecto = 'pepe' WHERE elemento_formulario = 'ef_editable_fecha'",
		
		//Not null
		"INSERT INTO apex_elemento_formulario (elemento_formulario, proyecto) VALUES ('ef_algo', 'toba')",
		"UPDATE apex_elemento_formulario SET elemento_formulario = NULL WHERE elemento_formulario='ef'",
	
	);
	
	protected $s__datos;
	
	
	function ini()
	{
		//-- Se agregan los comentarios para mejorar los mensajes
		$sql = array();
		$sql[] = "COMMENT ON TABLE apex_clase IS 'Clases de Componentes'";
		$sql[] = "COMMENT ON TABLE apex_objeto_ei_formulario_ef IS 'Campos de formulario'";
		$sql[] = "COMMENT ON TABLE apex_elemento_formulario IS 'Elementos de Formulario'";		
		$sql[] = "COMMENT ON TABLE apex_proyecto IS 'Proyectos'";	
		$sql[] = "COMMENT ON COLUMN apex_clase.clase IS 'Clase'";
		$sql[] = "COMMENT ON COLUMN apex_elemento_formulario.elemento_formulario IS 'identificador'";
		$sql[] = "COMMENT ON COLUMN apex_elemento_formulario.padre IS 'Padre'";
		$sql[] = "COMMENT ON COLUMN apex_elemento_formulario.proyecto IS 'Proyecto'";
		$sql[] = "COMMENT ON COLUMN apex_proyecto.proyecto IS 'Nombre del Proyecto'";
		toba::db()->ejecutar($sql);
	}
	
	
	function conf__cuadro_errores(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos)) {
			$cuadro->set_datos($this->s__datos);
		}
	}
	
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function ejecutar_sentencias()
	{
		$this->s__datos = array();
		foreach ($this->sentencias as $id => $sentencia) {
			try {
				toba::db()->ejecutar($sentencia);
			} catch (toba_error_db $e) {
				$error = array();
				$error['sql'] = $sentencia;
				$error['mensaje_original'] = $e->get_mensaje_motor();
				$error['mensaje_usuario'] = $e->getMessage();
				$this->s__datos[] = $error;
			}
		}
	}
	
	function evt__con_parseo()
	{
		toba::db()->set_parser_errores(new toba_parser_error_db_postgres7(null));
		$this->ejecutar_sentencias();
	}

	function evt__sin_parseo()
	{
		$this->ejecutar_sentencias();
	}
}

?>