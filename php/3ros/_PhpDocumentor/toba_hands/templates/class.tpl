{include file="header.tpl" eltype="clase" hasel=true contents=$classcontents}

<h2 class="class-name">{if $is_interface}Interface{else}Clase{/if} {$class_name}</h2>

<a name="sec-description"></a>
<div class="info-box">
	<div class="nav-bar">
		{if $children || $vars || $ivars || $methods || $imethods || $consts || $iconsts}
			<span class="disabled">Resúmen de la {if $is_interface}Interface{else}Clase{/if}</span> |
		{/if}
		{if $methods || $imethods}
			<a href="#sec-method-summary">Métodos</a>
			{if $vars || $ivars || $children || $consts || $iconsts}|{/if}			
		{/if}
		{if $children}
			<a href="#sec-descendents">Subclases</a>
			{if $vars || $ivars || $consts || $iconsts}|{/if}
		{/if}
		{if $ivars || $imethods}
			<a href="#sec-inherited">Propiedades, Constantes y Métodos Heredados</a>
			{if $vars || $ivars || $methods || $imethods || $consts || $iconsts}|{/if}
		{/if}
		{if $vars || $ivars}
			<a href="#sec-vars">Propiedades</a>
			{if $consts || $iconsts}|{/if}
		{/if}		
		{if $consts || $iconsts}
			<a href="#sec-consts">Constantes</a>
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

		<p class="notes">
			Ubicada en {$source_location} [<span class="field">line {if $class_slink}{$class_slink}{else}{$line_number}{/if}</span>]
		</p>

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

		{if count($tags) > 0}
<!--		
		<strong>Author(s):</strong>
		<ul>
		  {section name=tag loop=$tags}
			 {if $tags[tag].keyword eq "author"}
			 <li>{$tags[tag].data}</li>
			 {/if}
		  {/section}
		</ul> -->
		{/if}

		{include file="classtags.tpl" tags=$tags}
		</td>
		</tr></table>
	</div>
</div>


{if $methods}
	<a name="sec-method-summary"></a>
	<div class="info-box">
		<div class="info-box-title">Métodos</span></div>
		<div class="info-box-body">
			<div class="method-summary">
				<table border="0" cellspacing="0" cellpadding="0" class="method-summary">
				{section name=methods loop=$methods}
				{if $methods[methods].static}
				<div class="method-definition">
					<tr><td class="method-definition">static
					{if $methods[methods].function_return}
						<span class="method-result">{$methods[methods].function_return}</span>&nbsp;&nbsp;
					{/if}</td>
					<td class="method-definition"><a href="#{$methods[methods].function_name}" title="details" class="method-name">{if $methods[methods].ifunction_call.returnsref}&amp;{/if}{$methods[methods].function_name}</a>()&nbsp;&nbsp;</td>
					<td class="method-definition">{$methods[methods].sdesc}</td></tr>
				</div>
				{/if}
				{/section}
				{section name=methods loop=$methods}
				{if !$methods[methods].static}
				<div class="method-definition">
					{if $methods[methods].function_return}
						<tr><td class="method-definition"><span class="method-result">{$methods[methods].function_return}</span>&nbsp;&nbsp;</td>
					{/if}
					<td class="method-definition"><a href="#{$methods[methods].function_name}" title="details" class="method-name">{if $methods[methods].ifunction_call.returnsref}&amp;{/if}{$methods[methods].function_name}</a>()&nbsp;&nbsp;</td>
					<td class="method-definition">{$methods[methods].sdesc}</td></tr>
				</div>
				{/if}
				{/section}
				</table>
			</div>
			<br />
		</div>
	</div>
{/if}

{if $children}
	<a name="sec-descendents"></a>
	<div class="info-box">
		<div class="info-box-title">Subclases directas</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				<tr>
					<th class="class-table-header">Clase Hija</th>
					<th class="class-table-header">Descripción</th>
				</tr>
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

{if $ivars || $imethods || $iconsts}
	<a name="sec-inherited"></a>
	<div class="info-box">
		<div class="info-box-title">Propiedades, Constantes y Métodos Heredados</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				<tr>
					<th class="class-table-header" width="30%">Propiedades Heredadas</th>
					<th class="class-table-header" width="40%">Métodos Heredados</th>
					<th class="class-table-header" width="30%">Constantes Heredadas</th>
				</tr>
				<tr>
					<td width="30%">
						{section name=ivars loop=$ivars}
							<p>Heredado de <span class="classname">{$ivars[ivars].parent_class}</span></p>
							<blockquote>
								<dl>
									{section name=ivars2 loop=$ivars[ivars].ivars}
										<dt>
											<span class="method-definition">{$ivars[ivars].ivars[ivars2].link}</span>
										</dt>
										<dd>
											<span class="method-definition">{$ivars[ivars].ivars[ivars2].ivars_sdesc}</span>
										</dd>
									{/section}
								</dl>
							</blockquote>
						{/section}
					</td>
					<td width="40%">
						{section name=imethods loop=$imethods}
							<p>Heredado de <span class="classname">{$imethods[imethods].parent_class}</span></p>
							<blockquote>
								<dl>
									{section name=im2 loop=$imethods[imethods].imethods}
										<dt>
											<span class="method-definition">{$imethods[imethods].imethods[im2].link}</span>
										</dt>
										<dd>
											<span class="method-definition">{$imethods[imethods].imethods[im2].sdesc}</span>
										</dd>
									{/section}
								</dl>
							</blockquote>
						{/section}
					</td>
					<td width="30%">
						{section name=iconsts loop=$iconsts}
							<p>Heredado de <span class="classname">{$iconsts[iconsts].parent_class}</span></p>
							<blockquote>
								<dl>
									{section name=iconsts2 loop=$iconsts[iconsts].iconsts}
										<dt>
											<span class="method-definition">{$iconsts[iconsts].iconsts[iconsts2].link}</span>
										</dt>
										<dd>
											<span class="method-definition">{$iconsts[iconsts].iconsts[iconsts2].iconsts_sdesc}</span>
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


{if $vars || $ivars}
	<a name="sec-vars"></a>
	<div class="info-box">
		<div class="info-box-title">Propiedades</div>
		<div class="info-box-body">
			{include file="var.tpl"}
		</div>
	</div>
{/if}


{if $consts || $consts}
	<a name="sec-consts"></a>
	<div class="info-box">
		<div class="info-box-title">Constantes</div>
		<div class="info-box-body">
			{include file="const.tpl"}
		</div>
	</div>
{/if}

{include file="footer.tpl"}