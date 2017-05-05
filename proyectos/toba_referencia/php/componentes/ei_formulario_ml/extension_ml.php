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
	
	protected function generar_layout_fila($clave_fila)
	{
		$this->set_ancho_etiqueta('65px');
		$columnas = 2;
		$i = 0;
		foreach ($this->get_nombres_ef() as $ef) {
			$ultimo = ($i == $this->get_cantidad_efs());			
			if ($i % $columnas == 0) {
				echo "<td colspan='$columnas' class='". toba::escaper()->escapeHtmlAttr($this->estilo_celda_actual)."'>";
			}			
			$this->generar_html_ef($ef);
			$i++;			
			if ($i % $columnas == 0 || $ultimo) {
				echo '</td>';
			}			
		}
	}
}

?>