<?php

  $user_search_type = 'name';

  if ($this->input->get_post('user_search_type')) {
      $user_search_type = $this->input->get_post('user_search_type');
  }

  echo form_open('/users/select', array('method' => 'get', 'id' => 'user_find_form'));

?>
<article id="user_profile">
  <h3><span<?php if (!empty($data['user_content'])): ?> style="display:none"<?php endif; ?>><?php echo _('Select User'); ?></span><span<?php if (empty($data['user_content'])): ?> style="display:none"<?php endif; ?>><?php echo _('User Info'); ?></span></h3>
  <div class="card">
    <div class="card-body">
      <div id="user_info" class="row"<?php if (empty($data['user_content'])): ?> style="display:none"<?php endif; ?>>
        <div class="col-12 col-xl-6">
            <?php if (!empty($data['user_content']['picture_url'])): ?>
            <a href="<?php echo getPhotoPath('user', $data['user_content']['branch_id'], $data['user_content']['picture_url']); ?>" class="simple_image">
            <?php endif; ?>
            <?php if (empty($data['user_content']['picture_url'])): ?>
            <img id="profile_photo" src="/assets/images/common/bg_photo_none.gif" width="100%" height="100%">
            <?php else: ?>
            <img id="profile_photo" src="<?php echo getPhotoPath('user', $data['user_content']['branch_id'], $data['user_content']['picture_url'], 'large'); ?>" width="100%" height="100%" />
            <?php endif; ?>
            <?php if (!empty($data['user_content']['picture_url'])): ?>
            </a>
            <?php endif; ?>
        </div>
        <div class="col-12 col-xl-6">
          <div class="row">
          <div class="col-12">
            <label><?php echo _('User Name'); ?></label>
            <p id="user_name">
            <?php if (!empty($data['user_content'])): ?>
              <?php echo $data['user_content']['name']; ?><br />
              <?php endif; ?>
            </div>
            <div class="col-12">
            <label><?php echo _('Birthday'); ?></label>
            <p id="user_address">
              <?php if (!empty($data['user_content'])): ?>
              <?php if (empty($data['user_content']['birthday'])): ?>
              <?php echo _('Not Insert'); ?>
              <?php else: ?>
              <?php echo get_dt_format($data['user_content']['birthday'], $search_data['timezone']); ?>
              <?php endif; ?>
              <?php endif; ?>
            </p>
          </div>
          </div>
        </div>
        <div class="col-12">
        <div class="row">
          <div class="col-12 col-xl-6 text-left">
              <?php if (empty($data['user_content'])): ?>
              <input type="button" id="user_select_cancel" value="<?php echo _('Cancel'); ?>" class="btn btn-danger"  style="margin-top:15px"<?php if (!empty($data['user_content'])): ?> display:none<?php endif; ?>" />
              <?php endif; ?>
          </div>        
          <div class="col-12 col-xl-6">
            <label><?php echo _('Phone'); ?></label>
            <p id="user_phone">
              <?php if (!empty($data['user_content'])): ?>
              <?php echo get_hyphen_phone($data['user_content']['phone']); ?>
              <?php endif; ?>
            </p>
          </div>
          </div>
      
        </div>
      </div>

      <div id="user_search"<?php if (!empty($data['user_content'])): ?> style="display:none"<?php endif; ?>>
        <div style="margin-bottom:20px">
          <?php if (!empty($common_data['branch']['use_access_card'])): ?>        
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="user_search_type" id="type1" value="card_no"<?php if ($user_search_type == 'card_no'): ?> checked="checked"<?php endif; ?>>
            <label for="type1" class="form-check-label"><?php echo _('Access Card No'); ?></label>
          </div>
          <?php endif; ?>
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="user_search_type" id="type3" value="name"<?php if ($user_search_type == 'name'): ?> checked="checked"<?php endif; ?>>
            <label for="type3" class="form-check-label"><?php echo _('Name'); ?></label>
          </div>
          <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" name="user_search_type" id="type2" value="phone"<?php if ($user_search_type == 'phone'): ?> checked="checked"<?php endif; ?>>
            <label for="type2" class="form-check-label"><?php echo _('Phone'); ?></label>
          </div>
        </div>
        <div id="search_select">
          <label for="u_search_word" id="search_label"><?php echo get_search_label($user_search_type); ?></label>
          <div class="input-group">
          <?php
            $value = set_value('card_no');

            echo form_input(array(
              'type' => 'search',
              'id' => 'u_search_word',
              'name' => 'card_no',
              'value' => $value,
              'class' => 'form-control',
              'autofocus' => 'autofocus',
              'required' => 'required',
            ));
            ?>
            <span class="input-group-btn">
              <input type="submit" id="search_user" class="btn btn-success" value="<?php echo _('Search'); ?>" />
            </span>
          </div>
        </div>
        <div id="user_select_list_layer">
        <?php echo $Layout->Element('users/select_table'); ?>
        </div>
      </div>
    </div>
  </div>
</article>
<?php echo form_close(); ?>
