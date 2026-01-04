<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'SL_Model.php';

class Search extends SL_Model
{
    protected $table = 'users';
    protected $search_type = 'default';
    protected $search_pt = false;
    protected $all_primary=false;
    protected $all_rent=false;

    protected function set_search($count = false,$order='u.id',$desc=true)
    {
        $order=false;
        $desc='desc';

        if (isset($this->search['search_status'])) {
            switch ($this->search['search_status']) {
                case 'status5':
                    $order='ue.id';
                    $desc='asc'; 
                    break;
            }
        }

        if (empty($count)) {
            $select = 'u.*,uac.card_no,ud.token,GROUP_CONCAT(DISTINCT trainer.name) as trainer,GROUP_CONCAT(DISTINCT fc.name) as fc,r.no,
            ((SUM(if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))) * count(distinct a.id)) DIV count(*)) as pay_total,         
            if(o.stopped=1,(SELECT DATE_ADD(MAX(e.start_date),INTERVAL order_stops.stop_day_count Day) FROM order_stops WHERE order_stops.order_id=o.id AND order_stops.enable=1 AND order_stops.is_change_start_date=1 ORDER BY order_stops.id desc LIMIT 1),null) AS change_start_date,
            if(o.stopped=1,(SELECT DATE_ADD(MAX(e.end_date),INTERVAL order_stops.stop_day_count Day) FROM order_stops WHERE order_stops.order_id=o.id AND order_stops.enable=1 ORDER BY order_stops.id desc LIMIT 1),null) AS change_end_date,
            (SELECT count(*) FROM product_relations WHERE product_relations.enable=1 AND product_relations.product_relation_type_id=4 AND product_relations.product_id=p.id) as is_primary,
            c.lesson_type,c.lesson_quantity,

            if(p.id,GROUP_CONCAT(distinct(if(r.id, if(r.no=0, concat(p.title,"(미정)"),concat(p.title,"(",r.no,")")),p.title)) ORDER BY p.id desc),null) as product_name,
            
            if(o.id,GROUP_CONCAT(
                DISTINCT(
                    SELECT concat(
                        if(c.lesson_type,c.lesson_type,1),"||",IF(e.id,e.insert_quantity,if(r.id,r.insert_quantity,if(r_sws.id,r_sws.insert_quantity,""))),"||",IF(c.lesson_period_unit is not null,c.lesson_period_unit,"quantity"),"||",o.id)
                    ) ORDER BY p.id desc
                        )
                        ,null) as insert_quantity';

