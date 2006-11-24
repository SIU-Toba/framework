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
				margin-left: 20%;
				margin-right: 20%;				
				display: block;
			}
			.tutorial-agenda li {
				padding-top: 10px;
			}
			.tutorial-agenda a {
				font-size: 16px;
			}
			.ci-wiz-titulo {
				font-size: 1.5em; 
			}
		</style>			
		<?php
	}	
	
	function titulo_item()
	{
		return 'Tutorial';	
	}
	
}


function mostrar_video($video)
{
	$url = toba_recurso::url_proyecto()."/videos/$video/";
	$url_base = toba_recurso::url_proyecto().'/videos/';	
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
         var fo = new SWFObject( "'.$url_base.'controller.swf", "'.$url_base.'controller.swf", "992", "487", "7", "#FFFFFF", false, "best" );
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