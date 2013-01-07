<?php

class php_referencia
{
	private static $instancia;
	protected $archivos = array();
	protected $expandido = false;
	
	/**
	 * @return php_referencia
	 */
	static function instancia()
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
	
	function set_expandido($expandido)
	{
		$this->expandido = $expandido;
	}
	
	function mostrar()
	{
		if (! empty($this->archivos)) {
			echo '<div id="php-referencia-cont">
			Extensiones utilizadas:
			<ul>';
			foreach ($this->archivos as $i => $archivo) {
				echo '<li><a href="#archivo_'.$i.'" title="Ver extensi�n" onclick="toggle_nodo($$(\'archivo_'.$i.'\'));">'.
						basename($archivo)."</a></li>";
			}
			echo "</ul></div>";
			echo "<div id='archivos'>";
			$oculto = ($this->expandido) ? "" : "style='display:none'";
			foreach ($this->archivos as $i => $archivo) {
				echo "<div id='archivo_$i' class='php-referencia' $oculto>";
				echo "<strong>$archivo</strong>:<br /><br />";
				highlight_file($archivo);
				echo "</div>";
			}
			echo "</div>";
		}
	}
}

?>