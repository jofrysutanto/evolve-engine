<ul>
    <?php foreach ($shareable as $share): ?>
    <li>
        <?= $share->service ?> - <?= $share->url ?>
    </li>
    <?php endforeach ?>
</ul>