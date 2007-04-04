<?php
  class ctrl_requisitos extends toba_control
  {
    function ejecutar(&$parametros)
    {
      $this->set_resultado(false);
      $this->set_mensaje('Ocurrió un error con ' . get_class($this) . ' parametros: ' .  print_r($parametros,true));

      toba::logger()->info('SOY ' . get_class($this) . ' !!!' . print_r($parametros,true));    
      return true;
    }  
  }

?>