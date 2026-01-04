<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function get_period($starttime, $endtime, $timezoneObj,$numberonly=false,$montholny=false)
{
    $starttimeObj = new DateTime($starttime, $timezoneObj);
    $endtimeObj = new DateTime($endtime, $timezoneObj);
    
    $endtimeObj->setTime(24, 0, 0);
    $diffObj = $starttimeObj->diff($endtimeObj);

    $year=$diffObj->format('%y');
    
    if ($year<1) {
        if ($diffObj->format('%m')<1) {
            if($montholny) {
                $month=1;

                if(empty($numberonly)) {
                    $month.=_('Period Month');
                }

                return $month;
            } else {
                $day=$diffObj->format('%d');

                if(empty($numberonly)) {
                    $day.=_('Day');
                }

                return $day;
            }
        } else {
            $diffObj = $starttimeObj->diff($endtimeObj);
            $month=$diffObj->format('%m');
            
            if (empty($numberonly)) {
                $month.=_('Period Month');
            }

            return $month;
        }
    } else {
        $diffObj = $starttimeObj->diff($endtimeObj);
        $month=$diffObj->format('%m');

        $month=$year*12+$month;
        
        if (empty($numberonly)) {
            $month.=_('Period Month');
        }

        return $month;
    }
}

function get_dt_format($datetime, $timezoneObj = null, $format = null)
{
    if (empty($datetime) or $datetime == '0000-00-00') {
        return _('Not Inserted');
    }

    if (is_null($format)) {
        $format = 'Y' . _('Year') . ' n' . _('Month') . ' j' . _('Day');
    }

    if (is_null($timezoneObj)) {
        $timezoneObj = new DateTimeZone('Asia/Seoul');
    }

    $dateTimeObj = new DateTime($datetime, $timezoneObj);
    /*
    $formatter = new IntlDateFormatter('ko_KR', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
    $formatter->setPattern($format);
    return $formatter->format($dateTimeObj);  */

    return $dateTimeObj->format($format);
}

function get_product_category_label($product_category_type)
{
    switch ($product_category_type) {
        case 'rent':
            return _('Rent');
        case 'rent_sw':
            return _('Rent Sw');
        case 'enroll':
            return _('Enroll');
    }
}

function get_search_label($user_search_type)
{
    switch ($user_search_type) {
        case 'phone':
            $search_label = _('Phone');
            break;
        case 'card_no':
            $search_label = _('Access Card No');
            break;
        default:
            $search_label = _('Name');
    }

    return $search_label;
}

function get_product_type_name($product_type)
{
    switch ($product_type) {
        case 'facility':
            $product_type_s = $product_type = _('Rent');
            break;
        case 'course':
            $product_type_s = _('Enroll');
            break;
        case 'other':
            $product_type_s = _('Other');
            break;
        default:
            $product_type_s = _('Product');
    }

    return $product_type_s;
}

function get_lesson_unit($lesson_type, $period_unit = null)
{
    $lesson_unit = array('M' => _('Period Month'), 'W' => _('Week'), 'D' => _('Day'));
    switch ($lesson_type) {
        case '1': // 기간제
            return $lesson_unit[$period_unit];
        case '2': // 횟수제
            break;
        case '4': // PT
            return _('Count Time');
        case '5': // GX
            return _('Count Time');
        case '3': // 쿠폰제
            return _('Count');
        default :
            return '';
    }
}

function get_hyphen_phone($phone)
{
    if (empty($phone)) {
        $phone_value = _('Not Inserted');
    } else {
        $phone_value = add_hyphen($phone);
    }

    return $phone_value;
}

function get_card_no($card_no = null, $hex_to_dex = false)
{
    if (empty($card_no)) {
        return _('Not Inserted');
    }

    return $card_no;
}

function change_enable($enable)
{
    if ($enable) {
        return '<span class="text-success">' . _('Using') . '</span>';
    } else {
        return '<span class="text-danger">' . _('Stopped') . '</span>';
    }
}

function change_use($use)
{
    if ($use) {
        return _('Use');
    } else {
        return _('Not Use');
    }
}

function display_deleted_product($type, $in_out = null)
{
    if (is_null($in_out)) {
        $param = '';
    } else {
        $param = '?in_out=' . $in_out;
    }

    switch ($type) {
        case 'course':
            $result = anchor('/accounts/view-deleted/course' . $param, _('Deleted Course'));
            break;
        case 'facility':
            $result = anchor('/accounts/view-deleted/facility' . $param, _('Deleted Facility'));
            break;
        case 'other':
            $result = anchor('/accounts/view-deleted/other' . $param, _('Deleted Other'));
            break;
        case 'product':
            $result = anchor('/accounts/view-deleted/product' . $param, _('Deleted Product'));
            break;
        default:
            $result = _('Deleted Unkown');
    }

    return $result;
}

