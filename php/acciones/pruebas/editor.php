<?
	
	//ei_arbol($_POST,"POST");
	$form = "FORM";
	$editor = "editor";
	$vinculo = $this->vinculador->generar_solicitud();

	if(isset($_POST[$editor])){
		$valor = stripslashes($_POST[$editor]);
		echo $valor;
	}else{
		$valor = "Hola";
	}

?>
<html>
  <head>
  </head>

<body>

<script type="text/javascript" src="<? echo recurso::js("fckeditor/fckeditor.js") ?>"></script>

<form name='<? echo $form ?>' method='post' action='<? echo $vinculo  ?>'>
<script type="text/javascript">
   var oFCKeditor = new FCKeditor('<? echo $editor ?>','100%','400','Toba','<? echo $valor ?>' ) ;
  oFCKeditor.BasePath = 'js/fckeditor/';
  //oFCKeditor.ToolbarSet = "Toba";
  oFCKeditor.Create() ;
</script>
<input name='enviar' type='submit'>
</form>

<?/*
    <textarea id="MyTextarea" name="MyTextarea">This is <b>the</b> initial value.</textarea>
    <script type="text/javascript">
      window.onload = function()
      {
        var oFCKeditor = new FCKeditor( 'MyTextarea' ) ;
		oFCKeditor.BasePath = 'js/fckeditor/';
        oFCKeditor.ReplaceTextarea() ;
      }
    </script>
*/?>
  </body>
</html>