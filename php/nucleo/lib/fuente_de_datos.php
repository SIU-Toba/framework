<?

class fuente_de_datos
{
	protected $definicion;
	protected $db;
	
	function __construct($definicion)
	{
		$this->definicion = $definicion;
	}
	
	function get_db()
	{
		if (!isset($this->db)) {
			$this->pre_conectar();
			$this->db = dba::get_db($this->definicion['instancia_id']);
			//$this->db->set_fuente_de_datos($this);
			$this->post_conectar();
		}
		return $this->db;
	}
	
	/**
	*	Lugar para personalizar las acciones previas a la conexión
	*/
	function pre_conectar() {}
	
	/**
	*	Lugar para personalizar las acciones posteriores a la conexión
	*/
	function post_conectar() {}
}
?>