{if count($info_tags) > 0}
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$info_tags}
	{if $info_tags[tag].keyword ne "author" and $info_tags[tag].keyword ne "jsdoc"
		and $info_tags[tag].keyword ne "wiki"}
			<tr><td><strong>{$info_tags[tag].keyword|capitalize}:</strong>&nbsp;&nbsp;</td><td>{$info_tags[tag].data}</td></tr>
	{/if}
	{if $info_tags[tag].keyword eq "wiki"}
		<tr>
			<td colspan="2"><strong><a href='{$subdir}../wiki/trac/toba/wiki/{$info_tags[tag].data}.html'>
				<img border='0' style='vertical-align: middle' src="{$subdir}media/wiki-small.png" />
			Documentación WIKI</a></strong></td></tr>
	{/if}	
	{if $info_tags[tag].keyword eq "jsdoc"}
		{assign var="jsdoc" value=" "|split:$info_tags[tag].data}
		<tr><td colspan="2"><a href='{$subdir}../api_js/{$jsdoc[0]}.html'>
		<img border='0' style='vertical-align: middle' src="{$subdir}media/javascript-small.png" />
		<strong>Clase Javascript equivalente: {$jsdoc[1]}</strong></a></td></tr>
	{/if}
{/section}
</table>
{/if}
<br>
{if count($api_tags) > 0}
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$api_tags}
  <tr>
    <td class="indent"><strong>{$api_tags[tag].keyword|capitalize}</strong>&nbsp;&nbsp;</td><td>{$api_tags[tag].data}</td>
  </tr>
{/section}
</table>
<br />
{/if}
