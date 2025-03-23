<div id="accounts" class="container">
    <div class="row">
        <?php echo $Layout->Element('accounts/nav') ?>
        <div class="col-12">
            <?php include __DIR__ . DIRECTORY_SEPARATOR . 'search_form.php' ?>
        </div>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'article_total.php' ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'article_income.php' ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'article_refund.php' ?>
    </div>
</div>
