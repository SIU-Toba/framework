<?php
  class ctrl_materia extends toba_control
  {
    function ejecutar(&$parametros)
    {
       $this->set_resultado(false);
        $this->set_mensaje('Ocurrió un error con ' . get_class($this));
     }  
  }

?>