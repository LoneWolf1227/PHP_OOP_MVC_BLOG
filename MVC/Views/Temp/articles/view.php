<?php require __DIR__ . '/../header.php'?>
    <h2><?=$article->getName()?></h2>
    <p><?=$article->getText()?></p>
    <p>Автор: <?=$article->getAuthor()->getNickname()?></p>
<?php require __DIR__ . '/../footer.php'?>