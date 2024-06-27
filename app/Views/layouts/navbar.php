<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
    <a href="<?= base_url(); ?>" class="app-brand-link">
        <span class="app-brand-text menu-text fw-bold ms-2">PARAWARGA</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
    </div>

    <ul class="menu-inner py-1">
        <?php
            $menuParent = array_filter($menu, static fn ($item) => $item['parent_id'] == 0);
            foreach($menuParent as $index => $val) : ?>
                <li class="menu-item <?= ($val['is_active']) ? $val['is_active'] . ' open' : ''?>">
                    <a href="<?= (!empty($val['link'])) ? base_url($val['link'])  : base_url() ?>" class="menu-link <?= $val['toggle'] ?>">
                        <i class="menu-icon tf-icons bx <?= (!empty($val['icon'])) ? $val['icon'] : 'bx-folder'?>"></i>
                        <?= $val['name'];?>
                    </a>
                    <?php 
                        $menuChild = array_filter($menu, static fn ($item) => $item['parent_id'] == $val['id']);
                        foreach($menuChild as $indexChild => $valChild) : ?>
                        <ul class="menu-sub">
                            <li class="menu-item <?= $valChild['is_active']?>">
                                <a href="<?= base_url($valChild['link'])?>" class="menu-link">
                                <?= $valChild['name'];?>
                                </a>
                            </li>
                        </ul>
                    <?php endforeach; ?>
                </li>
        <?php endforeach; ?>

        <!-- Misc -->
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
        <li class="menu-item">
            <a
            href="https://github.com/themeselection/sneat-html-admin-template-free/issues"
            target="_blank"
            class="menu-link">
                <i class="menu-icon tf-icons bx bx-support"></i>
                Support
            </a>
        </li>
        <li class="menu-item">
            <a
            href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/documentation/"
            target="_blank"
            class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                Documentation
            </a>
        </li>
    </ul>
</aside>