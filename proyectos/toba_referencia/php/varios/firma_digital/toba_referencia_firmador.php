<?php
$firmador = toba_dir().'/www/firmador_pdf/firmador_pdf.php';
if (!file_exists($firmador)) {
	return;
}
require_once($firmador);

$firmador = null;

function get_firmador()
{
	global $firmador;
	if ($firmador == null) {
		$firmador = new firmador_pdf(new toba_referencia_firmador());
		$firmador->set_guardar_sesion_en_db(toba::db()->get_pdo());
	}
	return $firmador;
}


?>
