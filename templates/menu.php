<?php
foreach($this->pages as $page) {
    if(!$page->menu) continue;
    $pages[] = $page;
}
if(empty($pages)) return;
?>

<ul>
    <?php foreach($pages as $page): ?>
        <?php if($page->public || ($this->user && in_array($this->user->role_slug, $page->access))): ?>
            <?php if($page->spacer): ?><li class="menu__spacer"></li><?php endif; ?>
            <li class="menu__item<?php echo $page->submenu ? ' menu__item__sub' : '' ; ?><?php echo $this->page->path == $page->path || $this->page->menupath == $page->path ? ' active' : ''; ?>">
                <?php echo $page->submenu ? '&nbsp;&nbsp;&nbsp;&nbsp;' : ''; ?><a href="<?php echo $this->root . $page->path; ?>"><?php echo $page->name; ?></a>
            </li>
        <?php endif;?>
    <?php endforeach; ?>
    <li><div class="switch"><div class="switch__pin"></div></div></li>
</ul>

<script><?php include('lightswitch'.(PROD ? '.min' : '').'.js'); ?>lightswitch.init();</script>
