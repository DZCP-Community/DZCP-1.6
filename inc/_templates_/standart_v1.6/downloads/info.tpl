<div class="row" style="margin-top: 4px;">
    <div class="col-lg-12" style="margin-bottom: 20px;">
        <div class="card position-relative shadow" style="cursor:pointer">
            <div class="card-header">
                <span class="m-0 font-weight-bold text-danger">Download: {$titel}</span>
            </div>
            <div class="card-body card-cell" style="padding: 0.6rem;">
              <div class="card-group">
                <div class="card" style="max-width: 300px;">
                  <div class="card-body card-cell">
                      <div class="item">
                          <div class="clearfix" style="max-width:474px;">
                              <ul id="image-gallery" class="gallery list-unstyled cS-hidden">
                                {$pic}
                              </ul>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mt-2">
                            <b>Datei:</b> {$file}
                        </div>
                        <div class="mt-2">
                            <b>Kategorie:</b> {$kat}
                        </div>
                        <div class="mt-2">
                            <b>Downloads:</b> {$hits}
                        </div>
                        <div class="mt-2">
                            <b>Datum:</b> {$date}
                        </div>
                        <div class="mt-2">
                            <b>Aktualisiert:</b> {$updated}
                        </div>
                        <div class="mt-2">
                            <b>SHA1-Hash:</b><p>{$crc}</p>
                        </div>
                    </div>
                </div>
              </div>
                <div class="card mb-2">
                    <div class="card-body card-cell">
                        {$desc}
                    </div>
                </div>
                <div class="card mb-2">
                    <div class="card-body card-cell">
                        <form id="download" name="download" method="post" target="_blank" action="?action=getfile">
                            <button type="submit" class="btn btn-secondary btn-lg btn-block">Download</button>
                        </form>
                    </div>
                </div>
                {if {$has_forum_url}}
                    <div class="card mb-2">
                        <div class="card-body card-cell">
                            <form id="forum" name="forum" method="post" target="_self" action="../forum/{$forum_url}">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Zum Forum</button>
                            </form>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
