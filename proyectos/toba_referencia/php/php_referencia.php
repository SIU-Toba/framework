<?php

class php_referencia
{
	private static $instancia;
	protected $archivos = array();
	
	/**
	 * @return php_referencia
	 */
	function instancia()
	{
		if (!isset(self::$instancia)) {
			self::$instancia = new php_referencia();	
		}
		return self::$instancia;
	}
	
	function agregar($archivo)
	{
		$this->archivos[] = $archivo;	
	}
	
	function mostrar()
	{
		if (! empty($this->archivos)) {
			echo '<div id="php-referencia-cont">
			Extensiones utilizadas:
			<ul>';
			foreach ($this->archivos as $i => $archivo) {
				echo '<li><a href="#archivo_'.$i.'" title="Ver extensión" onclick="toggle_nodo($(\'archivo_'.$i.'\'));">'.
						basename($archivo)."</a></li>";
			}
			echo "</ul></div>";
			echo "<div id='archivos'>";
			foreach ($this->archivos as $i => $archivo) {
				echo "<div id='archivo_$i' class='php-referencia' style='display:none'>";
				echo "<strong>$archivo</strong>:<br><br>";
				highlight_file($archivo);
				echo "</div>";
			}
			echo "</div>";
		}
	}
}

?>