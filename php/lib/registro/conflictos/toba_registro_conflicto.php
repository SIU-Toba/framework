<?php

abstract class toba_registro_conflicto
{
    /**
     * Conflicto irresoluble
     */
    public const fatal = 'fatal';

    /**
     * Conflicto resoluble
     */
    public const warning = 'warning';

    protected $tipo;

    protected $numero;
    /**
     * @var toba_registro
     */
    protected $registro;

    protected $descripcion_componente;

    public function __construct($registro)
    {
        $this->registro = $registro;
    }

    public function get_tipo()
    {
        return $this->tipo;
    }

    public function get_numero()
    {
        return $this->numero;
    }

    public function set_descripcion_componente($desc)
    {
        $this->descripcion_componente = $desc;
    }

    abstract public function get_descripcion();
}
