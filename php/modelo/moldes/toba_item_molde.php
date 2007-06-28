<?php

class toba_item_molde extends toba_molde_elemento
{
	protected $clase = 'toba_item';
	protected $ci = null;						// CI base
	protected $cn = null;						// CN base

	function ini()
	{
	}
	
	//----------------------------------------------------
	//-- API CONSTRUCCION
	//----------------------------------------------------

	function ci()
	{
		if(!isset($this->ci)) $this->ci = new toba_ci_molde($this->asistente);
		return $this->ci;
	}
	
	function cn()
	{
		if(!isset($this->cn)) $this->cn = new toba_cn_molde($this->asistente);
		return $this->cn;
	}
	
	function set_carpeta_item($id)
	{
	}

	//---------------------------------------------------
	//-- GENERAR
	//---------------------------------------------------

	function generar()
	{
		//Abrir transaccion
		if(isset($this->ci)) {
			$this->ci->generar();	
		}
		if(isset($this->cn)) {
			$this->cn->generar();	
		}
		//Asociar CI y CN
		parent::generar();
	}
}
?>