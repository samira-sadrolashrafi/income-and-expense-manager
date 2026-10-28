<aside class="col-lg-3 col-xl-2 d-none d-lg-block dashboard-sidebar-wrap">
    <nav class="dashboard-sidebar" aria-label="منوی اصلی ترازینو">
        <button type="button" class="sidebar-toggle" data-sidebar-toggle
                aria-expanded="false" aria-label="باز کردن منو" title="باز کردن منو">
            <i class="fa-solid fa-angles-left" aria-hidden="true"></i>
        </button>

        <a class="dashboard-brand" href="<?= html_escape(site_url('dashboard')) ?>" aria-label="ترازینو - داشبورد">
            <svg class="tz-brand-logo" viewBox="0 0 48 48" width="44" height="44" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="tzMarkDesktop" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#132D56"/>
                        <stop offset="1" stop-color="#174B96"/>
                    </linearGradient>
                </defs>
                <rect x="1" y="1" width="46" height="46" rx="12" fill="url(#tzMarkDesktop)"/>
                <rect x="7" y="9" width="18" height="7" rx="2.4" fill="#DAECFF"/>
                <rect x="7" y="21" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                <rect x="17" y="21" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                <rect x="7" y="31" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                <rect x="17" y="31" width="6" height="6" rx="1.6" fill="#BBD9FF"/>
                <rect x="27" y="28" width="5" height="10" rx="2" fill="#42A5F5"/>
                <rect x="34" y="21" width="5" height="17" rx="2" fill="#55C5ED"/>
                <rect x="41" y="14" width="4" height="24" rx="2" fill="#41D4B4"/>
            </svg>
            <span>ترازینو</span>
        </a>

        <div class="nav nav-pills flex-column gap-1">
            <a class="nav-link<?= $active_page === 'dashboard' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('dashboard')) ?>"
               aria-label="داشبورد" title="داشبورد"
               aria-current="<?= $active_page === 'dashboard' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>داشبورد</span>
            </a>

            <a class="nav-link<?= $active_page === 'transactions' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('transactions')) ?>"
               aria-label="تراکنش‌ها" title="تراکنش‌ها"
               aria-current="<?= $active_page === 'transactions' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                <span>تراکنش‌ها</span>
            </a>

            <a class="nav-link<?= $active_page === 'categories' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('categories')) ?>"
               aria-label="دسته‌بندی‌ها" title="دسته‌بندی‌ها"
               aria-current="<?= $active_page === 'categories' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-tags" aria-hidden="true"></i>
                <span>دسته‌بندی‌ها</span>
            </a>

            <a class="nav-link<?= $active_page === 'profile' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('profile')) ?>"
               aria-label="پروفایل" title="پروفایل"
               aria-current="<?= $active_page === 'profile' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
                <span>پروفایل</span>
            </a>
        </div>

        <fieldset class="tz-theme-control mt-3 pt-2 border-top border-light border-opacity-10" data-component="theme-selector">
            <legend class="tz-theme-legend">
                <i class="fa-solid fa-palette" aria-hidden="true"></i><span>تم برنامه</span>
            </legend>
            <div class="tz-theme-options d-flex gap-2">
                <label class="tz-theme-option">
                    <input type="radio" name="theme-desktop" value="light" aria-label="تم روشن">
                    <i class="fa-solid fa-sun" aria-hidden="true"></i><span>روشن</span>
                </label>
                <label class="tz-theme-option">
                    <input type="radio" name="theme-desktop" value="dark" aria-label="تم تیره">
                    <i class="fa-solid fa-moon" aria-hidden="true"></i><span>تیره</span>
                </label>
            </div>
        </fieldset>

        <div class="tz-sidebar-spacer pt-3 border-top border-light border-opacity-10">
            <button type="button" class="nav-link w-100 border-0 text-start"
                    aria-label="خروج" title="خروج"
                    data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span>خروج</span>
            </button>
        </div>
    </nav>
</aside>

<div class="offcanvas offcanvas-start" tabindex="-1" id="dashboardMobileNav"
     aria-labelledby="dashboardMobileNavTitle">
    <div class="offcanvas-header border-bottom">
        <h2 class="offcanvas-title h5 mb-0" id="dashboardMobileNavTitle">
            <i class="fa-solid fa-calculator text-primary me-2" aria-hidden="true"></i>
            ترازینو
        </h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="بستن منو"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        <nav class="nav nav-pills flex-column gap-1 dashboard-mobile-nav" aria-label="منوی ترازینو در موبایل">
            <a class="nav-link<?= $active_page === 'dashboard' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('dashboard')) ?>"
               aria-current="<?= $active_page === 'dashboard' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>داشبورد</span>
            </a>

            <a class="nav-link<?= $active_page === 'transactions' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('transactions')) ?>"
               aria-current="<?= $active_page === 'transactions' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></i>
                <span>تراکنش‌ها</span>
            </a>

            <a class="nav-link<?= $active_page === 'categories' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('categories')) ?>"
               aria-current="<?= $active_page === 'categories' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-tags" aria-hidden="true"></i>
                <span>دسته‌بندی‌ها</span>
            </a>

            <a class="nav-link<?= $active_page === 'profile' ? ' active' : '' ?>"
               href="<?= html_escape(site_url('profile')) ?>"
               aria-current="<?= $active_page === 'profile' ? 'page' : 'false' ?>">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
                <span>پروفایل</span>
            </a>
        </nav>

        <fieldset class="tz-theme-control tz-mobile-theme-control mt-3 pt-2 border-top" data-component="theme-selector">
            <legend class="tz-theme-legend">
                <i class="fa-solid fa-palette" aria-hidden="true"></i><span>تم برنامه</span>
            </legend>
            <div class="tz-theme-options d-flex gap-2">
                <label class="tz-theme-option">
                    <input type="radio" name="theme-mobile" value="light" aria-label="تم روشن">
                    <i class="fa-solid fa-sun" aria-hidden="true"></i><span>روشن</span>
                </label>
                <label class="tz-theme-option">
                    <input type="radio" name="theme-mobile" value="dark" aria-label="تم تیره">
                    <i class="fa-solid fa-moon" aria-hidden="true"></i><span>تیره</span>
                </label>
            </div>
        </fieldset>

        <div class="tz-sidebar-spacer pt-3 border-top">
            <button type="button"
                    class="nav-link w-100 d-flex align-items-center gap-2 border-0 bg-transparent text-body-secondary text-start"
                    data-sidebar-logout data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span>خروج</span>
            </button>
        </div>
    </div>
</div>
