<?php
include_once('nucleo/browser/interface/ef.php');

class lista_casos
{
	static function get_categorias()
	{
		return array(
					array('id' => 'todas', 'nombre' => '-- Todas --'),
					array('id' => 'administrador', 'nombre' => 'Administrador Toba'),
					array('id' => 'ef', 'nombre' => "Ef's"),
					array('id' => 'items', 'nombre' => 'Items'),
					array('id' => 'objetos', 'nombre' => 'Objetos'),
					array('id' => 'persistencia', 'nombre' => 'Persistencia'),
					array('id' => 'varios', 'nombre' => 'Varios')
				);
	}
	
	static function get_casos($categoria = apex_ef_no_seteado)
	{
		$casos = array(
					//Items
					array('id' => 'test_item', 'nombre' => 'Comportamiento básico del ítem', 'categoria' => 'items'),
					array('id' => 'test_arbol_items', 'nombre' => 'Manejo del árbol de ítems', 'categoria' => 'items'),
					
					//Objetos
					array('id' => 'test_ei_formulario_ml', 'nombre' => 'ei_formulario_ml', 'categoria' => 'objetos'),

					//EF
					array('id' => 'test_editable', 'nombre' => 'EF Editable', 'categoria' => 'ef'),
					array('id' => 'test_editable_numero', 'nombre' => 'EF Número', 'categoria' => 'ef'),
					array('id' => 'test_multi_seleccion', 'nombre' => 'EF Multi-selección', 'categoria' => 'ef'),
					array('id' => 'test_fijos', 'nombre' => 'EF Fijos', 'categoria' => 'ef'),					

					//Persistencia
					array('id' => 'test_db_tablas_cd', 'nombre' => 'DB Tablas Cabecera-Detalle', 'categoria' => 'persistencia'),
					array('id' => 'test_db_registros_s', 'nombre' => 'db_registros SIMPLE', 'categoria' => 'persistencia'),
					array('id' => 'test_db_registros_mt', 'nombre' => 'db_registros MULTI TABLA', 'categoria' => 'persistencia'),
					
					//Administrador
					array('id' => 'test_elemento_toba', 'nombre' => 'Elementos Toba', 'categoria' => 'administrador'),
					array('id' => 'test_reflexion', 'nombre' => 'Reflexión sobre archivos y clases PHP', 'categoria' => 'administrador'),
					
					//Varios
					array('id' =>  'test_parseo_etiquetas', 'nombre' => 'Parseo de etiquetas', 'categoria' => 'varios'), 
				);

		if ($categoria == 'todas' || $categoria == apex_ef_no_seteado)
			return $casos;
		else {
			$casos_selecc = array();
			foreach ($casos as $caso) {
				if ($caso['categoria'] == $categoria) {
					$casos_selecc[] = $caso;
				}
			}
			return $casos_selecc;
		}
	}
}


?>