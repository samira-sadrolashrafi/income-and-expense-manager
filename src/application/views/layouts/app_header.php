<header class="dashboard-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-3 px-3 px-xl-4 py-3">
    <div class="d-flex align-items-center gap-3 flex-grow-1">
        <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center d-lg-none"
                data-bs-toggle="offcanvas" data-bs-target="#dashboardMobileNav"
                aria-controls="dashboardMobileNav" aria-label="باز کردن منوی اصلی">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <div class="min-w-0">
            <h1 class="h5 fw-bold mb-1"><?= html_escape($app_page_title) ?></h1>
            <?php if (!empty($app_page_description)): ?>
                <p class="text-body-secondary small mb-0 d-none d-sm-block">
                    <?= html_escape($app_page_description) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="dropdown flex-shrink-0">
        <button type="button" class="btn btn-light border d-flex align-items-center gap-2 px-2 px-sm-3 py-2 text-body"
                id="tarazinoAccountMenu" data-bs-toggle="dropdown" aria-expanded="false"
                aria-label="باز کردن منوی حساب کاربری">

            <?php if ($this->session->userdata('avatar')): ?>
                <img src="<?= html_escape(base_url('uploads/avatars/' . basename($this->session->userdata('avatar')))) ?>"
                     width="40" height="40" class="rounded-circle object-fit-cover flex-shrink-0"
                     alt="عکس پروفایل <?= html_escape($app_username) ?>" loading="eager" decoding="async">
            <?php else: ?>
                <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle flex-shrink-0"
                      style="width: 40px; height: 40px;" aria-hidden="true">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </span>
            <?php endif; ?>

            <span class="d-none d-sm-flex flex-column align-items-start text-start lh-sm">
                <span class="fw-semibold small text-truncate" style="max-width: 11rem;">
                    <?= html_escape($app_username) ?>
                </span>
                <span class="text-body-secondary small mt-1">حساب کاربری</span>
            </span>
            <i class="fa-solid fa-chevron-down text-body-secondary small ms-1" aria-hidden="true"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2" aria-labelledby="tarazinoAccountMenu">
            <li><h2 class="dropdown-header small mb-0"><?= html_escape($app_username) ?></h2></li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="<?= html_escape(site_url('profile')) ?>">
                    <i class="fa-solid fa-user-gear text-body-secondary" aria-hidden="true"></i>
                    <span>پروفایل</span>
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger"
                        data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                    <span>خروج از حساب</span>
                </button>
            </li>
        </ul>
    </div>
</header>
