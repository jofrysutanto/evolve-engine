<ul class="social-share">
    <?php foreach ($shareable as $share): ?>
    <li class="social-share__item social-share__item--<?= $share->service ?>">
        <a href="<?= $share->url ?>" title="Share this" target="_blank">
            &nbsp;
        </a>
    </li>
    <?php endforeach ?>
</ul>