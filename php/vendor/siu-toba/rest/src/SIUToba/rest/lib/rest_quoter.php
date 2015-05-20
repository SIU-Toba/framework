<?php

namespace SIUToba\rest\lib;

interface rest_quoter
{
    /**
     * Quotea un parametro (con la conexion a la bd?) Se utiliza en helpers que crean sql.
     */
    public function quote($dato);
}
