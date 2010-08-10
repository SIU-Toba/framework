<?php

class toba_registro_conflicto_constraints extends toba_registro_conflicto
{
	/**
	 * @var toba_error_db
	 */
	protected $db_error;
	
	function  __construct($registro, $db_error)
	{
		parent::__construct($registro);
		$this->tipo = toba_registro_conflicto::fatal;
		$this->numero = 2;
		$this->db_error = $db_error;
	}

	function get_descripcion()
	{
		$tabla = $this->registro->get_tabla();
		$sql_conflictivo = $this->registro->to_sql();
		$sql_state = $this->db_error->get_sqlstate();
		$mensaje = $this->db_error->get_mensaje_motor();
		if ($sql_state == 'db_23505') {
			return "[F:$this->numero] Error de constraints en la tabla $tabla. Hay un error de unique keys. Postgres dijo: $mensaje. El sql conflictivo es: $sql_conflictivo";
		} else {
			return "[F:$this->numero] Error de constraints en la tabla $tabla. El error no fue reconocido por el importador. Postgres dijo: $mensaje. El sql conflictivo es: $sql_conflictivo";
		}
	}
}

?>
