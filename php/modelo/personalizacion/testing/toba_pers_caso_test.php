<?php
/**
 * De esta clase tienen que heredar todos aquellos que pretendan ejecutar un test
 * de personalización.
 */
abstract class toba_pers_caso_test
{
	/**
	 * @var toba_db_postgres7
	 */
	protected $db;
	protected $sql = array();

	function  __construct($db)
	{
		$this->db = $db;
	}

	function get_db()
	{
		return $this->db;
	}

	abstract function get_descripcion();

	function ejecutar()
	{
		foreach ($this->sql as $sentencia) {
			try {
				$this->db->ejecutar($sentencia);
			} catch (toba_error $e) {
				throw  new toba_error("Error cargando los datos de la personalizacion. El sql ejecutado fue: $sentencia");
			}
		}
	}
}
?>
