{include file="header.tpl" eltype="clase" hasel=true contents=$classcontents}

<a name="sec-description"></a>
<div class="info-box">
	<div class="nav-bar">
		{if $children || $vars || $methods || $imethods || $consts || $iconsts}
			<span class="disabled">Resúmen de la {if $is_interface}Interface{else}Clase{/if}</span> |
		{/if}

		{if $children}
			<a href="#sec-descendents">Subclases</a>
			{if $imethods || $methods}|{/if}
		{/if}
		{if $imethods}
			<a href="#sec-inherited">Métodos Heredados</a>
			{if $vars  || $methods || $imethods || $consts || $iconsts}|{/if}
		{/if}
		{if $methods || $imethods}
			<a href="#sec-method-summary">Métodos Propios</a>
		{/if}		
	</div>
	<div class="info-box-body">
		<table width="100%" border="0">
		<tr><td valign="top" width="60%" class="class-overview">

        {if $implements}
        <p class="implements">
            Implementa interfaces:
            <ul>
                {foreach item="int" from=$implements}<li>{$int}</li>{/foreach}
            </ul>
        </p>
        {/if}
		{include file="docblock.tpl" type="class" sdesc=$sdesc desc=$desc}



		{if $tutorial}
			<hr class="separator" />
			<div class="notes">Tutorial: <span class="tutorial">{$tutorial}</div>
		{/if}

		<pre>{section name=tree loop=$class_tree.classes}{$class_tree.classes[tree]}{$class_tree.distance[tree]}{/section}</pre>

		{if $conflicts.conflict_type}
			<hr class="separator" />
			<div><span class="warning">Entra en Conflicto con clases:</span><br />
			{section name=me loop=$conflicts.conflicts}
				{$conflicts.conflicts[me]}<br />
			{/section}
			</div>
		{/if}

		<p class="notes">
			Ubicada en {$source_location} [<span class="field">line {if $class_slink}{$class_slink}{else}{$line_number}{/if}</span>]
		</p>	
		{include file="classtags.tpl" tags=$tags}
	
		</td>
		</tr></table>
	</div>
</div>


{if $children}
	<a name="sec-descendents"></a>
	<div class="info-box">
		<div class="info-box-title">Subclases directas</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				{section name=kids loop=$children}
				<tr>
					<td style="padding-right: 2em">{$children[kids].link}</td>
					<td>
					{if $children[kids].sdesc}
						{$children[kids].sdesc}
					{else}
						{$children[kids].desc}
					{/if}
					</td>
				</tr>
				{/section}
			</table>
			<br />
		</div>
	</div>
{/if}



{if $ivars || $imethods}
	<a name="sec-inherited"></a>
	<div class="info-box">
		<div class="info-box-title">Métodos Heredados</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				<tr>
					<td width="100%">
						{section name=imethods loop=$imethods}
							<p>Heredado de <span class="classname"><strong>{$imethods[imethods].parent_class}</strong></span></p>
							<blockquote>
								<dl>
									{section name=im2 loop=$imethods[imethods].imethods}
										{assign var="met_comienzo" value=$imethods[imethods].imethods[im2].link|strpos:'>'}
										{assign var="met_fin" value=$imethods[imethods].imethods[im2].link|strpos:'::'}
										{assign var="nuevo1" value=$imethods[imethods].imethods[im2].link|substr:0:$met_comienzo+1}
										{assign var="nuevo2" value=$imethods[imethods].imethods[im2].link|substr:$met_fin+2}
										<dt>
											<span class="method-definition">{$nuevo1}{$nuevo2}</span>
										</dt>
										<dd>
											<span class="method-definition">{$imethods[imethods].imethods[im2].sdesc}</span>
										</dd>
										
									{/section}
								</dl>
							</blockquote>
						{/section}
					</td>
				</tr>
			</table>
			<br />
		</div>
	</div>
{/if}

{if $methods}
	<a name="sec-method-summary"></a>
	<div class="info-box">
		<div class="info-box-title">Métodos Propios</div>
		<div class="info-box-body">
			<div class="method-summary">
				<table border="0" cellspacing="5" cellpadding="0" class="method-summary">
				{section name=methods loop=$methods}
					{if $methods[methods].static}
						<tr><td></td><td class="method-definition" nowrap>static
						{if $methods[methods].function_return}
							<span class="method-result">{$methods[methods].function_return}</span>&nbsp;&nbsp;
						{/if}</td>
						<td class="method-definition"><a href="#{$methods[methods].function_name}" title="details" class="method-name">{if $methods[methods].ifunction_call.returnsref}&amp;{/if}{$methods[methods].function_name}</a>()&nbsp;&nbsp;</td>
						<td class="method-definition">{$methods[methods].sdesc}</td></tr>
					{/if}
				{/section}
				{section name=methods loop=$methods}
					{if !$methods[methods].static}
						<tr>
						<td nowrap align="right">
							{section name=tag loop=$methods[methods].info_tags}
							    {if $methods[methods].info_tags[tag].keyword eq "ventana"}
						    		<img border='0' title='Ventana de extensión' style='vertical-align: middle' src="{$subdir}media/ventana.png" />   	
						    	{/if}
							{/section}
							{section name=tag loop=$methods[methods].api_tags}
							    {if $methods[methods].api_tags[tag].data eq "protected"}
						    	<img border='0' title='protected' style='vertical-align: middle' src="{$subdir}media/candado.png" />
						    	{/if}
							{/section}	
						</td>
						{if $methods[methods].function_return}
							<td nowrap class="method-definition"><span class="method-result">{$methods[methods].function_return}</span>&nbsp;&nbsp;</td>
						{/if}
						<td class="method-definition"><a href="#{$methods[methods].function_name}" title="details" class="method-name">{if $methods[methods].ifunction_call.returnsref}&amp;{/if}{$methods[methods].function_name}</a>()&nbsp;&nbsp;</td>
						<td class="method-definition">{$methods[methods].sdesc}</td></tr>
					{/if}
				{/section}
				</table>
			</div>
			<br />
		</div>
	</div>
{/if}



{if $consts}
	<a name="sec-const-summary"></a>
	<div class="info-box">
		<div class="info-box-title">Constantes</span></div>
		<div class="info-box-body">
			<div class="const-summary">
			<table border="0" cellspacing="0" cellpadding="0" class="var-summary">
			{section name=consts loop=$consts}
				<div class="var-title">
					<tr>
					<td class="var-title"><a href="#{$consts[consts].const_dest}" title="details" class="const-name-summary">{$consts[consts].const_name}</a>&nbsp;&nbsp;</td>
					<td class="const-summary-description">{$consts[consts].sdesc}</td></tr>
				</div>
				{/section}
				</table>
			</div>
			<br />
		</div>
	</div>
{/if}



{if $methods || $imethods}
	<a name="sec-methods"></a>
	<div class="info-box">

		<div class="info-box-body">
			{include file="method.tpl"}
		</div>
	</div>
{/if}

{include file="footer.tpl"}