function add_hyphen($tel)
{
    $tel = preg_replace('/[^0-9]/', '', $tel);    // 숫자 이외 제거
    if (substr($tel, 0, 2) == '02') {
        return preg_replace('/([0-9]{2})([0-9]{3,4})([0-9]{4})$/', '\\1-\\2-\\3', $tel);
    } elseif (strlen($tel) == '8' && (substr($tel, 0, 2) == '15' || substr($tel, 0, 2) == '16' || substr($tel, 0, 2) == '18')) {
        // 지능망 번호이면
        return preg_replace('/([0-9]{4})([0-9]{4})$/', '\\1-\\2', $tel);
    } else {
        return preg_replace('/([0-9]{3})([0-9]{3,4})([0-9]{4})$/', '\\1-\\2-\\3', $tel);
    }
}

function change_day_name($name)
{
    switch ($name) {
        case 'Monday':
            $new_name = _('Monday');
            break;
        case 'Tuesday':
            $new_name = _('Tuesday');
            break;
        case 'Wednesday':
            $new_name = _('Wednesday');
            break;
        case 'Thursday':
            $new_name = _('Thursday');
            break;
        case 'Friday':
            $new_name = _('Friday');
            break;
        case 'Saturday':
            $new_name = _('Saturday');
            break;
        case 'Sunday':
            $new_name = _('Sunday');
            break;
    }

    return $new_name;
}

function decamelize($word)
{
    return $word = preg_replace_callback(
        '/(^|[a-z])([A-Z])/',
        function ($m) {
            return strtolower(strlen($m[1]) ? "$m[1]_$m[2]" : "$m[2]");
        },
        $word
    );
}

function camelize($word)
{
    return $word = preg_replace_callback(
        '/(^|_)([a-z])/',
        function ($m) {
            return strtoupper("$m[2]");
        },
        $word
    );
}

function valid_date($date)
{
    if ($date == '0000-00-00') {
        return false;
    }

    $date_a = explode('-', $date);
    if (!is_array($date_a)) {
        return false;
    }

    if (count($date_a) < 3) {
        return false;
    }

    return checkdate($date_a[1], $date_a[2], $date_a[0]);
}

// DB요일필드를 문자열 요일 필드로, 0123456 => 일월화수목금토
function dowtostr($dow)
{
    if (empty($dow) or strlen($dow) >= 7) {
        return '전요일';
    }

    $dow = str_replace('0', _('Simple Sunday'), $dow);
    $dow = str_replace('1', _('Simple Monday'), $dow);
    $dow = str_replace('2', _('Simple Tuesday'), $dow);
    $dow = str_replace('3', _('Simple Wednesday'), $dow);
    $dow = str_replace('4', _('Simple Thursday'), $dow);
    $dow = str_replace('5', _('Simple Friday'), $dow);
    $dow = str_replace('6', _('Simple Saturday'), $dow);

    return implode(',', str_split($dow, 3)); // 유니코드는 3바이트
}

function get_nav_class($method, $get_type, $default_method)
{
    $nav_class = 'nav-link';

    if ($get_type) {
        if (in_array($method,array('index','view','edit','delete'))) {
            if (in_array($get_type, array('index', 'view','edit', 'delete'))) {
                $nav_class .= ' active';
            }
        } else {
            if ($get_type == $method) {
                $nav_class .= ' active';
            }
        }
    } else {
        if (is_array($default_method)) {
            if (in_array($method, $default_method)) {
                $nav_class .= ' active';
            }
        } else {
            if (in_array($method,array('index','view','edit','delete'))) {
                if (in_array($default_method, array('index', 'view', 'delete'))) {
                    $nav_class .= ' active';
                }
            } else {
                if ($method == $default_method) {
                    $nav_class .= ' active';
                }
            }
        }
    }

    return $nav_class;
}

function display_gender($gender)
{
    switch ($gender) {
        case 0:
            $gender_s = _('Female');
            break;
        case 1:
            $gender_s = _('Male');
            break;
        case 2:
            $gender_s = _('Unisex');
            break;
        default:
            $gender_s = _('Not Insert');
    }

    return $gender_s;
}

function display_send_type($type)
{
    switch ($type) {
        case 'push':
            $str = _('Push');
            break;
        case 'wapos':
            $str = _('Use Push IF Available,or SMS');
            break;
        default:
            $str = _('SMS');
    }

    return $str;
}

