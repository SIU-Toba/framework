{foreach key=subpackage item=files from=$classleftindex}
  <div class="package">
	{if $subpackage != ""}
		{$subpackage}<br />
	{/if}
	{section name=files loop=$files}
    	{if $subpackage != ""}
    		<span style="padding-left: 1em;">
    	{/if}
		{if $files[files].link != ''}<a href="{$files[files].link}">{/if}
		{if $files[files].title|substr:0:5 == 'toba_'}
			{$files[files].title|substr:5}
		{else}
			{$files[files].title}
		{/if}
			{if $files[files].link != ''}</a>{/if}
    	{if $subpackage != ""}
    		</span>
    	{/if}
	
	 <br />
	{/section}
  </div>
{/foreach}
