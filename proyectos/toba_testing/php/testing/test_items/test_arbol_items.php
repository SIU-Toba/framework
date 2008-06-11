<?php

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
		
		$arbol = new toba_catalogo_items(false, 'toba_testing');
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
		$arbol = new toba_catalogo_items(false, 'toba_testing');
		$arbol->set_carpeta_inicial(1000213);
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
					array(1000203, 0),
					array(1000204, 1),
					array(1000205, 1),
					array(1000200, 2),
					array(1000206, 2),
					array(1000207, 2),
					array(1000208, 2),
					array(1000209, 3),
					array(1000211, 3),
					array(1000210, 4),
					array(1000212, 1)
			);
		$arbol = new toba_catalogo_items(false, 'toba_testing');
		$arbol->set_carpeta_inicial(1000203);
		$arbol->ordenar();
		foreach ($niveles as $nivel) {
			$encontrado = false;
			foreach ($arbol->items() as $item) {
				if ($item->id() == $nivel[0]) {
					$encontrado = true;
					$this->AssertEqual($item->get_nivel_prof() , $nivel[1], "Nivel del item {$item->id()} (%s)");
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
					array(1000204, 1),
					array(1000207, 2),
					array(1000209, 3),
					array(1000210, 4),
			);
		$arbol = new toba_catalogo_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->set_carpeta_inicial(1000203);
		$arbol->ordenar();
		$arbol->dejar_grupo_acceso('admin');
		foreach ($niveles as $nivel) {
			$encontrado = false;
			foreach ($arbol->items() as $item) {
				if ($item->id() == $nivel[0]) {
					$encontrado = true;
					$this->AssertEqual($item->get_nivel_prof() , $nivel[1], "Nivel del item {$item->id()} (%s)");
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
		$arbol = new toba_catalogo_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->cambiar_permisos(array(), 'prueba_asignacion');
		
		//Chequeo
		$arbol = new toba_catalogo_items(false, 'toba_testing');
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
		$arbol = new toba_catalogo_items(false, 'toba_testing');
		$arbol->sacar_publicos();
		$arbol->cambiar_permisos(array(1000210), 'prueba_asignacion');
		
		//Chequeo
		$items_buscados = array(
					'',
					1000202,
					1000203,
					1000205,
					1000208,
					1000209,
					1000210
			);
		$arbol = new toba_catalogo_items(false, 'toba_testing');
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