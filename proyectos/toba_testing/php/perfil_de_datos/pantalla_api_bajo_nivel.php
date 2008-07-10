<?php 
require_once('pantalla_perfil_datos.php');

class pantalla_api_bajo_nivel extends pantalla_perfil_datos
{
	function generar_layout()
	{
		$fuente = toba::proyecto()->get_parametro('fuente_datos');
		echo "<pre>";
		foreach($this->sql as $s) {
			echo "SQL: $s<br>";
			$tablas_gatillo = toba::perfil_de_datos()->buscar_tablas_gatillo_en_sql($s, $fuente);
			ei_arbol($tablas_gatillo,'Gatillos encontrados');
			$dimensiones = toba::perfil_de_datos()->reconocer_dimensiones_implicadas( array_keys($tablas_gatillo), $fuente );
			ei_arbol($dimensiones,'Dimensiones implicadas');

			$where = array();
			foreach( $dimensiones as $dimension => $tabla ) {
				//-- 2 -- Obtengo la porcion de WHERE perteneciente a cada gatillo
				$alias_tabla = $tablas_gatillo[$tabla];
				$where[] = toba::perfil_de_datos()->get_where_dimension_gatillo($fuente, $dimension, $tabla, $alias_tabla);
			}
			ei_arbol($where,'WHERE dim: ');
			echo "<br>";
		}
		echo "</pre>";
	}
}

?>