<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= base_url('assets/js/logout.js') ?>"></script>
<script src="<?= base_url('assets/js/auto-dismiss.js') ?>"></script>
<script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
<script src="<?= base_url('assets/js/theme.js') ?>"></script>

<?php if (!empty($use_charts)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<?php endif; ?>

<script src="<?= base_url('assets/vendor/jalalidatepicker/jalalidatepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/jalali-datepicker.js') ?>"></script>

<?php if (!empty($page_scripts)): ?>
    <?php foreach ($page_scripts as $script): ?>
        <script src="<?= html_escape($script) ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
