<?php
/**
 * @ignore
 */
class toba_codigo_propiedad_php extends toba_codigo_elemento
{
	protected $tipo;
	protected $nombre;
	protected $comentarios;
	
	function __construct($nombre, $tipo, $comentarios=null)
	{
		$this->nombre = $nombre;
		$this->tipo =$tipo;
		$this->comentarios = $comentarios;
	}

	function get_codigo()
	{
		$prop = $this->identado() . "$this->tipo $this->nombre";
		if(isset($this->comentarios)){
			$prop .= ";// $this->comentarios" . "\n";
		}else{
			$prop .= ";" . "\n";
		}
		return $prop;
	}
}
?>