function display_message_type($type)
{
    switch (strtolower($type)) {
        case 'sms':
            $str = _('SMS');
            break;
        case 'mms':
            $str = _('MMS');
            break;
        case 'lms':
            $str = _('LMS');
            break;
        default:
            $str = _('Push');
    }

    return $str;
}

// 한국나이
function age($bY)
{
    if (strlen($bY) == 2) {
        $bY = substr($bY, 0, 2);
        $cY = substr(date('Y'), 2, 2);
        $bY = ($bY > $cY) ? "19$bY-01-01" : "20$bY-01-01";
    }
    $bY = date('Y', strtotime($bY));

    return date('Y') - $bY + 1;
}

function getPhotoPath($folderName, $branch_id, $fileName, $thumb = null)
{
    // 이미지가 없는 경우에는 기본 이미지를 출력
    if (empty($fileName) == true) {
        return '/assets/images/common/bg_photo_none.gif';
    }

    if (!empty($thumb)) {
        $fileName = $thumb . '_thumb_' . $fileName;
    }

    if (empty($_ENV['FOG_PROVIDER'])) {
        $type = 'local';
    } else {
        $type = $_ENV['FOG_PROVIDER'];
    }

    switch ($type) {
        case 'AzureRM':
            $img_path = 'https://' . $_ENV['AZURE_STORAGE_ACCOUNT_NAME'] . '.blob.core.windows.net/' . $_ENV['FOG_DIRECTORY'] . '/' . $folderName . '/' . $branch_id . '/' . $fileName;
            break;
        case 'AWS':
            $img_path = 'https://s3-ap-northeast-2.amazonaws.com/' . $_ENV['FOG_DIRECTORY'] . '/' . $folderName . '/' . $branch_id . '/' . $fileName;
            break;
        default:
            $img_path = '/files/' . $folderName . '/' . $branch_id . '/' . $fileName;
    }

    return $img_path;
}

function sl_str_change($string, $lenth = 3, $etc_string = null)
{
    if (strpos($string, '::') === false) {
        return $string;
    }

    $a_string = explode('::', $string);
    $etc_count = 0;
    foreach ($a_string as $index => $ss) {
        if ($index >= $lenth) {
            ++$etc_count;
            continue;
        }

        $return_a[] = $ss;
    }

    if (!empty($etc_count)) {
        if (empty($etc_string)) {
            $etc_string = sprintf(_('Etc Person %d'), $etc_count);
        }
    }

    return implode(',', $return_a) . ' ' . $etc_string;
}

function sl_message_change(array $message_users, array $message_temp_users, $display_length = 3, $etc_string = null)
{
    if (empty($message_users['total']) and empty($message_temp_users['total'])) {
        return '';
    }

    $return_a = array();

    if (!empty($message_users['total'])) {
        foreach ($message_users['list'] as $index => $value) {
            if ($index >= $display_length) {
                continue;
            }

            $return_a[] = $value['name'];
        }
    }

    if (count($return_a) < $display_length) {

        if (!empty($message_temp_users['total'])) {
            foreach ($message_temp_users['list'] as $index => $value) {
                if ($index >= $display_length) {
                    continue;
                }

                $return_a[] = $value['name'];
            }
        }
    }

    if ($message_users['total'] + $message_temp_users['total'] > $display_length) {
        if (empty($etc_string)) {
            $etc_string = sprintf(_('Etc Person %d'), $message_users['total'] + $message_temp_users['total'] - $display_length);
        }
    }

    return implode(',', $return_a) . ' ' . $etc_string;
}

function sl_get_thumb($url, $type = 'origin')
{
    switch ($type) {
        case 'large':
            $prefix = 'large_thumb';
            break;
        case 'medium':
            $prefix = 'medium_thumb';
            break;
        case 'small':
            $prefix = 'small_thumb';
            break;
        default:
            break;
    }

    if (isset($prefix)) {
        $pathinfo = pathinfo($url);
        $url = $pathinfo['dirname'] . '/' . $prefix . '_' . $pathinfo['basename'];
    }

    return $url;
}

function get_reservation_type($type)
{
    switch ($type) {
        case 'PT':
            $reserverion_type = _('PT');
            break;
        case 'FPT':
            $reserverion_type = _('Free PT');
            break;
        case 'OT':
            $reserverion_type = _('OT');
            break;
        case 'Counsel':
            $reserverion_type = _('Counsel');
            break;
        case 'Etc':
            $reserverion_type = _('Etc');
            break;
        default:
            $reserverion_type = _('Not Exists Type');
    }

    return $reserverion_type;
}

