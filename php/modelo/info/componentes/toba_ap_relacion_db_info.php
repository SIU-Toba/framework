<?php

class toba_ap_relacion_db_info implements toba_meta_clase
{
    protected $datos;

    public function __construct($datos)
    {
        $this->datos = $datos;
    }

    public function get_nombre_instancia_abreviado()
    {
        return "dr_ap";
    }

    public function set_subclase($nombre, $archivo, $pm)
    {
        $db = toba_contexto_info::get_db();
        $nombre = $db->quote($nombre);
        $archivo = $db->quote($archivo);
        $pm = $db->quote($pm);
        $sql = "
			UPDATE apex_objeto_datos_rel
			SET 
				ap = 3,
				ap_clase = $nombre,
				ap_archivo = $archivo, 
				punto_montaje = $pm
			WHERE
					proyecto = '{$this->datos['proyecto']}'
				AND	objeto = '{$this->datos['objeto']}'
		";
        toba::logger()->debug($sql);
        $db->ejecutar($sql);
    }

    //---------------------------------------------------------------------
    //-- METACLASE
    //---------------------------------------------------------------------

    public function get_molde_subclase()
    {
        $molde = new toba_codigo_clase($this->get_subclase_nombre(), $this->get_clase_nombre());

        //-- Ini
        $doc = 'Se ejecuta al inicio de todos los request en donde participa el componente';
        $metodo = new toba_codigo_metodo_php('ini', array(), array($doc));
        $metodo->set_doc($doc);
        $molde->agregar($metodo);

        //-- Pre Sinc
        $doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a sincronizar";
        $comentarios = array(
            $doc,
            "La transacción con la bd ya fue iniciada (si es que hay)"
        );
        $metodo = new toba_codigo_metodo_php('evt__pre_sincronizacion', array(), $comentarios);
        $metodo->set_doc($doc);
        $molde->agregar($metodo);

        //-- Post Sinc
        $doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos posteriores a la sincronización";
        $comentarios = array(
            $doc,
            "La transacción con la bd aún no fue terminada (si es que hay)"
        );
        $metodo = new toba_codigo_metodo_php('evt__post_sincronizacion', array(), $comentarios);
        $metodo->set_doc($doc);
        $molde->agregar($metodo);

        //-- Pre Elim
        $doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos previo a la eliminación";
        $comentarios = array(
            $doc,
            "La transacción con la bd ya fue iniciada (si es que hay)"
        );
        $metodo =  new toba_codigo_metodo_php('evt__pre_eliminacion', array(), $comentarios);
        $metodo->set_doc($doc);
        $molde->agregar($metodo);

        //-- Post Elim
        $doc = "Ventana para incluír validaciones (disparar una excepcion) o disparar procesos posteriores a la eliminación";
        $comentarios = array(
            $doc,
            "La transacción con la bd ya fue iniciada (si es que hay)"
        );
        $metodo = new toba_codigo_metodo_php('evt__post_eliminacion', array(), $comentarios);
        $metodo->set_doc($doc);
        $molde->agregar($metodo);
        return $molde;
    }

    public function get_clase_nombre()
    {
        return 'toba_ap_relacion_db';
    }

    public function get_clase_archivo()
    {
        return 'nucleo/componentes/persistencia/toba_ap_relacion_db.php';
    }

    public function get_punto_montaje()
    {
        return $this->datos['punto_montaje'];
    }

    public function get_subclase_nombre()
    {
        return $this->datos['ap_clase'];
    }

    public function get_subclase_archivo()
    {
        return $this->datos['ap_archivo'];
    }

    //---------------------------------------------------------------------

    public function get_descripcion_subcomponente()
    {
        return 'Administrador de Persistencia';
    }
}
