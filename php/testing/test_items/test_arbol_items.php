<?php
require_once('nucleo/lib/arbol_items.php');

class test_arbol_items extends test_toba
{

	function get_descripcion()
	{
		return "Manejo del árbol de ítems";
	}	

	function sentencias_restauracion()
	{
		$sentencias[] = "DELETE FROM apex_usuario_grupo_acc_item 
						WHERE proyecto='toba_testing' AND usuario_grupo_acc='prueba_asignacion'";
		return $sentencias;
	}

	function asegurar_unicidad($items)
	{
		$ids = array();
		foreach ($items as $item)
		{
			if (in_array($item->id(), $ids))
				$this->fail('El conjunto de items contiene items repetidos');
			else
				$ids[] = $item->id();
		}
	}	
	//---------------------------------------------------------------------	
	function test_recorrido_rama_inexistente()
	/*
		Intenta recorrer el arbol a partir de una rama que no existe
	*/
	{
		
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->set_carpeta_inicial('/cualquieraa');
		$arbol->ordenar();
		$this->assertEqual($arbol->cantidad_items(), 0 ,'La rama no existe (%s)');
		$this->asegurar_unicidad($arbol->items());		
	}
	//---------------------------------------------------------------------	
	function test_recorrido_rama_sin_hijos()
	/*
		Recorre una hoja, debe encontrar sólo a sí mimsmo
	*/
	{
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->set_carpeta_inicial('/pruebas_arbol_items/rama_vacia');
		$arbol->ordenar();
		$this->assertEqual($arbol->cantidad_items(), 1 ,'La rama no tiene hijos (%s)');
		$this->asegurar_unicidad($arbol->items());		
	}

	//---------------------------------------------------------------------	
	function test_recorrido_cubre_todo_el_arbol()
	/*
		Recorre una rama de varios niveles.
		Se busca el caso que haya un id no jerarquico, niveles sin items y profundidad variada
	*/
	{

		$cant_niveles = 5;
		$niveles = array(
					array('/pruebas_arbol_items/rama_profunda', 0),
					array('/pruebas_arbol_items/rama_profunda/ia', 1),
					array('/pruebas_arbol_items/rama_profunda/r0', 1),
					array('identificador_no_jerarquico', 2),
					array('/pruebas_arbol_items/rama_profunda/r0/i0b', 2),
					array('/pruebas_arbol_items/rama_profunda/r0/r01', 2),
					array('/pruebas_arbol_items/rama_profunda/r0/r02', 2),
					array('/pruebas_arbol_items/rama_profunda/r0/r02/r021', 3),
					array('/pruebas_arbol_items/rama_profunda/r0/r02/r022', 3),
					array('/pruebas_arbol_items/rama_profunda/r0/r02/r021/i021a', 4),
					array('/pruebas_arbol_items/rama_profunda/r1', 1)
			);
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->set_carpeta_inicial('/pruebas_arbol_items/rama_profunda');
		$arbol->ordenar();
		foreach ($niveles as $nivel) {
			$encontrado = false;
			foreach ($arbol->items() as $item) {
				if ($item->id() == $nivel[0]) {
					$encontrado = true;
					$this->AssertEqual($item->nivel() , $nivel[1], "Nivel del item {$item->id()} (%s)");
					break;
				}
			}
			if (!$encontrado)
				$this->fail("El item {$nivel[0]} no se encuentra");
		}
		$this->assertEqual($arbol->cantidad_items(), count($niveles) ,'Cant. Items del arbol (%s)');
		$this->assertEqual($arbol->profundidad(), $cant_niveles, 'Profundidad del arbol (%s)');
		$this->asegurar_unicidad($arbol->items());		
	}
	//---------------------------------------------------------------------		
	function test_recorrido_con_filtrado_items()
	/*
		Filtra los items publicos y recorre una rama. Posteriormente se deja solos los items que puede acceder Admin
	*/
	{
		$cant_niveles = 5;
		$niveles = array(
					array('/pruebas_arbol_items/rama_profunda/ia', 1),
					array('/pruebas_arbol_items/rama_profunda/r0/r01', 2),
					array('/pruebas_arbol_items/rama_profunda/r0/r02/r021', 3),
					array('/pruebas_arbol_items/rama_profunda/r0/r02/r021/i021a', 4),
			);
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->set_carpeta_inicial('/pruebas_arbol_items/rama_profunda');
		$arbol->ordenar();
		$arbol->dejar_grupo_acceso('admin');
		foreach ($niveles as $nivel) {
			$encontrado = false;
			foreach ($arbol->items() as $item) {
				if ($item->id() == $nivel[0]) {
					$encontrado = true;
					$this->AssertEqual($item->nivel() , $nivel[1], "Nivel del item {$item->id()} (%s)");
					break;
				}
			}
			if (!$encontrado)
				$this->fail("El item {$nivel[0]} no se encuentra");
		}
		$this->assertEqual($arbol->cantidad_items(), count($niveles) ,'Cant. Items del arbol (%s)');		
		$this->assertEqual($arbol->profundidad(), $cant_niveles, 'Profundidad del arbol (%s)');	
		$this->asegurar_unicidad($arbol->items());		
	}
	//---------------------------------------------------------------------		
	function test_arbol_denegar_permisos()
	/*
		Deniega los permisos de un grupo a todo el arbol
	*/
	{
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->cambiar_permisos(array(), 'prueba_asignacion');
		
		//Chequeo
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->ordenar();
		foreach ($arbol->items() as $item)
		{
			$this->assertFalse($item->grupo_tiene_permiso('prueba_asignacion'));
		}
		$this->asegurar_unicidad($arbol->items());
	}
	
	//---------------------------------------------------------------------	
	function test_rama_otorgar_permisos_item_profundo()
	/*
		Otorga permisos sólo a un item profundo en el arbol, esto debe provocar un otorgamiento a todo el camino de carpetas
		que lo contienen.
	*/
	{
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->cambiar_permisos(array('/pruebas_arbol_items/rama_profunda/r0/r02/r021/i021a'), 'prueba_asignacion');
		
		//Chequeo
		$items_buscados = array(
					'',
					'/pruebas_arbol_items',
					'/pruebas_arbol_items/rama_profunda',
					'/pruebas_arbol_items/rama_profunda/r0',
					'/pruebas_arbol_items/rama_profunda/r0/r02',
					'/pruebas_arbol_items/rama_profunda/r0/r02/r021',
					'/pruebas_arbol_items/rama_profunda/r0/r02/r021/i021a'
			);
		$arbol = new arbol_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->ordenar();
		$arbol->dejar_grupo_acceso('prueba_asignacion');
		$this->assertEqual($arbol->cantidad_items(), count($items_buscados));
		foreach ($arbol->items() as $item)
		{
			$this->assertTrue(in_array($item->id(), $items_buscados), 'Las carpetas del camino deben tener permisos');
		}
		$this->asegurar_unicidad($arbol->items());
	}	
	
}


?>