{if count($api_tags) > 0}
<strong>Notas Extras:</strong><br />
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$api_tags}
  <tr>
    <td class="indent"><strong>{$api_tags[tag].keyword|capitalize}:</strong>&nbsp;&nbsp;</td><td>{$api_tags[tag].data}</td>
  </tr>
{/section}
</table>
<br />
{/if}

{if count($info_tags) > 0}
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$info_tags}
	{if $info_tags[tag].keyword ne "author"}
		{if $info_tags[tag].keyword ne "jsdoc"}
			<tr><td><strong>{$info_tags[tag].keyword|capitalize}:</strong>&nbsp;&nbsp;</td><td>{$info_tags[tag].data}</td></tr>
		{/if}
	{/if}
	{if $info_tags[tag].keyword eq "jsdoc"}
		{assign var="jsdoc" value=" "|split:$info_tags[tag].data}
		<tr><td><strong>Clase Javascript equivalente:</strong>&nbsp;&nbsp;</td>
			<td><a href='{$subdir}../api_js/{$jsdoc[0]}.html'>{$jsdoc[1]}</a></td></tr>
	{/if}
{/section}
</table>
{/if}
