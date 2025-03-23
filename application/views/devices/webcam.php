<input type="hidden" id="v_type" value="<?php echo $this->input->get('type') ?>">
<input type="hidden" id="v_id" value="<?php echo $this->input->get('id') ?>">
<div style="text-align:center">
    <div style="display:none"><video id="video" autoplay></video></div>
    <canvas id="canvas" width="372" height="372" style="border:2px solid blue;margin: 30px auto 0"></canvas>
    <div style="display:none"><img id="photo" style="width:168px;height:168px;border:1px solid green"></div>
    <div style="margin:20px;text-align:center">
    <button id="snap" class="btn btn-secondary"><?php echo _('Photo Shoot') ?></button>&nbsp;&nbsp;
    <button id="save" class="btn btn-secondary" disabled><?php echo _('Save') ?></button>&nbsp;&nbsp;
    <button id="resume" class="btn btn-secondary" disabled><?php echo _('Reset') ?></button></div>
</div>
<div id="result"></div>
