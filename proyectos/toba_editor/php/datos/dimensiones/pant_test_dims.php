<?php 
class pant_test_dims extends toba_ei_pantalla
{
	function generar_layout()
	{
		$this->dep('form_elegir_sql')->generar_html();
		echo '<hr />';
		$this->dep('form_test')->generar_html();
		echo '<hr />';
		$this->mostrar_resultado_pruebas();
	}
	
	function mostrar_resultado_pruebas()
	{
		$pruebas = $this->controlador->get_pruebas_realizadas();
		if($pruebas) {
			echo "<div style='padding: 5px; overflow: auto; height: 400px; width: 600px; text-align: left; background-color: rgb(255, 255, 255); font-size: 11px;'>";
			echo $this->controlador->get_cabecera_prueba();
			foreach($pruebas as $id => $prueba) {
				echo '<hr />';
				echo 'SQL ' . ($id + 1);
				echo '<hr />';
				ei_arbol($prueba);
			}
			echo "</div>";
		}	
	}
}

?>