function get_lesson_counter($type)
{
    switch ($type) {
        case 1:
            $lesson_counter = _('Period');
            break;
        case 3:
            $lesson_counter = _('Count Quantity');
            break;
        default:
            $lesson_counter = _('Count Time');
    }

    return $lesson_counter;
}

function get_employee_status($type, $html = false)
{
    switch ($type) {
        case 'R':
            if ($html) {
                $employee_status = '<span class="text-danger">' . _('Resign') . '</span>';
            } else {
                $employee_status = _('Resign');
            }
            break;
        case 'L':
            $employee_status = _('Leave');
            break;
        default:
            if ($html) {
                $employee_status = '<span class="text-success">' . _('Holding') . '</span>';
            } else {
                $employee_status = _('Holding');
            }
    }

    return $employee_status;
}

function get_lesson_type($type)
{
    switch ($type) {
        case 1:
            $lesson_type = _('Period Type');
            break;
        case 2:
            $lesson_type = _('Count Type');
            break;
        case 3:
            $lesson_type = _('Coupon Type');
            break;
        case 4:
            $lesson_type = _('PT');
            break;
        case 5:
            $lesson_type = _('GX');
            break;
    }

    return $lesson_type;
}

function product_price($price)
{
    if ($price) {
        return number_format($price);
    } else {
        return _('Free');
    }
}

function get_eo($number)
{
    if ($number % 2 == 0) {
        return 'even';
    } else {
        return 'odd';
    }
}

function sl_active_class($className, $classonly = false)
{
    $return = false;

    $_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', $_SERVER['REQUEST_URI_PATH']);

    if (is_array($className)) {
        if (in_array($segments[1], $className)) {
            $return = true;
        }
    } else {
        if (!strcmp($segments[1], $className)) {
            $return = true;
        }
    }

    if ($return) {
        if ($classonly) {
            return ' active';
        } else {
            return 'class="active"';
        }
    } else {
        return false;
    }
}

function rent_order_anchor($text, $facility_id, $order, $options = array())
{
    $link = 'rents';
    if ($facility_id) {
        $link .= '?facility_id=' . $facility_id . '&amp;order=' . $order;
    } else {
        $link .= '?order=' . $order;
    }

    return anchor($link, $text, $options);
}

function remake_rent_status_list_by_order($list, $order)
{
    $use_a = array();
    $expire_a = array();
    $breakdown_a = array();
    $await_a = array();

    foreach ($list as $value) {
        switch ($value['status']) {
            case 'use':
                $use_a[] = $value;
                break;
            case 'expire':
                $expire_a[] = $value;
                break;
            case 'breakdown':
                $breakdown_a[] = $value;
                break;
            default:
                $await_a[] = $value;
                break;
        }
    }

    switch ($order) {
        case 'use':
            $list = array_merge($use_a, $expire_a, $breakdown_a, $await_a);
            break;
        case 'expire':
            $list = array_merge($expire_a, $use_a, $breakdown_a, $await_a);
            break;
        case 'breakdown':
            $list = array_merge($breakdown_a, $use_a, $expire_a, $await_a);
            break;
        case 'await':
            $list = array_merge($await_a, $use_a, $expire_a, $breakdown_a);
            break;
    }

    return $list;
}

function display_edit_log_field($field)
{
    switch ($field) {
        case 'commission' :
            $change_field = _('Commission');
            break;
        case 'quantity' :
            $change_field = _('Quantity');
            break;
        case 'use_quantity' :
             $change_field = _('Use Quantity');
            break;
        case 'title' :
            $change_field = _('Title');
             break;
        case 'content' :
            $change_field = _('Content');
            break;
        case 'phone' :
            $change_field = _('Phone');
            break;
        case 'complete' :
            $change_field = _('Process Complete');
            break;
        case 'question_course' :
            $change_field = _('Question Course');
            break;
        case 'manager' :
             $change_field = _('Manager');
             break;
        case 'execute_date' :
            $change_field = _('Counsel Date');
            break;
        case 'counselor' :
            $change_field = _('Counselor');
            break;
        case 'trainer_id' :
            $change_field = _('Trainer');
            break;
        case 'original_price' :
            $change_field = _('Original Price');
            break;
        case 'insert_quantity' :
            $change_field = _('Insert Quantity');
            break;
        case 'pt_serial' :
            $change_field = _('PT Serial');
            break;
        case 'start_date' :
            $change_field = _('Start Date');
            break;
        case 'end_date' :
            $change_field = _('End Date');
            break;
        case 'price' :
            $change_field = _('Price');
            break;
        case 'payment' :
            $change_field = _('Payment');
            break;
        case 'credit' :
            $change_field = _('Credit');
            break;
        case 'cash' :
            $change_field = _('Cash');
            break; 
        case 'transaction_date' :
            $change_field = _('Transaction Date');
            break;
        case 'type' :
            $change_field = _('Type');
            break;
        case 'account_category_id' :
            $change_field = _('Account Category');
            break;
        default :
            $change_field = $field;
    }

    return $change_field;
}

