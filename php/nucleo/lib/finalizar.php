<?
	//Implementacion trucha de destructores

	$GLOBALS['objetos_a_finalizar']=null;
	$GLOBALS['objetos_a_finalizar_proximo']=null;
	register_shutdown_function('finalizar_objetos');
	
	function registrar_finalizacion( $objeto )
	{
		$posicion = $GLOBALS['objetos_a_finalizar_proximo'];
		$GLOBALS['objetos_a_finalizar'][$posicion] =& $objeto;
		$GLOBALS['objetos_a_finalizar_proximo']++;
		return $posicion;
	}
	//-------------------------------------------------------------------------------

	function desregistrar_finalizacion($posicion)
	{
		unset($GLOBALS['objetos_a_finalizar'][$posicion]);
	}
	//-------------------------------------------------------------------------------
	
	function finalizar_objetos()
	{
		if(isset($GLOBALS['objetos_a_finalizar']))
		{
			foreach(array_keys($GLOBALS['objetos_a_finalizar']) as $objeto)
			{
				if(is_object($GLOBALS['objetos_a_finalizar'][$objeto])){
					$GLOBALS['objetos_a_finalizar'][$objeto]->finalizar();
				}else{
					echo "ERROR, se registro la finalizacion de un objeto que ya no existe";
				}
			}
		}
		//dump_session();
	}
	//-------------------------------------------------------------------------------

?>