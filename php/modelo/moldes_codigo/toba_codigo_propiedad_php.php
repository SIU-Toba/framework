<?php
/**
 * @ignore
 */
class toba_codigo_propiedad_php extends toba_codigo_elemento
{
	protected $tipo;
	protected $nombre;
	protected $comentarios;
	protected $valor_defecto;
	
	function __construct($nombre, $tipo, $comentarios=null, $valor_defecto = null)
	{
		$this->nombre = $nombre;
		$this->tipo =$tipo;
		$this->comentarios = $comentarios;
		$this->valor_defecto = $valor_defecto;
	}

	function get_codigo()
	{
		$prop = $this->identado() . "$this->tipo $this->nombre";
		if (!is_null($this->valor_defecto)) {
			$prop .= " = $this->valor_defecto";
		}
		if(isset($this->comentarios) && !empty($this->comentarios)) {
			$prop .= ";// $this->comentarios" . "\n";
		}else{
			$prop .= ";" . "\n";
		}
		return $prop;
	}
}
?>