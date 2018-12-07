<?php

use SIU\InterfacesManejadorSalidaToba\Componentes\TiposPagina\IPaginaBasica;

class referencia_tp_basico implements IPaginaBasica{
	
	protected $config;
	
	function __construct(){
		$this->config = require(__DIR__.'/../config/params.php');
	}
	
	public function getInicioHtml(){
		return"<!DOCTYPE html>";
	}
	
	public function getInicioHead($titulo){
		$favicon = \toba_recurso::imagen_proyecto('favicon.ico');
		return"<html>
				<head>
					<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
					<link rel='icon' href='$favicon'  >
					<title>$titulo</title>";
	}
	
	public function getEncoding(){
		return"<meta charset='utf-8'>";
	}
	
	public function getPlantillasCss(){
		$salida = \toba_recurso::link_css_proyecto('toba');
		$files = [ 
				'/siu/manejador_salida_bootstrap/js/bootstrap/css/bootstrap.min.css',
				'/siu/manejador_salida_bootstrap/css/fontawesome/css/fontawesome.min.css',
				'/siu/manejador_salida_bootstrap/css/generic.css',
				'/siu/manejador_salida_bootstrap/css/global.css',
				'/siu/manejador_salida_bootstrap/css/skin_custom.css'
		];
		foreach ($files as $file) {			
			$salida .= "<link rel='stylesheet' href='".\toba_recurso::url_toba().$file."'>";
		}
				
		//Aprovecho agregar los JS que necesito
		$salida .= '<!--[if lt IE 9]>' .
					\toba_js::incluir(\toba_recurso::url_toba().'/siu/manejador_salida_bootstrap/js/html5shiv/html5shiv.min.js') .
				          \toba_js::incluir(\toba_recurso::url_toba().'/siu/manejador_salida_bootstrap/js/respond.js/respond.min.js') .
				'<![endif]-->';
		return $salida;
	}
	
	public function getEstilosCss(){
		$color = referencia_config::getMainColor();
		$corte_0 = referencia_config::getCorteControl0();
		$corte_1 = referencia_config::getCorteControl1();
		$corte_2 = referencia_config::getCorteControl2();
		$salida = "<style>
				:root {
			 		--main-color: $color;
				}
				.corte-0{
					background-color: $corte_0;
				    color: white;
				}
				.corte-1{
					background-color: $corte_1;
				    color: black;
				}
				.corte-2{
					background-color: $corte_2;
				   	color: black;
				}
			</style>";
		$salida .= "	";
		return $salida;
	}
	
	public function getFinHead(){
		return"</head>";
	}
	
	public function getPreEncabezadoHtml(){
		

	}
	
	public function getPostEncabezadoHtml(){
		
	}
	
	public function getPreContenido(){
		$pre = "<div class='content-wrapper'>
					<section class='content-header'>
						
					</section>
					<section class='content row'>
						<div class='col-md-12'>
				";
		
		return "<div><div class='col-md-12 wrapper'>";
	}
	
	public function getPostContenido(){
		//cierre de los tags en el pre_contenido();
		$salida =  "		</div>
				</section>
		</div>"; 
		$salida .= "</div>";
		return $salida;
	}
	
	public function getInicioBarraSuperior(){
		
	}
	
	public function getContenidoBarraSuperior(){
		
	}
	
	public function getFinBarraSuperior(){
		
	}
	
	public function getInicioCuerpo(){
		$salida = "<body class='hold-transition sidebar-mini '>";
		$salida .= "<script>
				var colap = localStorage.getItem('colapsado');
				if( colap == 1 ){
					$('body').addClass('sidebar-collapse')
				}
			  </script>";
		
		$salida .= $this->incluir_override_js();
		$salida .= "<div id='div_toba_esperar' class='div-esperar' style='display:none'></div>"; //Se agrega sino toba no puede hacer la cascada! PAPELON!		
		return $salida;
	}
	
	private function incluir_override_js(){
		$files = $this->config["assets"]['js'];
		$salida = '';
		foreach ($files as $file){
			$salida .=  \toba_js::incluir(\toba_recurso::url_toba().$file);
		}
		return $salida;
	}
	
	public function getOverlay(){
		return'<div class="modal fade" tabindex="-1" role="dialog" id="modal_notificacion">
  					<div class="modal-dialog" role="document">
    					<div class="modal-content" >
				
				    	</div><!-- /.modal-content -->
				  	</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->';
	}
	
	public function getCapaEspera(){
		$logo = referencia_config::getLogoEspera();
		return'<div class="modal fade" tabindex="-1" role="dialog" id="modal_espera">
  					<div class="modal-dialog" role="document">
    					<div class="modal-content">
							<div class="modal-header">
								<div class="row">
									<img src="'.$logo.'"  class="center-block"/>
						        </div>
							</div>
							<div class="modal-body">
					        	<div class="row">
                      				<div class="progress">
  										<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="2" aria-valuemax="100" style="width: 100%">
	    									<span class="sr-only">45% Complete</span>
	  									</div>
									</div>
	                			</div>
								<div class="row text-center">
									<h4 class="">Procesando. Por favor aguarde...</h4>
								</div>
							</div>
					    </div><!-- /.modal-content -->
				  	</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->';
	}
	
	/**
	 * @todo ver la razon de que se imprima el editor dos veces
	 * {@inheritDoc}
	 * @see \SIU\InterfacesManejadorSalidaToba\Componentes\TiposPagina\IPaginaBasica::getFinCuerpo()
	 */
	public function getFinCuerpo(){
		$scripts = $this->footer_scripts();
		return"		$scripts
				</body>";
		
	}
	
	public function getFooterHtml(){
		
	}
	
	protected function footer_scripts(){
		return "
				</div> <!-- Cierra el div del wrapper abierton en preContenidoHtml -->
				<!-- AdminLTE App -->
				<script src='".\toba_recurso::url_toba()."/siu/manejador_salida_bootstrap/js/app.js"."'></script>
				<script >
				  $(document).ready(function(){
				    $.AdminLTE.layout.activate();
				  })
				</script>";  
	}
	
	public function getFinHtml(){
		return"</html>";
	}
	
	public function getResizeFuente($js_aumentar, $js_reducir){
		return \toba::output()->get('PaginaBasica',true)->getResizeFuente($js_aumentar, $js_reducir);
	}
	
}
