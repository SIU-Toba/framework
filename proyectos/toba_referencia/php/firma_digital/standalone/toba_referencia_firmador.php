<?php
$firmador = toba::proyecto()->get_path().'/www/firmador_pdf/firmador_pdf.php';
if (!file_exists($firmador)) {
	return;
}
require_once($firmador);

$firmador = null;

function get_firmador()
{
	global $firmador;
	if ($firmador == null) {
		$firmador = new firmador_pdf();
		$firmador->set_guardar_sesion_en_php();
	}
	return $firmador;
}


?>
