<?
	$this->info_definicion();
	$this->info_estado();

	$this->hilo->info();
	
    dump_conexiones();
    dump_SESSION();

	$this->vinculador->info();

//    dump_COLOR();
	$this->hilo->limpiar_memoria();
?>