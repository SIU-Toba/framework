<?php
  class ctrl_comision extends toba_control
  {
    function ejecutar(&$parametros)
    {
       $this->set_resultado(false);
        $this->set_mensaje('Ocurri� un error con ' . get_class($this));
     }  
  }

?>