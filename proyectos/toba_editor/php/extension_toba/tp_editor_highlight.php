<?php 

class tp_editor_highlight extends toba_tp_basico_titulo
{
	
	protected function plantillas_css()
	{
		parent::plantillas_css();
		
		//Envio el CSS de la lib para hacer hightlight de codigo
		$url = toba_recurso::url_proyecto('toba_editor') . '/js/packages/@highlightjs/cdn-assets/styles/github.min.css';
		echo "\n<link href='$url' rel='stylesheet' type='text/css' media='screen'/>\n";
	}
	
	protected function cabecera_html()
	{
		//Obtengo la url para incluir el JS necesario
		$url = toba_recurso::url_proyecto('toba_editor'). '/js/packages/@highlightjs/cdn-assets/highlight.min.js';
		
		echo toba::output()->get('PaginaBasica')->getInicioHtml();
		echo toba::output()->get('PaginaBasica')->getInicioHead($this->titulo_pagina());
		$this->encoding();
		$this->plantillas_css();
		$this->estilos_css();
		
		echo toba_js::incluir($url);			//Lo incluyo antes por una cuestion de visualizacion nomas
		toba_js::cargar_consumos_basicos();

		echo toba::output()->get('PaginaBasica')->getFinHead();
	}
}

?>

