<?php
if ($this->input->get('popup')):
echo form_open('order-admins/add');
?>
<input type="hidden" name="product_category_id" value="3" />
<div class="modal-header">
  <h2 class="modal-title"><?php echo _('SMS charge') ?></h2>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
<?php else: ?>
  <div class="container">
    <div class="row">
      <div class="col-12">

      <?php echo form_open('order-admins/add') ?>
      <input type="hidden" name="product_category_id" value="3" />
<?php endif ?>


        <?php if($data['product']['total']): ?>
        <ul style="list-style:none">
          <?php foreach($data['product']['list'] as $product): ?>
          <li><label><input type="radio" name="product_id" checked="checked" value="<?php echo $product['id'] ?>"> <?php echo $product['title'] ?></label></li>
          <?php endforeach ?>
        </ul>
        <?php endif ?>

<?php if ($this->input->get('popup')): ?>
</div>
<div class="modal-footer">
  <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close') ?></button> -->
      <input type="submit" id="submitBtn" class="btn btn-block btn-primary" value="<?php echo _('Order') ?>" />
</div>
<?php echo form_close() ?>
<?php else: ?>
    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo _('Close') ?></button>-->
      <input type="submit" id="submitBtn" class="btn btn-block btn-primary" value="<?php echo _('Order') ?>" />
      <?php echo form_close() ?>
    </div>
  </div>
</div>
<?php endif ?>
