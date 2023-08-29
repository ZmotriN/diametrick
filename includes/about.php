<?php
const ROWS = 6;
const CELLS = 12;
mt_srand(microtime(1));
?>

<div class="credits">
    <h2>Développeur Full-Stack</h2>
    Maxime Larrivée-Roy / <a target="_blank" href="https://zmotrin.github.io/exercices-css/">zmotrin.github.io</a>
    <br><br>
    <div class="credits__anim">
        <?php for($i = 0; $i < ROWS; $i++): ?>
            <div class="credits__anim__line">
                <?php for($j = 0; $j < CELLS; $j++): ?>
                    <div class="credits__anim__block" style="--nb1: <?php echo sprintf('%.03f',rand(0,1000)/1000); ?>; --nb2: <?php echo sprintf('%.03f',rand(0,1000)/1000); ?>; --nb3: <?php echo sprintf('%.03f',rand(0,1000)/1000); ?>; --nb4: <?php echo sprintf('%.03f',rand(0,1000)/1000); ?>;"></div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>