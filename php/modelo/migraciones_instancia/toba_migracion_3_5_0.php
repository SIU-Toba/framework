<?php

class toba_migracion_3_5_0 extends toba_migracion
{
    public function instancia__cambios_estructura()
    {
        /**
         * Cambia el schema a los logs
         */
        $schema_logs = $this->elemento->get_db()->get_schema() . '_logs';

        /**
        * Se evita el mensaje 'ERROR:  cannot ALTER TABLE "apex_objeto" because
        * it has pending trigger events' de postgres 8.3
        */
        $sql = 'SET CONSTRAINTS ALL IMMEDIATE;';
        $this->elemento->get_db()->ejecutar($sql);
        $sql = array();

        $sql[] = 'CREATE INDEX IF NOT EXISTS idx_apex_msg_indice ON apex_msg (trim(indice), proyecto);';
        
        $this->elemento->get_db()->ejecutar($sql);

        $sql = 'SET CONSTRAINTS ALL DEFERRED;';
        $this->elemento->get_db()->ejecutar($sql);
    }
}
