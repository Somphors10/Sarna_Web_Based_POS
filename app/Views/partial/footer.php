<?php

use Config\OSPOS;

?>

        </div>
    </div>
</main>
</div>

    <div id="footer" class="pos-footer-wrap">
        <div class="jumbotron push-spaces pos-footer">
            <strong class="pos-footer-text">
                <?= lang('Common.copyrights', [date('Y')]) ?>
                <span class="pos-footer-separator">·</span>
                <a href="https://opensourcepos.org" target="_blank"><?= lang('Common.website') ?></a>
                <span class="pos-footer-separator">·</span>
                <?= esc(config('App')->application_version) ?> -
                <a target="_blank" href="https://github.com/opensourcepos/opensourcepos/commit/<?= esc(config(OSPOS::class)->commit_sha1) ?>">
                    <?= esc(substr(config(OSPOS::class)->commit_sha1, 0, 6)); ?>
                </a>
            </strong>
        </div>
    </div>
</body>

</html>
