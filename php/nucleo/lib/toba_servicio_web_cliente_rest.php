<?php
require_once(toba::nucleo()->toba_dir(). '/php/3ros/guzzle/autoload.php');

class toba_servicio_web_cliente_rest extends toba_servicio_web_cliente
{
	protected $guzzle;
	
	function __construct($opciones, $id_servicio, $proyecto = null) 
	{
        parent::__construct($opciones, $id_servicio, $proyecto);
	}
	
	/**
	 * @return Guzzle\Service\Client
	 */
	function guzzle()
	{
		if (! isset($this->guzzle)) {
			$this->guzzle = new Guzzle\Service\Client($this->opciones['to']);

            //https://github.com/guzzle/guzzle/issues/120
            $options = $this->guzzle->getConfig()->get('curl.options');
            $options['body_as_string'] = TRUE;
            $this->guzzle->getConfig()->set('curl.options', $options);

			if (isset($this->opciones['auth_tipo'])) {
				$this->guzzle->setDefaultOption('auth', 
						array(	$this->opciones['auth_usuario'], 
								$this->opciones['auth_password'], 
								$this->opciones['auth_tipo']));
			}		
		}
		return $this->guzzle;	
	}

}
