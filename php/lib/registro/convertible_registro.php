<?php

/**
 * Si una clase implementa esta interfaz se dice que es convertible a toba_registro
 */
interface convertible_registro
{
    /**
     * @param toba_db_postgres7 $db
     * @return toba_registro_insert
     */
    public function get_como_insert($db);

    /**
     * @param toba_db_postgres7 $db
     * @return toba_registro_update
     */
    public function get_como_update($db);

    /**
     * @param toba_db_postgres7 $db
     * @return toba_registro_delete
     */
    public function get_como_delete($db);
}
