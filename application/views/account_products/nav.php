<?php
$param = 'year=' . $data['year'] . '&amp;month=' . $data['month'];

if (isset($data['day'])) {
    $param .= '&amp;day=' . $data['day'];
}

if (isset($data['content']['id'])) {
    $total_menu_class = 'nav-link';
} else {
    $total_menu_class = 'nav-link active';
}
?>
<nav class="col-12 col-lg-12">
    <ul class="nav nav-pills">
        <li class="nav-item"><?php echo anchor('/account-products?' . $param, '종합', array('class' => $total_menu_class)); ?></li>
        <?php
        if ($data['product_list']['total']):
        foreach ($data['product_list']['list'] as $product):
            $menu_class = 'nav-link';
            if (isset($data['content']['id'])) {
                if ($product['product_id'] == $data['content']['id']) {
                    $menu_class .= ' active';
                    $list = $product['user_list'];
                    $product_type = get_product_type_name($product['product_type']);
                }
            }
            ?>
            <li class="nav-item"><?php echo anchor('/account-products/view/' . $product['product_id'] . '?' . $param, $product['product_name'], array('class' => $menu_class)); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</nav>