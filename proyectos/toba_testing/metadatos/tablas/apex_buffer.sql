INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba_testing', '45', 'CI + CN', 'CI + CN', '$cn = $this->cargar_objeto(\"objeto_cn_t\", 0); 
    $ci = $this->cargar_objeto(\"objeto_ci_me_tab\", 0); 

    //$this->objetos[$cn]->cargar_datos($editable); 
     
    $this->objetos[$ci]->asignar_controlador_negocio( $this->objetos[$cn] ); 

    $this->objetos[$ci]->procesar(); 
    $this->objetos[$ci]->obtener_html();', NULL);
INSERT INTO apex_buffer (proyecto, buffer, descripcion_corta, descripcion, cuerpo, archivo_origen) VALUES ('toba_testing', '48', 'Ei Cuadro', 'Ei Cuadro', '$cuadro = $this->cargar_objeto(\"objeto_ei_cuadro\", 0);
$this->objetos[$cuadro]->cargar_datos(array(
          array(\'id\' => 0, \'descripcion\' => \'Cero\'),
          array(\'id\' => 1, \'descripcion\' => \'Uno\')));
$this->objetos[$cuadro]->obtener_html();', NULL);
