<!doctype html>
<html lang="fa" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ترازینو، سامانه مدیریت مالی شخصی، درآمدها، هزینه‌ها و موجودی">
    <meta name="theme-color" content="#0f213a">
    <title><?= html_escape($page_title) ?> | ترازینو</title>

    <script>
        try {
            document.documentElement.setAttribute(
                'data-bs-theme',
                localStorage.getItem('tarazino-theme') === 'dark' ? 'dark' : 'light'
            );
            document.documentElement.setAttribute(
                'data-sidebar-expanded',
                localStorage.getItem('tarazino-sidebar-expanded') === 'true' ? 'true' : 'false'
            );
        } catch (error) {
            document.documentElement.setAttribute('data-sidebar-expanded', 'false');
        }
    </script>

    <script src="<?= base_url('assets/js/persian-utils.js') ?>"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/jalalidatepicker/jalalidatepicker.min.css') ?>">

    <style>
        :root {
            --bs-body-font-family: 'Vazirmatn', system-ui, sans-serif;
        }
    </style>

    <?php foreach ($page_styles as $style): ?>
        <link rel="stylesheet" href="<?= html_escape($style) ?>">
    <?php endforeach; ?>
</head>
<body class="bg-body">
