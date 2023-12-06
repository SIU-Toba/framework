<?php
class ci_listado extends toba_ci
{
    protected $datos = [ 'columna' => 'No se que va aca', 'leyenda' => 'Etiqueta'];
    
    function ini()
    {
        $params = toba::memoria()->get_parametro('id_cuadro');
        if (isset($params)) {
            //Como estamos forzando la navegacion via un link en lugar de un evento, hay que forzar esta regeneracion del token
            // de lo contrario se produce un error al recuperar el mismo, ya que cambia el espacio de memoria sincronizada y no se recupera correctamente
            // osea, usa los eventos!! en lugar de un link
            toba::memoria()->fijar_csrf_token(true);        
            $this->set_pantalla("pant_form");
        }
    }
    
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
        $this->set_pantalla('pant_inicial');
	}

	function evt__cancelar()
	{
        //$this->datos = null;
        $this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
        $id = $cuadro->get_id();
        $vinculo = toba::vinculador()->get_url(null, null, [ 'id_cuadro' => $id[1]]);
        //toba::logger()->debug('El vinculo es ' . $vinculo);
        if (isset($this->datos)) {
            $this->datos['vinculo'] = '<a href="'. $vinculo . '"> Diaguitame</a>';
            $this->datos['id_cuadro'] = $id[1];
            $this->datos['otro_vinculo'] = 'Mamito';
            $cuadro->set_datos([$this->datos]);
        }
	}

    function evt__cuadro__seleccion($datos) 
    {
        toba::logger()->debug($datos);
        $this->set_pantalla('pant_form');
    }
    
	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{        
        if (! isset($this->datos)) {
            $this->datos = [ 'columna' => 'No se que va aca', 'leyenda' => 'Etiqueta'];
        }
        
        $form->set_datos($this->datos);
	}

	function evt__formulario__modificacion($datos)
	{
        $this->datos = $datos;
	}

}
?>