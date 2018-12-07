<?php 

use SIU\InterfacesManejadorSalidaToba\IFactory;

class referencia_factory implements IFactory{
	function getProvider(){
		return 'referencia';
	}
	
	function getPaginaBasica(){		
		return 'referencia_tp_basico';
	}
	
	function getPaginaTitulo(){
		return null;
	}
	
	function getPaginaNormal(){
		return null;
	}
	
	function getPaginaPopup(){
		return null;
	}
	
	function getPaginaLogon(){
		return null;
	}
	
	function getMenu(){
		return null;
	}
	
	function getElementoInterfaz(){
		return 'referencia_ei';
	}
	
	function getPantalla(){
		return 'referencia_pantalla';
	}
	
	function getCuadro(){
		return 'referencia_cuadro';
	}
	
	function getCuadroSalidaHtml(){
		return 'referencia_cuadro_salida_html';
	}
	
	function getFiltro(){
		return null;
	}
	
	function getFormulario(){
		return 'referencia_formulario';
	}
	
	function getFormularioMl(){
		return 'referencia_formulario_ml';
	}
	
	function getEventoUsuario(){
		return 'referencia_evento_usuario';
	}
	
	function getEventoTab(){
		return 'referencia_evento_tab';
	}
	
	function getInputsForm(){
		return null;
	}
	
	function getFiltroColumnas(){
		return null;
	}
}
