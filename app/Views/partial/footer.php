        <?php if (is_platform_super_admin()): ?>
        </div>
        <?php else: ?>
        </div>
    </div>
        <?php endif; ?>
</main>
</div>
    <?php if (!is_platform_super_admin()): ?></div><?php endif; ?>

<?= view('partial/confirm_modal') ?>
<?= view('partial/logout_confirm') ?>
</body>

</html>
