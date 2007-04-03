<?php 
php_referencia::instancia()->agregar(__FILE__);
class extension_ml extends toba_ei_formulario_ml
{
	
	/**
	 * Se redefine el encabezado para no incluir los nombres de las columnas
	 */
	function generar_formulario_encabezado()
	{
		
	}
	
	function generar_layout_fila()
	{
		$this->set_ancho_etiqueta('65px');
		$columnas = 2;
		$i = 0;
		foreach ($this->lista_ef_post as $ef) {
			$ultimo = ($i == count($this->lista_ef_post));			
			if ($i % $columnas == 0) {
				echo "<td colspan='$columnas' class='{$this->estilo_celda_actual}'>";
			}			
			$this->generar_html_ef($ef);
			$i++;			
			if ($i % $columnas == 0 || $ultimo) {
				echo "</td>";	
			}			
		}
	}
}

?>