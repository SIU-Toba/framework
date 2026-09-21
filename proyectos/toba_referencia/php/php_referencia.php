<?php

class php_referencia
{
    private static $instancia;
    protected $archivos = array();
    protected $expandido = false;

    /**
     * @return php_referencia
     */
    public static function instancia()
    {
        if (!isset(self::$instancia)) {
            self::$instancia = new php_referencia();
        }
        return self::$instancia;
    }

    public function agregar($archivo)
    {
        $this->archivos[] = $archivo;
    }

    public function set_expandido($expandido)
    {
        $this->expandido = $expandido;
    }

    public function mostrar()
    {
        if (! empty($this->archivos)) {
            $escapador = toba::escaper();
            echo '<div id="php-referencia-cont">
			Extensiones utilizadas:
			<ul>';
            foreach ($this->archivos as $i => $archivo) {
                echo '<li><a href="'. $escapador->escapeHtmlAttr('#archivo_'.$i).'" title="Ver extensión" onclick="toggle_nodo($$(\''. $escapador->escapeHtmlAttr('archivo_'.$i).'\'));">'.
                        $escapador->escapeHtml(basename($archivo))."</a></li>";
            }
            echo "</ul></div>";
            echo "<div id='archivos'>";
            $oculto = ($this->expandido) ? "" : "style='display:none'";
            foreach ($this->archivos as $i => $archivo) {
                echo "<div id='". $escapador->escapeHtmlAttr("archivo_$i")."' class='php-referencia' $oculto>";
                echo '<strong>'. $escapador->escapeHtml($archivo).'</strong>:<br /><br />';
                highlight_file($archivo);
                echo "</div>";
            }
            echo "</div>";
        }
    }
}
