<div class="col-12">
    <p class="summary text-right">총 
    <strong class="mark"><span id="list_count"><?php echo $data['total']; ?></span>개</strong>의 PT수강이 있습니다.</p>
    <table class="table table-striped">
        <colgroup>
            <col/>
            <col/>
            <col/>
            <col style="width:160px"/>
        </colgroup>
        <thead class="thead-default">
            <tr>
                <th><?php echo _('Course Name'); ?></th>
                <th><?php echo _('Type'); ?></th>
                <th></th>
                <th>사용처리 날짜</th>
            </tr>
        </thead>
                        <tbody>
                        <?php if ($data['total']): ?>
                            <?php foreach ($data['list'] as $enroll): ?>
                                <tr>
                                    <td><?php echo $enroll['product_name']; ?></td>
                                    <td>
                                        <?php
                                        switch ($enroll['type']) {
                                            case 'no_show':
                                                echo _('No Show');
                                                break;
                                            case 'sign':
                                                echo _('Sign');
                                                break;
                                            default:
                                                echo _('Complete');
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                    <td><?php echo date_format(new DateTime($enroll['created_at']), 'Y년 n월 j일'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5"><?php echo _('No Data'); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>