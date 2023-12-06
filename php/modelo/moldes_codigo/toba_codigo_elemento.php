<?php
/**
 * @ignore
 */
abstract class toba_codigo_elemento
{
    protected $nombre;
    protected $identacion=0;
    protected $caracteres_tab = 4;
    protected $grupo = null;


    /**
     * Permite indicar que un elemento del codigo pertenece a un grupo dado (por ejemplo tal dependencia del ci)
     */
    public function set_grupo($grupo)
    {
        $this->grupo = $grupo;
    }

    public function get_grupo()
    {
        return $this->grupo;
    }

    public function get_nombre()
    {
        return $this->nombre;
    }

    public function identar($nivel)
    {
        $this->identacion += $nivel;
    }

    public function identado()
    {
        return str_repeat("\t", $this->identacion);
    }

    public function get_caracteres_identacion()
    {
        return $this->identacion * $this->caracteres_tab;
    }

    abstract public function get_codigo();
}
