<?php
require_once('comando_toba.php');
//require_once('modelo/personalizacion/personalizacion.php');

class comando_personalizacion extends comando_toba
{
    protected static $schema_original;
    
    static function get_info()
	{
		return 'Administracion de PERSONALIZACIONES';
	}


//    function opcion__test()
//    {
//		$p = $this->get_proyecto();
//		$p->get_pms()->migrar_proyecto();
////		define('apex_pa_proyecto', $p->get_id());
////		toba::puntos_montaje($p)->usar_punto_montaje_proyecto();
//    }

    /**
     * Prepara un proyecto para comenzar a ser personalizado
     */
    function opcion__comenzar()
    {
		$this->consola->titulo('Preparando personalizacion');
        $p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
        $pers->iniciar();
        $this->consola->titulo('Personalizacion preparada');
    }

    /**
     * Exporta la personalización realizada
     */
    function opcion__exportar()
    {
        $this->consola->titulo('Exportando la personalizacion');
        $p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
        $pers->exportar();
        $this->consola->titulo('Exportacion terminada');
    }

	/**
	 * Chequeo de conflictos. Ejecute este comando antes de importar la personalización
	 */
	function opcion__conflictos()
    {
		$p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
		$pers->chequear_conflictos();
    }

    /**
     * Importa la personalización que está en el directorio personalización del
     * proyecto
     */
    function opcion__importar()
    {
        $this->consola->titulo('Aplicando la personalizacion');
        $p = $this->get_proyecto();
		$pers = new toba_personalizacion($p, $this->consola);
        $pers->aplicar();
        $this->consola->titulo('Aplicacion terminada');
    }
}
?>
