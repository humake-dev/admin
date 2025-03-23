<?php
  if (empty($data['content'])) {
      $form_url='products/add';
  } else {
      $form_url='products/edit/'.$data['content']['id'];
  }
?>
<div class="card">
<?php echo form_open_multipart($form_url, array('class'=>'card-body')) ?>
<div class="form-group">
  <label for="p_category_id"><?php echo _('Category') ?></label>
  <?php
    $options=array(''=>_('Select Category'));

    if($data['category']['total']) {
      foreach($data['category']['list'] as $index=>$value) {
        $options[$value['id']]=$value['title'];
      }
    }

    $select=set_value('product_category_id');

    if (isset($data['content']['product_category_id'])) {
        $select=set_value('product_category_id', $data['content']['product_category_id']);
    }
    echo form_dropdown('product_category_id', $options, $select, array('id'=>'p_category_id','class'=>'form-control'));
?>
</div>
<div class="form-group">
  <label for="p_title"><?php echo _('Title') ?></label>
  <?php

  $value=set_value('title');

  if(!$value) {
    if (isset($data['content']['title'])) {
        $value=$data['content']['title'];
    }
  }

  echo form_input(array(
          'name'          => 'title',
          'id'            => 'p_title',
          'value'         => $value,
          'class'         => 'form-control'
  ));
  ?>
</div>
<div class="form-group">
  <label for="p_price"><?php echo _('Price') ?></label>
  <?php

  $price_value=set_value('price');

  if(!$price_value) {
    if (isset($data['content']['price'])) {
        $price_value=$data['content']['price'];
    }
  }

  echo form_input(array(
          'type'          => 'number',
          'name'          => 'price',
          'id'            => 'p_price',
          'value'         => $price_value,
          'class'         => 'form-control'
  ));
  ?>
</div>
<div class="form-group">
  <label for="n_picture"><?php echo _('Image') ?></labeL>
    <?php

    echo form_upload(array(
            'name'          => 'photo[]',
            'id'            => 'n_picture',
            'value'         => $value,
            'class'         => 'form-control-file'
    ));
    ?>
</div>
<?php if(!empty($data['exists_primary_product'])): ?>
  <div class="form-group">
  <div class="form-check form-check-inline">
              <label class="form-check-label">
                <?php
                  $m_checked=false;
                    if (isset($data['is_sub_product'])) {
                        if ($data['is_sub_product']) {
                            $m_checked=true;
                        }
                    }

                    echo form_checkbox(array(
                            'name'          => 'sub_product',
                            'value'         => '1',
                            'checked'       => $m_checked,
                            'class'         => 'form-check-input'
                    ));
                    ?> <?php echo _('Show At Add Primary Product') ?>
                  </label>
                </div>
</div>
<?php endif ?>
<div class="form-group">
  <?php echo form_submit('', _('Submit'), array('class'=>'btn btn-primary btn-block')) ?>
</div>
<?php echo form_close() ?>
</div>
