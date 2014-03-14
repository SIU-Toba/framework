<?php

class toba_modelo_rest extends toba_modelo_elemento
{
	protected $proyecto;

	
	function __construct(toba_modelo_proyecto $proyecto)
	{		
		$this->proyecto = $proyecto;		
		$this->db = $this->proyecto->get_db();
	}

    /**
     * @param toba_modelo_proyecto $proyecto
     * @return toba_ini
     */
    static function get_ini_server(toba_modelo_proyecto  $proyecto)
    {
        $directorio = $proyecto->get_dir_instalacion_proyecto(). "/rest";
		$ini = new toba_ini($directorio.'/servidor.ini');			
        return $ini;
    }

    /**
     * @param toba_modelo_proyecto $proyecto
     * @return toba_ini
     */
    static function get_ini_usuarios(toba_modelo_proyecto  $proyecto)
    {
        $directorio = $proyecto->get_dir_instalacion_proyecto(). "/rest";
		$ini = new toba_ini($directorio.'/servidor_usuarios.ini');			
        return $ini;
    }


    /**
     * @param toba_modelo_proyecto $proyecto
     * @param $id_servicio
     * @return toba_ini
     */
    static function get_ini_cliente(toba_modelo_proyecto  $proyecto, $id_servicio)
    {
        $directorio = $proyecto->get_dir_instalacion_proyecto(). "/rest/$id_servicio";
		$ini = new toba_ini($directorio. '/cliente.ini');			
        return $ini;
    }

	//------------------------------------------------------------------------------------------------------------------//

}
?>