            if ($this->search_pt) {
                $select .= ',if(e.id,GROUP_CONCAT(
                    DISTINCT(
                        SELECT concat(e.quantity,"||",e.id)
                        ) ORDER BY e.id desc
                            )
                            ,null) as quantity';
                $select .= ',if(e.id,GROUP_CONCAT(
                                   DISTINCT(
                                    SELECT concat(e.use_quantity,"||",e.id)
                                    ) ORDER BY e.id desc
                                        )
                                        ,null) as use_quantity';
                //$select .= ',(SUM(e.quantity)) AS quantity';
                //$select .= ',(SUM(e.use_quantity)) AS use_quantity';
            }

            if($desc=='desc') {
                if(empty($order)) {
                    $order='MAX(o.transaction_date)';
                }
                $select .= ',if(e.id, MAX(e.start_date), MAX(DATE(r.start_datetime))) as start_date,
                if(e.id, MAX(e.end_date),MAX(DATE(r.end_datetime))) as end_date,
                MAX(o.transaction_date) as transaction_date'; 
            } else {
                if(empty($order)) {
                    $order='MIN(o.transaction_date)';
                }
                $select .= ',if(e.id, MIN(e.start_date), MIN(DATE(r.start_datetime))) as start_date,
                if(e.id, MIN(e.end_date),MIN(DATE(r.end_datetime))) as end_date,
                MIN(o.transaction_date) as transaction_date';
            }
            
            if (!empty($this->search['search_field'])) {
                if ($this->search['search_field']=='visit_route') {
                    $select.=',ua.visit_route,ua.id as additional_id';
                }
                
                if ($this->search['search_field']=='company') {
                    $select.=',ua.company,ua.id as additional_id';
                }
            }

            $this->pdo->select($select);
        }

        $giver_search = false;
        if (isset($this->search['search_status'])) {
            if ($this->search['search_status'] == 'status11') {
                $giver_search = true;
            }
        }

        if (empty($this->device_only)) {
            $this->pdo->join('user_devices AS ud', 'ud.user_id=u.id', 'left');
        } else {
            $this->pdo->join('user_devices AS ud', 'ud.user_id=u.id');
        }

        if (empty($giver_search)) {
            $this->pdo->join('orders as o', 'o.user_id=u.id', 'left');
        } else {
            $this->pdo->join('order_transfers AS ot', 'ot.giver_id=u.id');
            $this->pdo->join('orders as o', 'ot.order_id=o.id', 'left');
        }
        $this->pdo->join('order_products as op', 'op.order_id=o.id', 'left');
        $this->pdo->join('products as p', 'op.product_id=p.id', 'left');
        $this->pdo->join('rents as r', 'r.order_id=o.id', 'left');
        $this->pdo->join('rent_sws as r_sws', 'r_sws.order_id=o.id', 'left');
        $this->pdo->join('account_orders AS ao', 'ao.order_id=o.id', 'left');
        $this->pdo->join('accounts AS a', 'ao.account_id=a.id', 'left');
        $this->pdo->join('courses as c', 'c.product_id=p.id', 'left');
        $this->pdo->join('user_fcs AS ufc', 'ufc.user_id=u.id', 'left');
        $this->pdo->join('admins AS fc', 'ufc.fc_id=fc.id', 'left');
        $this->pdo->join('enrolls as e', 'e.order_id=o.id', 'left');
        $this->pdo->join('enroll_trainers as et', 'et.enroll_id=e.id', 'left');
        $this->pdo->join('order_ends as oe', 'oe.order_id=o.id', 'left');
        $this->pdo->join('user_access_cards as uac', 'uac.user_id=u.id', 'left');

        if (empty($this->search_pt)) {
            $this->pdo->join('user_trainers AS ut', 'ut.user_id=u.id', 'left');
            $this->pdo->join('admins AS trainer', 'ut.trainer_id=trainer.id', 'left');
        } else {
            $this->pdo->join('admins AS trainer', 'et.trainer_id=trainer.id', 'left');
            $this->pdo->where(['c.lesson_type' => 4]);
        }

        if (isset($this->push)) {
            if (empty($this->push)) {
                $this->pdo->select('u.*,ud.token');
                $this->pdo->join('user_devices as ud', 'ud.user_id=u.id', 'left');
            } else {
                $this->pdo->select('u.*,ud.token');
                $this->pdo->join('user_devices as ud', 'ud.user_id=u.id');
            }
        }        

        if (!empty($this->phone_only)) {
            $this->pdo->where('u.phone is not null and length(u.phone)>6');
        }

        if (!empty($this->wapos)) {
            $this->pdo->select('u.*,ud.token');
            $this->pdo->join('user_devices as ud', 'ud.user_id=u.id', 'left');
            $this->pdo->where('(u.phone is NOT NULL and length(u.phone)>6) OR ud.token is NOT NULL');
        }

        if(!empty($this->empty_no)) {
            $this->pdo->where(['r.no'=>0]);
        }

        if(!empty($this->not_empty_no)) {
            $this->pdo->where('r.no!=0');
        }

        if (!empty($this->fc_id) or !empty($this->trainer_id)) {
            if (!empty($this->fc_id) and !empty($this->trainer_id)) {
                if (empty($this->search_pt)) {
                    $this->pdo->where('(ut.trainer_id=' . $this->trainer_id . ' AND ufc.fc_id=' . $this->fc_id . ')', null, false);
                } else {
                    $this->pdo->where('(et.trainer_id=' . $this->trainer_id . ' AND ufc.fc_id=' . $this->fc_id . ')', null, false);
                }
            } else {
                if (!empty($this->trainer_id)) {
                    if (empty($this->search_pt)) {
                        $this->pdo->where('(ut.trainer_id=' . $this->trainer_id . ')', null, false);
                    } else {
                        $this->pdo->where('(et.trainer_id=' . $this->trainer_id . ')', null, false);
                    }
                }

                if (!empty($this->fc_id)) {
                    $this->pdo->where(['ufc.fc_id' => $this->fc_id]);
                }
            }
        } else {
            if(!empty($this->search_trainer_null)) {
                if (empty($this->search_pt)) {
                    $this->pdo->where('ut.trainer_id is null');
                } else {
                    $this->pdo->where('et.trainer_id is null');
                }
            }

            if (!empty($this->search_fc_null)) {
                $this->pdo->where('ufc.fc_id is null');
            }
        }

        if (isset($this->product_id)) {
            if (is_array($this->product_id)) {
                $product_ids = $this->product_id;
                foreach ($product_ids as $p_index => $p_product_id) {
                    if (empty($p_product_id)) {
                        unset($product_ids[$p_index]);
                        continue;
                    }
                }

                $product_id = null;
                if (!empty(count($product_ids))) {
                    $product_id = $product_ids;
                }
            } else {
                $product_id = $this->product_id;
            }

            if (!empty($product_id)) {
                // $product_id_exists = true;
                if (is_array($product_id)) {
                    if (count($product_id)) {
                        $this->pdo->where_in('op.product_id', $product_id);
                        $exists_product_id_query = 'order_products.id in('.implode(',',$product_id).')';
                    } else {
                        $product_id_exists = false;
                    }
                } else {
                    $this->pdo->where(['op.product_id' => $product_id]);
                    $exists_product_id_query = 'order_products.product_id='.$product_id;
                }

                //if ($product_id_exists) {
                    $this->pdo->where('o.enable!=0 AND oe.id is null');
                //}
                
                if($this->all_primary or $this->all_rent) {
                    $exists_product_id_query=false;
                }
            }
        }

        if (!empty($this->search['search_field'])) {
            if ($this->search['search_field'] == 'birthday') {
                $order='DATE_FORMAT(u.birthday,"%m-%d")';
                $desc = 'asc';
                switch($this->search['birthday_search_type']) {
                    case 'birthday_month' :
                        $birthday_month_obj = new DateTime($this->search['birthday_month']);                    
                        $birthday_month=$birthday_month_obj->format('m');                                                
                        $this->pdo->where('(DATE_FORMAT(u.birthday,"%m")="'.$birthday_month.'")');  
                        break;
                    case 'birthday_year' :
                        $birthday_year_obj = new DateTime($this->search['birthday_year']);
                        $birthday_year=$birthday_year_obj->format('Y');
                        $this->pdo->where('(DATE_FORMAT(u.birthday,"%Y")="'.$birthday_year.'")');
                        break;
                    default :
                        if(!empty($this->search['start_birthday'])) {
                            $start_birthday_obj = new DateTime($this->search['start_birthday']);
                            $start_birthday=$start_birthday_obj->format('Y-m-d');                            
                        }

                        if(!empty($this->search['end_birthday'])) {                        
                            $end_birthday_obj = new DateTime($this->search['end_birthday']);
                            $end_birthday=$end_birthday_obj->format('Y-m-d');                            
                        }


                        
                        if(empty($this->search['include_year'])) {
                          if(!empty($this->search['start_birthday']) and !empty($this->search['end_birthday'])) {
                            if($start_birthday_obj->format('Y')==$end_birthday_obj->format('Y')) {
                                $this->pdo->where('DATE_FORMAT(u.birthday,"%m-%d") BETWEEN DATE_FORMAT("'.$start_birthday.'","%m-%d") AND DATE_FORMAT("'.$end_birthday.'","%m-%d")');
                            } else {
                                $this->pdo->where('(DATE_FORMAT(u.birthday,"%m-%d") BETWEEN DATE_FORMAT("'.$start_birthday.'","%m-%d") AND DATE_FORMAT("2050-12-31","%m-%d")) OR (DATE_FORMAT(u.birthday,"%m-%d") BETWEEN DATE_FORMAT("1950-01-01","%m-%d") AND DATE_FORMAT("'.$end_birthday.'","%m-%d"))');
                $order='    (
        (DATE_FORMAT(u.birthday, "%m%d") + 120000 - DATE_FORMAT("'.$start_birthday.'", "%m%d"))
        % 120000
    )';

                            }
                        } else {
                            if(!empty($this->search['start_birthday'])) {
                                $this->pdo->where('DATE_FORMAT(u.birthday,"%m-%d") BETWEEN DATE_FORMAT("'.$start_birthday.'","%m-%d") AND DATE_FORMAT("2050-12-31","%m-%d")');
                            }

                            if(!empty($this->search['end_birthday'])) {
                                $this->pdo->where('DATE_FORMAT(u.birthday,"%m-%d") BETWEEN DATE_FORMAT("1950-01-01","%m-%d") AND DATE_FORMAT("'.$end_birthday.'","%m-%d")');
                            }
                        }
                            
                        } else {
                        if(!empty($this->search['start_birthday']) and !empty($this->search['end_birthday'])) {
                            $this->pdo->where('DATE_FORMAT(u.birthday,"%Y-%m-%d") BETWEEN "'.$start_birthday.'" AND "'.$end_birthday.'"');
                        } else {
                            if(!empty($this->search['start_birthday'])) {
                                $this->pdo->where('DATE_FORMAT(u.birthday,"%Y-%m-%d")>="'.$start_birthday.'"');
                            }

                            if(!empty($this->search['end_birthday'])) {
                                $this->pdo->where('DATE_FORMAT(u.birthday,"%Y-%m-%d")<="'.$end_birthday.'"');
                            }
                        }
                        }                                           
                }
                $search_birthday=true;
            } else {
                if (!empty($this->search['search_field'])) {
                    switch($this->search['search_field']) {
                        case 'card_no' :
                            $this->pdo->like('uac.' . $this->search['search_field'], trim($this->search['search_word']));
                            break;
                        case 'company' :
                            $this->pdo->join('user_additionals as ua', 'ua.user_id=u.id');
                            $this->pdo->like('ua.company',trim($this->search['search_word']));
                            break;
                        case 'visit_route' :
                            $this->pdo->join('user_additionals as ua', 'ua.user_id=u.id');
                            $this->pdo->like('ua.visit_route', trim($this->search['search_word']));
                            break;
                        default :
                            $this->pdo->like('u.' . $this->search['search_field'], trim($this->search['search_word']));
                    }
                }
            }
        }


                $payment_search = true;

                if (empty($this->search['payment_id'])) {
                    $payment_search = false;
                }

                if (isset($this->search['user_type'])) {
                    if ($this->search['user_type'] == 'free') {
                        $payment_search = false;
                    }
                }

                if (!empty($payment_search)) {
                    switch ($this->search['payment_id']) {
                        case 'status3':
                            $this->pdo->where_in('a.account_category_id', [REFUND_ENROLL, REFUND_RENT, REFUND_ORDER, REFUND_OTHER]);
                            break;
                        case 'status4':
                            $this->pdo->where('a.cash>0');
                            break;
                        case 'status5':
                            $this->pdo->where('a.credit>0');
                            break;
                    }
                }

                if (isset($this->search['search_status'])) {
                    switch ($this->search['search_status']) {
                        case 'status1':
                            $this->pdo->where(['o.re_order' => 0]);
                            $this->pdo->where('a.account_category_id=' . ADD_ENROLL . ' AND a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
                            $this->pdo->having('count(o.id)>0');
                            break;
                        case 'status2':
                            $this->pdo->where(['o.re_order' => 1]);
                            $this->pdo->where('a.account_category_id=' . ADD_ENROLL . ' AND a.transaction_date BETWEEN "' . $this->start_date . '" AND "' . $this->end_date . '"');
                            $this->pdo->having('count(o.id)>0');
                            break;
                        case 'status3':
                            $this->pdo->where('(EXISTS(SELECT * FROM order_stops INNER JOIN user_stops ON order_stops.user_stop_id=user_stops.id WHERE order_stops.order_id=o.id AND order_stops.stop_start_date>="' . $this->start_date . '" AND order_stops.stop_start_date<="' . $this->end_date . '" AND order_stops.enable=1 AND user_stops.enable=1)
                            OR EXISTS(SELECT * FROM order_stop_logs WHERE order_stop_logs.order_id=o.id AND order_stop_logs.stop_start_date>="' . $this->start_date . '" AND order_stop_logs.stop_start_date<="' . $this->end_date . '" AND order_stop_logs.enable=1)
                            )');
                            break;
                        case 'status4':
                            $this->pdo->where('(EXISTS(SELECT * FROM order_stops INNER JOIN user_stops ON order_stops.user_stop_id=user_stops.id WHERE order_stops.order_id=o.id AND order_stops.stop_end_date>="' . $this->start_date . '" AND order_stops.stop_end_date<="' . $this->end_date . '" AND order_stops.enable=1 AND user_stops.enable=1)
                            OR EXISTS(SELECT * FROM order_stop_logs WHERE order_stop_logs.order_id=o.id AND order_stop_logs.stop_end_date>="' . $this->start_date . '" AND order_stop_logs.stop_end_date<="' . $this->end_date . '" AND order_stop_logs.enable=1)
                            )');
                            break;
                        case 'status5':
                            $this->pdo->join('entrances AS ue', 'ue.user_id=u.id');
                            $this->pdo->where('date(ue.in_time)>="' . $this->start_date . '" AND date(ue.in_time)<="' . $this->end_date . '"');
                            break;
                        case 'status6':
                            $this->pdo->where_not_in('u.id', 'SELECT users.id from users INNER JOIN entrances AS ue ON ue.user_id=users.id WHERE u.branch_id=' . $this->session->userdata('branch_id') . ' AND date(ue.in_time)>="' . $this->start_date . '" AND date(ue.in_time)<="' . $this->end_date . '"', false);
                            $this->pdo->where('EXISTS(SELECT * FROM users INNER JOIN orders ON users.id=orders.user_id INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN order_products ON order_products.order_id=orders.id INNER JOIN product_relations ON product_relations.product_id=order_products.product_id WHERE u.id=users.id AND orders.enable=1 AND product_relations.product_relation_type_id=4 AND (enrolls.end_date>="' . $this->start_date . '" AND enrolls.start_date<="' . $this->end_date . '"))');                            
                            break;
                        case 'status7':
                            $this->pdo->where('e.start_date>="' . $this->start_date . '" AND e.start_date<="' . $this->end_date . '"');
                            if ($desc=='desc') {
                                $order='MAX(e.start_date)';
                            } else {
                                $order='MIN(e.start_date)';
                            }                                      
                            break;
                        case 'status8':
                            $os_join = true;
                            $this->pdo->join('order_stops AS os', 'os.order_id = o.id', 'left');
                            $this->pdo->where('if(o.stopped=1 AND os.enable=1,DATE_ADD(e.end_date, INTERVAL os.stop_day_count DAY),e.end_date)>="' . $this->start_date . '" AND if(o.stopped=1 AND os.enable=1,DATE_ADD(e.end_date, INTERVAL os.stop_day_count DAY),e.end_date)<="' . $this->end_date . '"',null,false);
                            
                            if (empty($exists_product_id_query)) {
                                $exists_query='NOT EXISTS(SELECT * FROM orders INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id LEFT JOIN order_stops ON order_stops.order_id=orders.id WHERE orders.user_id=u.id AND courses.lesson_type=c.lesson_type AND orders.enable=1 AND if(orders.stopped=1 AND order_stops.enable=1, DATE_ADD(enrolls.end_date, INTERVAL order_stops.stop_day_count DAY),enrolls.end_date)>=DATE_ADD("' . $this->end_date . '", INTERVAL 1 DAY))';
                            } else {
                             //   $exists_query='NOT EXISTS(SELECT * FROM orders INNER JOIN order_products INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id  LEFT JOIN order_stops ON order_stops.order_id=orders.id WHERE orders.user_id=u.id AND courses.lesson_type=c.lesson_type AND orders.enable=1 AND if(orders.stopped=1 AND order_stops.enable=1, DATE_ADD(enrolls.end_date, INTERVAL order_stops.stop_day_count DAY),enrolls.end_date)>=DATE_ADD("' . $this->end_date . '", INTERVAL 1 DAY))';
                                $exists_query='NOT EXISTS(SELECT * FROM orders INNER JOIN enrolls ON enrolls.order_id=orders.id INNER JOIN courses ON enrolls.course_id=courses.id INNER JOIN order_products ON order_products.order_id=orders.id LEFT JOIN order_stops ON order_stops.order_id=orders.id WHERE orders.user_id=u.id AND courses.lesson_type=c.lesson_type AND orders.enable=1 AND '.$exists_product_id_query.' AND if(orders.stopped=1 AND order_stops.enable=1, DATE_ADD(enrolls.end_date, INTERVAL order_stops.stop_day_count DAY),enrolls.end_date)>=DATE_ADD("' . $this->end_date . '", INTERVAL 1 DAY))';
                            }

                            $this->pdo->where($exists_query, null, false);

                            if ($desc=='desc') {
                                $order='MAX(e.end_date)';
                            } else {
                                $order='MIN(e.end_date)';
                            }                         
                            break;
                        case 'status9':
                            $this->pdo->where('date(r.start_datetime)>="' . $this->start_date . '" AND date(r.start_datetime)<="' . $this->end_date . '"');
                            if ($desc=='desc') {
                                $order='MAX(r.start_datetime)';
                            } else {
                                $order='MIN(r.start_datetime)';
                            }         
                            break;
                        case 'status10':
                            if (empty($count)) {
                                $this->pdo->select($select.',if(p.id,GROUP_CONCAT(distinct(concat(p.title,"(",IF(r.no!=0,concat(r.no,"번"),"미정"),")")) ORDER BY p.id desc),null) as product_name');
                            }
                            $os_join = true;
                            $this->pdo->join('order_stops AS os', 'os.order_id = o.id', 'left');
                            $this->pdo->where('if(o.stopped=1 AND os.enable=1,DATE_ADD(DATE(r.end_datetime), INTERVAL os.stop_day_count DAY),DATE(r.end_datetime))>="' . $this->start_date . '" AND if(o.stopped=1 AND os.enable=1,DATE_ADD(DATE(r.end_datetime), INTERVAL os.stop_day_count DAY),DATE(r.end_datetime))<="' . $this->end_date . '"',null,false);

                            if (empty($exists_product_id_query)) {
                                $exists_query='NOT EXISTS(SELECT * FROM orders INNER JOIN rents ON rents.order_id=orders.id INNER JOIN facilities ON rents.facility_id=facilities.id LEFT JOIN order_stops ON order_stops.order_id=orders.id WHERE orders.user_id=u.id AND orders.enable=1 AND if(orders.stopped=1 AND order_stops.enable=1,DATE_ADD(DATE(rents.end_datetime), INTERVAL order_stops.stop_day_count DAY),DATE(rents.end_datetime))>=DATE_ADD("' . $this->end_date . '", INTERVAL 1 DAY))';
                            } else {
                                $exists_query='NOT EXISTS(SELECT * FROM orders INNER JOIN rents ON rents.order_id=orders.id INNER JOIN facilities ON rents.facility_id=facilities.id INNER JOIN order_products ON order_products.order_id=orders.id LEFT JOIN order_stops ON order_stops.order_id=orders.id WHERE orders.user_id=u.id AND orders.enable=1 AND '.$exists_product_id_query.' AND if(orders.stopped=1 AND order_stops.enable=1,DATE_ADD(DATE(rents.end_datetime), INTERVAL order_stops.stop_day_count DAY),DATE(rents.end_datetime))>=DATE_ADD("' . $this->end_date . '", INTERVAL 1 DAY))';
                            }

                            $this->pdo->where($exists_query, null, false);
                            if ($desc=='desc') {
                                $order='MAX(r.end_datetime)';
                            } else {
                                $order='MIN(r.end_datetime)';
                            }                                   
                            break;
                        case 'status11':
                            $this->pdo->where('DATE(ot.created_at)>="' . $this->start_date . '" AND DATE(ot.created_at)<="' . $this->end_date . '"');
                            break;
                        case 'status12':
                            $this->pdo->join('order_transfers AS ot', 'ot.order_id=o.id');
                            $this->pdo->where('DATE(ot.created_at)>="' . $this->start_date . '" AND DATE(ot.created_at)<="' . $this->end_date . '"');
                            break;
                        case 'status13':
                            $this->pdo->where('o.transaction_date>="' . $this->start_date . '" AND o.transaction_date<="' . $this->end_date . '"');
                            break;
                    }
                }

        $this->pdo->where(['u.branch_id' => $this->session->userdata('branch_id'), 'u.enable' => 1]);
        $this->pdo->group_by('u.id');

        if (isset($this->search['user_type']) or !empty($search_birthday)) {
            if (empty($this->search_pt)) {
                if (isset($this->reference_date)) {
                    if (empty($os_join)) {
                        $this->pdo->join('order_stops AS os', 'os.order_id = o.id', 'left');
                    }
                    
                    $this->pdo->where('(
                            (
                                if(o.stopped=1,
                                if(os.is_change_start_date=1,
                                DATE_ADD(DATE(r.start_datetime), INTERVAL os.stop_day_count DAY),DATE(r.start_datetime))<="' . $this->reference_date . '" AND DATE_ADD(DATE(r.end_datetime), INTERVAL os.stop_day_count DAY)>="' . $this->reference_date . '",
                                DATE(r.start_datetime)<="' . $this->reference_date . '" AND DATE(r.end_datetime)>="' . $this->reference_date . '")
                            ) OR (
                                if(o.stopped=1,
                                if(os.is_change_start_date=1,
                                DATE_ADD(e.start_date, INTERVAL os.stop_day_count DAY),e.start_date)<="' . $this->reference_date . '" AND DATE_ADD(e.end_date, INTERVAL os.stop_day_count DAY)>="' . $this->reference_date . '",
                                e.start_date<="' . $this->reference_date . '" AND e.end_date>="' . $this->reference_date . '")
                                )
                            )
                    ');
                }
            }

            if(isset($this->search['user_type'])) {
            switch ($this->search['user_type']) {
                case 'all':
                    break;
                case 'free':
                    $this->pdo->where('(a.account_category_id!=' . ADD_COMMISSION . ' OR a.id is null) AND NOT EXISTS(SELECT * FROM order_transfers INNER JOIN account_orders ON order_transfers.order_id=account_orders.order_id INNER JOIN accounts ON accounts.id=account_orders.account_id WHERE o.id=order_transfers.order_id AND order_transfers.recipient_id=u.id AND accounts.enable=1 AND accounts.account_category_id!=' . ADD_COMMISSION . ')');

                    if (empty($this->search_pt)) {
                        $this->pdo->having('IF(
                            SUM(
                                if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))
                                )<=0
                            ,1,0)');
                    } else {
                        $this->pdo->having('if(
                            (
                                SUM(CAST(e.quantity AS SIGNED))-
                                SUM(CAST(e.use_quantity AS SIGNED))
                            )<=0
                            ,1,0)');
                    }

                    break;
                default:
                    $this->pdo->where('(a.account_category_id!=' . ADD_COMMISSION . ')');

                    if (empty($this->search_pt)) {
                        $this->pdo->having('IF(
                            SUM(
                                if(a.type="I",(a.cash+a.credit),-(a.cash+a.credit))
                                )>0
                            ,1,0)');
                    } else {
                        $this->pdo->having('if(
                            (
                                SUM(CAST(e.quantity AS SIGNED))-
                                SUM(CAST(e.use_quantity AS SIGNED))
                            )>0
                            ,1,0)');
                    }
                }
            }
        }

        if (empty($count)) {
            $order_s=$order.' '.$desc.',u.name asc';
            $this->pdo->order_by($order_s);
        }
    }

    protected function get_index_data($per_page = 1000, $page = 0, $order = null, $desc = null, $enable = true)
    {
        $this->set_search(false,$order, $desc);
        $query = $this->pdo->get($this->table . ' as u', $per_page, $page);

        return $query->result_array();
    }

    public function get_count($id = null)
    {
        $this->pdo->select('count(*) as count');
        if (isset($id)) {
            $this->pdo->where([$this->table . '.id' => $id]);

            return $this->pdo->count_all_results($this->table . ' as u');
        }

        $this->set_search(true);

        return $this->pdo->count_all_results($this->table . ' as u');
    }
}
