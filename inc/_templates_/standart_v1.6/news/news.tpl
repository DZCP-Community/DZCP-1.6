<!-- start news.tpl -->
<div style="margin-top: 20px"></div>
{$show_sticky}
{$show}
<div class="col-md-12">
    <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative card-cell">
        <div class="col" style="display: flex;align-items: center;margin-left: 10px;">
            <select name="newskat" id="newskat" class="selectpicker">
                <option value="lazy" class="selectpicker">- {lang msgID="news_kat_choose"} -</option>
                {$kategorien}
            </select>
        </div>
        {if $nav}
        <div class="col">
            {$nav}
        </div>
        {/if}
    </div>
</div>
<!-- end news.tpl -->