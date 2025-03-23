<aside class="col-12 col-lg-3">
  <div class="row">
    <h2 class="col-12<?php if(!empty($data['use_entrance_analysis'])): ?> no_display_title<?php endif ?>">일일 출석목록</h2>
  </div>
  <article class="row">
    <div class="col-12">
      <div class="card">
        <form class="card-body">
          <div class="form-group">
          <label for=""><?php echo _('Date') ?></label>
          <div class="input-group-prepend date">
              <input type="text" name="date" data-date-end-date="0d" class="form-control datepicker" value="<?php echo set_value('date', $search_data['date']) ?>">
              <div class="input-group-text">
                <span class="material-icons">date_range</span>
              </div>
          </div>
          </div>
        </form>
      </div>
    </div>
  </article>
  <article id="user_list" class="row">
    <div class="col-12">
      <table class="table table-striped table-hover">
          <colgroup>
            <col />
            <col />
            <col />           
          </colgroup>
          <thead class="thead-default">
            <tr>
              <th><?php echo _('Increment Number') ?></th>
              <th><?php echo _('User Name') ?></th>
              <th><?php echo _('In Time') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if ($data['total']):
              $params='?entrance=1';

              if ($this->input->get('page')) {
                  $params.='&page='.$this->input->get('page');
              }

              if ($this->input->get('date')) {
                  $params.='&date='.$this->input->get('date');
              }
             ?>
            <?php foreach ($data['list'] as $index=>$value): ?>
            <tr<?php if (!empty($data['content'])): ?><?php if ($data['content']['id']==$value['id']): ?> class="table-primary"<?php endif ?><?php endif ?>>           
              <td>
                <?php echo number_format($data['total']-($data['page'])-$index) ?>
              </td>
              <td>
                <?php if ($this->router->fetch_method()=='index'): ?>
                <?php echo anchor($this->router->fetch_class().'/view/'.$value['id'].$params, $value['name']) ?>
                <?php else: ?>
                <?php echo anchor($this->router->fetch_class().'/'.$this->router->fetch_method().'/'.$value['id'].$params, $value['name']) ?>
                <?php endif ?>
              </td>
              <td><?php echo get_dt_format($value['in_time'],$search_data['timezone'],'H'._('Hour').' i'._('Minute')) ?></td>            
            </tr>
            <?php endforeach ?>
            <?php else: ?>
            <tr>
              <td colspan="3" class="text-center"><?php echo _('No Data') ?></td>
            </tr>
            <?php endif ?>
          </tbody>
        </table>
      </div>
  </article>
  <?php if ($data['total']): ?>
  <?php echo $this -> pagination -> create_links() ?>
  <?php endif ?>
</aside>
