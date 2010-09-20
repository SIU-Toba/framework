<?php
php_referencia::instancia()->agregar(__FILE__);

class ap_persona_juegos extends toba_ap_tabla_db_s
{
	/*
		Cuando una persona da de alta un JUEGO, se los anota en la oferta de juegos!
		El comportamiento con la modificacion y eliminacion esta con el CASCADE de la FK.
	*/
	protected function evt__post_insert($id)
	{
		/*
		$juego = $this->datos[$id]['juego'];
		$jugador = $this->datos[$id]['persona'];
		$sql = "INSERT INTO ref_juegos_oferta (juego,jugador) VALUES ($juego, $jugador);";
		ejecutar_sql($sql);*/
	}
}
?>