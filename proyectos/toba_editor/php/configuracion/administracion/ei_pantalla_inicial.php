<?php
class ei_pantalla_inicial extends toba_ei_pantalla
{
	function generar_layout()
	{
		$escapador = toba::escaper();
		$comando_ejecutado = $this->controlador()->get_comando_en_ejecucion();
		$log = nl2br($this->controlador()->get_log_comando_ejecucion());
		echo "<style type='text/css'>
						.div-consola {
							font-family:Arial;
							font-size:9px;
							height:430px;
							padding: 2px;
							border: 1px solid;
							overflow-x:hidden;
							overflow-y:scroll;
							background: white;
						}
					</style>";
		parent::generar_layout();
		echo "<fieldset> <legend> Comando Ejecutado</legend>". $escapador->escapeHtml($comando_ejecutado)."</fieldset><br>";
		echo '<fieldset><legend>Salida de Consola: </legend>';
		echo "<div class='div-consola'>". $escapador->escapeHtml($log)."</div></fieldset>";
	}

}

?>