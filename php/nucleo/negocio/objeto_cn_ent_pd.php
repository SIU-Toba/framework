<?php
require_once("nucleo/negocio/objeto_cn_ent.php");	//Ancestro de todos los OE

class objeto_cn_ent_pd extends objeto_cn_ent
/*
	Manejo de entidades con sincronizacion directa por request

*/
{
	function __construct($id, $resetear=false)
	{
		parent::__construct($id, $resetear);
	}


	public function ins($datos, $parametros)
	{
		parent::ins($datos, $parametros);
		$this->procesar();
	}
	
	public function del($parametros)
	{
		parent::del($parametros);
		$this->procesar();
	}
	
	public function upd($datos, $parametros)
	{
		parent::upd($datos, $parametros);
		$this->procesar();
	}

	public function seleccionar($id_registro, $parametros)
	{
		$this->cargar($id_registro);
	}
	
	function get_x($parametros)
	{
		$elemento = $parametros[0];
		return $this->entidad->acc_elemento($elemento, "get_x", 0);				
	}
}
?>