function display_edit_counsel_log_value($field, $value, $timezone)
{
    if (is_null($value)) {
        return '-';
    }

    $return_value = $value;

    if($field=='question_course') {
        switch($value) {
        case 'default' :
            $return_value = _('Question Default');
            break;
        case 'gold' :
            $return_value = _('Question Golf');
            break;
        case 'pt' :
            $return_value = _('Question PT');
            break;
        }
    }

    if($field=='complete') {
        if($value=='1') {
            $return_value = _('Process Complete');
        } else {
            $return_value = _('Processing');
        }
    }

    if($field=='type') {
        switch($value) {
            case 'D' : 
              $return_value=_('Counsel By App'); 
              break;
            case 'E' : 
              $return_value=_('Counsel By Interview');
              break;
            default : 
              $return_value=_('Counsel By Phone'); 
          }
    }

    return $return_value;
}

function display_edit_log_value($field, $value, $timezone)
{
    if (is_null($value)) {
        return '-';
    }

    $return_value = $value;

    if (in_array($field, array('start_date', 'end_date', 'transaction_date'))) {
        return get_dt_format($value, $timezone);
    }

    if (in_array($field, array('price', 'original_price', 'commission'))) {
        $return_value = number_format($return_value) . _('Currency');
    }

    return $return_value;
}

function get_d_quantity_format_unique($d_format) {
    if (empty($d_format)) {
        return '0';
    }

    $total_count = 0;
    $list = explode(',', $d_format);
    foreach ($list as $value) {
        $f_value = explode('||', $value);

        $total_count+=$f_value[0];
    }

    return $total_count;
}

function get_d_insert_quantity_format($d_format,$exists_other=true)
{
    if (empty($d_format)) {
        return '-';
    }

    $new_format = array();
    $list = explode(',', $d_format);

    if($exists_other) {
        foreach ($list as $value) {
            $f_value = explode('||', $value);
    
            if ($f_value[0] == 1) {
                $period = _('Period Month');
                if ($f_value[2] == 'D') {
                    $period = _('Day');
                }
    
                if (!empty($f_value[1])) {
                    $new_format[] = $f_value[1] . $period;
                }
            } else {
                if (!empty($f_value[1])) {
                    $new_format[] = $f_value[1] . _('Count Time');
                }
            }
        }

        return implode(',', $new_format);
    } else {
        $month_sum=0;
        $day_sum=0;
        $return_value='';
        foreach ($list as $value) {
            $f_value = explode('||', $value);

            if ($f_value[2] == 'D') {
                $day_sum=$day_sum+intval($f_value[1]);
            } else {
                $month_sum=$month_sum+intval($f_value[1]);
            }
        }

        if(!empty($month_sum)) {
            $return_value=$month_sum ._('Period Month');
        }

        if(!empty($day_sum)) {
            if(empty($return_value)) {
                $return_value=$day_sum._('Day');
            } else {
                $return_value.=' '.$day_sum._('Day');
            }
        }

        return $return_value;
    }
}

function fc_profit_product($product_string)
{
    $new_format = array();
    $list = explode(',', $product_string);
    foreach ($list as $value) {
        $f_value = explode('::', $value);

        if(empty($f_value[2])) {
            $period = '';
        } else {
            $period = '('.$f_value[1]._('Period Month').')';
            if ($f_value[2] == 'D') {
                $period = '('.$f_value[1]._('Day').')';
            }
        }
        
        $new_format[] = $f_value[0].$period;
    }

    return implode(',', $new_format);    
}

function get_employee_user_oo($product_string)
{
    if(empty($product_string)) {
        return false;
    }

    if(strpos($product_string,'::')===false) {
        return false;
    }

    $oo=array();
    $f_value = explode('::', $product_string);


    $period='';
    $period_c = _('Period Month');
    if ($f_value[1] == 'C') {
        $period_c = _('Count Time');
    } else {
        if ($f_value[1] == 'D') {
            $period_c = _('Day');
        }
    }

    if(!empty($f_value[0])) {
        $period=$f_value[0].$period_c;
    }

    $oo['period']=$period;

    $account='';
    if(!empty($f_value[2])) {
        $account=number_format($f_value[2])._('Currency');
    }

    $oo['account']=$account;
    $oo['transaction_date']=$f_value[3];

    return $oo;
}
