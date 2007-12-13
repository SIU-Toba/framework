<?php
require_once("tp_referencia.php");

class tp_tutorial extends tp_referencia 
{

	protected function estilos_css()
	{
		parent::estilos_css();
		?>
		<style type="text/css">
			.codigo {
				padding: 10px; 
				margin-top: 5px;
				margin-bottom: 5px;
				border: 1px solid black; 
				background-color:#EEEAEE;		
			}
			.ci-cuerpo, .ci-wiz-cont {
				border: none;
				background-color: white;
				font-size: 12px;
			}
			.ci-wiz-enc {
				border: 0;
			}
			.ci-wiz-titulo, h2 {
				font-size: 1.5em; 
			}
			.lista-separada {
				list-style-image: url('<?php echo toba_recurso::imagen_toba('nucleo/carpeta.gif'); ?>');
			}
			.lista-separada li {
				margin-top: 10px;
			}	
			.lista-separada .proyectos {
				background-color: #EEEAEE;
				border: 1px solid gray;
			}
			li {
				margin-bottom: 4px;
			}
			.caption {
				text-align: left;
				margin-left: 10px;
				margin-right: 10px;
				font-size:80%;
				font-style: italic;
				display:block;
			}
			.img-ef {
				background-color: #D5CECD;
				border: 1px solid gray;
			}			
		</style>			
		<?php
	}	
	
	function titulo_item()
	{
		return 'Tutorial';	
	}
	
}


function mostrar_video($video, $ancho=992, $alto=487, $controlador_propio=false)
{
	$url_base = get_url_desarrollos(true).'/toba_referencia_trunk/videos/';
	$url_js = toba_recurso::url_proyecto().'/videos/';	
	$url_controller = ($controlador_propio) ? $url_base.$video.'-cont.swf'  : $url_base.'controller.swf';
	return '
      <script type="text/javascript" src="'.$url_js.'swfobject.js"></script>
  	  <script type="text/javascript" src="'.$url_js.'cam_embed.js"></script>		
      <div id="flashcontent">	   		
			<div id="cs_noexpressUpdate">
			  <p>The Camtasia Studio video content presented here requires JavaScript to be enabled and the  latest version of the Macromedia Flash Player. If you are you using a browser with JavaScript disabled please enable it now. Otherwise, please update your version of the free Flash Player by <a href="http://www.macromedia.com/go/getflashplayer">downloading here</a>. </p>
		    </div>
	   </div>
      <script type="text/javascript">
		  // <![CDATA[          
         var fo = new SWFObject( "'.$url_controller.'", "'.$url_controller.'", "'.$ancho.'", "'.$alto.'", "7", "#FFFFFF", false, "best" );
         fo.addVariable( "csConfigFile", "'.$url_base.$video.'.xml"  ); 
         fo.addVariable( "csColor"     , "FFFFFF"           );
         fo.addVariable( "csPreloader" , "'.$url_base.'preload.swf" );
         if( args.movie )
         {
            fo.addVariable( "csFilesetBookmark", args.movie );
         }
         fo.write("flashcontent"); 		  	  
         // ]]>

	   </script>  	
   ';	
}

function mostrar_php($codigo)
{
	$salida = highlight_string($codigo, true);
	$salida = str_replace('&lt;?php<br />', '', $salida);
	$salida = str_replace('?&gt;<br />', '', $salida);
	return "<div class='codigo'>".$salida."</div>";
}
?>