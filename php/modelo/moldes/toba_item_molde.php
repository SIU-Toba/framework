<?php

/*
*	Falta un arbol para componentes.
*/
class toba_item_molde extends toba_molde_elemento
{
	protected $clase_dr = 'toba_item';
	protected $item;
	protected $ci = null;
	protected $cn = null;

	function __construct()
	{
		parent::__construct();
		$this->datos->tabla('base')->nueva_fila(array());
	}

	//----------------------------------------
	//-- Api ITEM ----------------------------
	//----------------------------------------
	
	function set_nombre($nombre)
	{
		$this->datos->tabla('base')->set_fila_columna_valor(0,'nombre',$nombre);
	}
	
	function set_carpeta_item($id)
	{
	}

	function ci()
	{
		$this->ci_principal	= $this->get_dr('toba_ci');
	}
	
	function cn()
	{
		
	}

	//----------------------------------------
	//------- Guardar -------------------------
	//----------------------------------------

	function generar()
	{
		//Abrir transaccion
		if(isset($this->ci)) {
			$this->ci->generar();	
		}
		if(isset($this->cn)) {
			$this->cn->generar();	
		}
		ei_arbol($this->datos->tabla('base')->get_filas());
	}

	function get_ids_generados()
	{
	}
}
?>