<?php
use Zend\Escaper\Escaper;
class toba_escapador extends Escaper
{
	private $_es_editor;
	
	function __construct($encoding = null)
	{
		$this->_es_editor = (toba::proyecto()->get_id() === 'toba_editor');
		parent::__construct($encoding);
	}
		
	function escapeHtml($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? htmlentities($texto, ENT_QUOTES , apex_default_charset) : parent::escapeHtml($texto);  //  htmlentities($texto, ENT_QUOTES , apex_default_charset)
	}
	
	function escapeHtmlAttr($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? $texto: parent::escapeHtmlAttr($texto);
	}
	
	function escapeCss($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? $texto: parent::escapeCss($texto);
	}
	
	function escapeJs($texto)
	{
		return (isset($this->_es_editor) && ($this->_es_editor === true)) ? $texto: parent::escapeJs($texto);
	}
}
?>
