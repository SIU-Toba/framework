<?php 
class pant_test_dims extends toba_ei_pantalla
{
	function generar_layout()
	{
		if ($this->existe_dependencia('form_elegir_sql')) {
			$this->dep('form_elegir_sql')->generar_html();
			echo '<hr />';			
			$this->dep('form_test')->generar_html();
			echo '<hr />';
			$this->mostrar_resultado_pruebas();
		}
	}
	
	function mostrar_resultado_pruebas()
	{
		$pruebas = $this->controlador->get_pruebas_realizadas();
		if ($pruebas) {
			echo "<div style='padding: 5px; overflow: auto; height: 400px; width: 600px; text-align: left; background-color: rgb(255, 255, 255); font-size: 11px;'>";
			//Cabecera
			$cabecera = $this->controlador->get_cabecera_prueba();
			echo '<h1>' . $cabecera['perfil_nombre'] . '</h1>';

			$this->lista($cabecera['dimensiones_restringidas'], 'DIMESIONES restringidas para el perfil');
			echo '<br>';
			$this->lista($cabecera['gatillos_activos'], 'GATILLOS activos');
			echo '<br>';
			
			// Pruebas
			foreach ($pruebas as $id => $prueba) {
				echo '<hr />';
				echo '<h1>SQL ' . ($id + 1);
				echo '</h1><hr />';
				//ei_arbol($prueba);
				//-- SQL original
				if (isset($prueba['sql_original'])) {
					echo "<div style='white-space: pre; padding: 5px; width: 95%; text-align: left; background-color: rgb(255, 251, 0)'>";
					echo "<strong>SQL original</strong>\n\n";
					echo $prueba['sql_original'];
					echo '</div>';
				}
				//-- SQL modificado
				if (isset($prueba['sql_modificado'])) {
					echo "<div style='white-space: pre; padding: 5px; width: 95%; text-align: left; background-color: rgb(255, 220, 0)'>";
					echo "<strong>SQL Resultante</strong>\n\n";
					echo $prueba['sql_modificado'];
					echo '</div>';
				}
				//-- Analisis
				if (isset($prueba['gatillos']) || isset($prueba['dimensiones']) || isset($prueba['where'])) {
					echo '<br>';
					echo "<div style='width: 95%; padding: 5px; text-align: left; background-color: rgb(200, 200, 200)'>";
					if (isset($prueba['gatillos'])) {
						$this->lista($prueba['gatillos'], 'GATILLOS reconocidos');
						echo '<br>';
					}
					if (isset($prueba['dimensiones'])) {
						$this->lista($prueba['dimensiones'], 'DIMESIONES reconocidas');
						echo '<br>';
					}
					if (isset($prueba['where'])) {
						echo "<div style='white-space: pre; padding: 5px; text-align: left; background-color: rgb(255, 220, 0)'>";
						echo "<strong>WHERE GENERADO</strong>\n\n";
						foreach ($prueba['where'] as $where) {
							echo $where . "\n";	
						}
						echo '</div>';
					}
					echo '</div>';
				}
				//-- CONTAR filas QUERY
				if (isset($prueba['query_filas_orig']) || isset($prueba['query_datos_orig'])) {
					echo '<br>';
					echo "<div style='width: 95%; padding: 5px; text-align: left; background-color: rgb(200, 200, 200)'>";
					if (isset($prueba['query_filas_orig'])) {
						echo "<div style='font-size: 14px; color: white; padding: 2px; width: 350px; text-align: center; background-color: rgb(255, 0, 0)'>";
						echo 'Filas Originales: <strong>' . $prueba['query_filas_orig'] . '</strong>';
						echo ' - Filas Resultantes: <strong>' . $prueba['query_filas_modif'] . '</strong>';
						echo '</div><br>';
	
					}
					//-- QUERY
					if (isset($prueba['query_datos_orig'])) {
						$this->tabla($prueba['query_datos_orig'], 'QUERY Original');
						echo '<br>';
						$this->tabla($prueba['query_datos_modif'], 'QUERY Resultante');
					}
					echo '</div>';
				}
			}
			echo '</div>';
		}	
	}
	
	function lista($valores, $titulo)
	{
		echo "<div style=' color:black'>\n";
		echo "<table style='BORDER-COLLAPSE: collapse;
							empty-cells: show; 
							background-color: white;
							border: 2px solid black;
							color:black;
							'>\n";
		if ($titulo) {
			echo "<tr>\n";
			echo "<td style='font-size: 12px; color: black; text-align: center; font-weight:bold;' colspan='2'>$titulo</td>\n";
			echo "</tr>\n";
		}
		//Cuerpo
		if ($valores) {
			$estilo_titulo = 'border: 1px solid red; color:red; background-color: yellow; text-align: center;';
			foreach ($valores as $id => $valor) {
				echo "</tr>\n";
				echo "<td style='$estilo_titulo'>$id</td>\n";
				echo "<td style='border: 1px solid gray; padding: 2px;' >$valor</td>\n";
				echo "</tr>\n";
			}
		} else {
			echo "<tr>\n";
			echo "<td style='border: 1px solid gray; padding: 2px;' colspan='".(count($fila) + 1)."'>No hay DATOS!</td>\n";
			echo "</tr>\n";
		}
		echo '</table>';
		echo '</div>';		
	}

	function tabla($tabla, $titulo=null)
	{
		$fila = (current($tabla));
		echo "<div style=' color:black'>\n";
		echo "<table style='BORDER-COLLAPSE: collapse;
							empty-cells: show; 
							background-color: white;
							border: 2px solid black;
							color:black;
							'>\n";
		if ($titulo) {
			echo "<tr>\n";
			echo "<td style='font-size: 12px; color: black; text-align: center; font-weight:bold;' colspan='".(count($fila) + 1)."'>$titulo</td>\n";
			echo "</tr>\n";
		}
		//Cuerpo
		if ($tabla) {
			echo "<tr>\n";
			echo "<td></td>\n";
			//Titulos de columnas
			$estilo_titulo = 'border: 1px solid red; color:red; background-color: yellow; text-align: center;';
			foreach (array_keys($fila) as $titulo) {
				echo "<td style='$estilo_titulo'>$titulo</td>\n";
			}
			echo "</tr>\n";
			//Filas
			foreach ($tabla as $id => $fila) {
				echo "</tr>\n";
				echo "<td style='$estilo_titulo'>$id</td>\n";
				foreach ($fila as $valor) {
					echo "<td style='border: 1px solid gray; padding: 2px;' >$valor</td>\n";
				}
				echo "</tr>\n";
	
			}
		} else {
			echo "<tr>\n";
			echo "<td style='border: 1px solid gray; padding: 2px;' colspan='".(count($fila) + 1)."'>No hay DATOS!</td>\n";
			echo "</tr>\n";
		}
		echo '</table>';
		echo '</div>';		
	}
}
?>