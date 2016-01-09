<h1>{$request|capitalize}</h1>
<small>Tipo: <b>{$type|capitalize}</b>. Zoom: <b>{if $zoom}{$zoom}x{else}Auto{/if}</b></small>
{space10}

<center>
	{*<img width="100%" src="{$imageGOOGLE}" alt="{$request}"/>*}
	{img width="100%" src="{$image}" alt="{$request}"}
	{space10}
</center>
{space15}

<h2>Ajustes:</h2>
<small>
	<b>Tipo:</b> 
	{if $type eq "hibrido"}H&iacute;brido{else}{link href="MAPA {$request} {$zoom} hibrido" caption="H&iacute;brido"}{/if}{separator}
	{if $type eq "fisico"}F&iacute;sico{else}{link href="MAPA {$request} {$zoom} fisico" caption="F&iacute;sico"}{/if}{separator}
	{if $type eq "politico"}Pol&iacute;tico{else}{link href="MAPA {$request} {$zoom} politico" caption="Pol&iacute;tico"}{/if}{separator}
	{if $type eq "terreno"}Terreno{else}{link href="MAPA {$request} {$zoom} terreno" caption="Terreno"}{/if}

	<br/>

	<b>Zoom:</b>
	{if $zoom eq ""}Auto{else}{link href="MAPA {$request} {$type}" caption="Auto"}{/if} {separator}
	{for $i=10 to 20}
		{if $zoom eq $i}{$i}x{else}{link href="MAPA {$request} {$type} {$i}x" caption="{$i}x"}{/if}
		{if $i lt 20}{separator}{/if}
	{/for}
</small>
