<div class="col-lg-6">
    <div class="card position-relative shadow" style="cursor:pointer" onclick="DZCP.goTo('index.php?action=download&amp;id={$id}')">
        <div class="card-header">
            <span class="m-0 font-weight-bold text-danger">Download: {$titel}</span>
        </div>
        <div class="card-body card-cell" style="padding: 0.6rem;">
            <div class="card mb-2">
                <div class="card-body">
                    <b>Kategorie:</b> {$kat}
                    <div class="mt-2">
                        <b>Downloads:</b> {$hits}
                    </div>
                    <div class="mt-2">
                        <b>Datum:</b> {$date}
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body card-cell">
                    <img src="{$pic}" alt="" style="width:264px; height:200px;"/>
                </div>
            </div>
            <div class="card mb-2 border-left-primary">
                <div class="card-body card-cell">
                    {$desc}
                </div>
            </div>
        </div>
    </div>
</div>