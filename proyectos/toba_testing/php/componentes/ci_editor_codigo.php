<?php
class ci_editor_codigo extends toba_testing_pers_ci
{
	//-----------------------------------------------------------------------------------
	//---- codigo -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__codigo(toba_ei_codigo $codigo)
	{
		$codigo->set_datos("
			
			class pepe extends pepe_abs {
				function conf__pepe(xxx pepe) {
					pepe->set_datos('dato');
				}
			}
		");
	}

}
?>