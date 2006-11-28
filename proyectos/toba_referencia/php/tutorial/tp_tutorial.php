<?php
require_once("tp_referencia.php");

class tp_tutorial extends tp_referencia 
{

	protected function estilos_css()
	{
		parent::estilos_css();
		?>
		<style type="text/css">
			.ci-cuerpo, .ci-wiz-cont {
				border: none;
				background-color: white;
				font-size: 12px;
			}
			.screenshot {
				display:block;
				margin: 10px 0px 10px 50px;
			}
			.screenshot img {
				border: 1px outset gray;			
			}
			.tutorial-agenda {
				margin-left: auto;
				margin-right: auto;
				width: 400px;
			}
			.tutorial-agenda ol {
			}
			.tutorial-agenda li {
				padding-top: 10px;
			}
			.tutorial-agenda a {
				font-size: 16px;
			}
			.ci-wiz-titulo, h2 {
				font-size: 1.5em; 
			}
			.lista-separada {
				list-style-image: url('<?php echo toba_recurso::imagen_toba('archivos/carpeta.gif'); ?>');
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
	$url = toba_recurso::url_proyecto()."/videos/$video/";
	$url_base = toba_recurso::url_proyecto().'/videos/';	
	$url_controller = ($controlador_propio) ? $url.'controller.swf'  : $url_base.'controller.swf';
	return '
      <script type="text/javascript" src="'.$url_base.'swfobject.js"></script>
  	  <script type="text/javascript" src="'.$url_base.'cam_embed.js"></script>		
      <div id="flashcontent">	   		
			<div id="cs_noexpressUpdate">
			  <p>The Camtasia Studio video content presented here requires JavaScript to be enabled and the  latest version of the Macromedia Flash Player. If you are you using a browser with JavaScript disabled please enable it now. Otherwise, please update your version of the free Flash Player by <a href="http://www.macromedia.com/go/getflashplayer">downloading here</a>. </p>
		    </div>
	   </div>
      <script type="text/javascript">
		  // <![CDATA[          
         var fo = new SWFObject( "'.$url_controller.'", "'.$url_controller.'", "'.$ancho.'", "'.$alto.'", "7", "#FFFFFF", false, "best" );
         fo.addVariable( "csConfigFile", "'.$url.'config.xml"  ); 
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

?>