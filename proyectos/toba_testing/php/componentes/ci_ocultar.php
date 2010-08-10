<?php 
class ci_ocultar extends toba_testing_pers_ci
{

	function conf__arbol()
	{
		
	}
	
	function conf__archivos(toba_ei_archivos $archivos)
	{
		$archivos->set_path_absoluto(dirname(__FILE__));
	}	
	
	function conf__esquema(toba_ei_esquema $esquema) 
	{
		$esquema->set_datos("digraph G {Hello->World}");	
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$cuadro->set_datos(array(array('clave' => 1)));		
	}
	
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		{$this->objeto_js}.evt__colapsar = function()
		{
			for (dep in this._deps) {
				this._deps[dep].cambiar_colapsado();
			}
			return false;
		}
				
		
		{$this->objeto_js}.evt__ocultar = function()
		{
			for (dep in this._deps) {
				this._deps[dep].ocultar();
			}
			return false;
		}
		
		{$this->objeto_js}.evt__mostrar = function()
		{
			for (dep in this._deps) {
				this._deps[dep].mostrar();
			}		
			return false;
		}
		";
	}
}

?>