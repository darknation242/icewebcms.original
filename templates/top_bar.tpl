<div class="chromestyle" id="chromemenu">
<ul>
<li><a href="/">Home</a></li>
<li><a href="#" rel="dropmenu1">Modules</a></li>
<li><a href="#">Armory</a></li>
</ul>
</div>

<!--1st drop down menu -->                                                   
<div id="dropmenu1" class="dropmenudiv">
{foreach from=$test item=foo}
<a href="{$foo.url}">{$foo.name}</a>
{/foreach}
</div>

<script type="text/javascript">

cssdropdown.startchrome("chromemenu")

</script>
