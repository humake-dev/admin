<?php

class RolePermissionsSeeder extends Seeder {

    private $table = 'role_permissions';

    public function run() {
        $this->db->truncate($this->table);


        $this->timezone = new DateTimeZone($this->config->item('time_reference'));

        $date_time_obj = new DateTime('now', $this->timezone);
        $this->now = $date_time_obj->format('Y-m-d H:i:s');

        //seed records manually
        $data_a = array(
            // role_id 1 = 전체관리자
            array('role_id' => 1,'permission_id' => 1,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 5,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 9,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 13,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 17,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 21,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 25,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 29,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 33,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 37,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 41,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 45,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 49,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 53,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 57,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 65,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 69,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 73,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 77,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 99,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 1,'permission_id' => 100,'created_at'=>$this->now,'updated_at'=>$this->now),

            
            // role_id 2 = 센터관리자            
            array('role_id' => 2,'permission_id' => 1,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 5,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 9,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 13,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 17,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 21,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 25,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 29,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 33,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 37,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 41,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 45,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 49,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 53,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 57,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 65,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 69,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 73,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 77,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 99,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 2,'permission_id' => 100,'created_at'=>$this->now,'updated_at'=>$this->now),

            // role_id 3 = 지점관리자 
            /*
            직원 읽기(10)
            회원 읽기, 쓰기(14,15)
            락커대여 읽기, 쓰기(30,31)
            수강신청 읽기, 쓰기(34,35)
            예약 관리(37)
            상담 관리(41)
            메세지 읽기,쓰기(46,47)
            운동복 관리(49)
            공지사항 관리(53)
            회계정보 읽기(58)
            신체지수 관리(61)
            주문 관리(73)
            영업분석및 회계분석(99)
            */      
            array('role_id' => 3,'permission_id' => 10,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 14,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 15,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 30,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 31,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 34,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 35,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 37,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 41,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 46,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 47,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 49,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 53,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 58,'created_at'=>$this->now,'updated_at'=>$this->now), 
            array('role_id' => 3,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 73,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 77,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 3,'permission_id' => 99,'created_at'=>$this->now,'updated_at'=>$this->now),

            // role_id 4 = 팀장
            /*
            직원 읽기(10)
            회원 읽기, 쓰기(14,15)
            락커대여 읽기, 쓰기(30,31)
            수강신청 읽기, 쓰기(34,35)
            예약 관리(37)
            상담 관리(41)
            메세지 읽기,쓰기(46,47)
            운동복대여 읽기,쓰기(50, 51)
            공지사항 관리(53)
            회계정보 읽기(58)
            신체지수 관리(61)
            주문 관리(73)
            */
            array('role_id' => 4,'permission_id' => 10,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 14,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 15,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 30,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 31,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 34,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 35,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 37,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 41,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 46,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 47,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 50,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 51,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 53,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 58,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 73,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 4,'permission_id' => 99,'created_at'=>$this->now,'updated_at'=>$this->now),

            // role_id 4 = FC 팀장
            /*
            직원 읽기(10)
            회원 읽기, 쓰기(14,15)
            락커대여 읽기, 쓰기(30,31)
            수강신청 읽기, 쓰기(34,35)
            예약 읽기(38)
            상담 관리(41)
            메세지 읽기,쓰기(46,47)
            운동복대여 관리49)
            공지사항 관리(53)
            회계정보 읽기(58)
            신체지수 관리(61)
            주문 관리(73)
            */
            array('role_id' => 5,'permission_id' => 10,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 14,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 15,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 30,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 31,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 34,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 35,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 38,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 41,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 46,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 47,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 49,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 53,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 58,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 73,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 5,'permission_id' => 99,'created_at'=>$this->now,'updated_at'=>$this->now),

            // role_id 6 = 직원
            /*
            회원 읽기, 쓰기(14,15)
            락커대여 읽기(30)
            수강신청 읽기(34)
            예약 읽기, 쓰기(38,39)
            상담 읽기, 쓰기(42,43)
            메세지 읽기,쓰기(46,47)
            운동복대여 읽기(50)
            신체지수 관리(61)
            */
            array('role_id' => 6,'permission_id' => 14,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 15,'created_at'=>$this->now,'updated_at'=>$this->now),   
            array('role_id' => 6,'permission_id' => 30,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 34,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 38,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 39,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 42,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 43,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 46,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 47,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 50,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 6,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),

            // role_id 7 = FC 직원 
            /*
            회원 읽기, 쓰기(14,15)
            락커대여 읽기(30)
            수강신청 읽기(34)
            예약 읽기(38)
            상담 읽기, 쓰기(42,43)
            메세지 읽기,쓰기(46,47)
            운동복대여 읽기(50)
            공지사항 일기(54)
            */
            array('role_id' => 7,'permission_id' => 14,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 15,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 30,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 34,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 38,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 42,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 43,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 46,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 47,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 50,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 54,'created_at'=>$this->now,'updated_at'=>$this->now),
            array('role_id' => 7,'permission_id' => 61,'created_at'=>$this->now,'updated_at'=>$this->now),

            /*
truncate admin_permissions;
truncate role_permissions;

UPDATE `roles` SET `title` = 'FC팀장', `description` = 'FC팀장' WHERE (`id` = '5');
UPDATE `roles` SET `title` = '직원', `description` = '직원' WHERE (`id` = '6');
UPDATE `roles` SET `title` = 'FC직원', `description` = 'FC직원', `show_list` = '1', `enable` = '1' WHERE (`id` = '7');

update admins set role_id=6 WHERE role_id=5 and is_trainer=1;
update admins set role_id=7 WHERE role_id=5 and is_trainer=0;
update admins set role_id=5 WHERE role_id=4 and is_trainer=0;
            */
        );

        foreach($data_a as $data) {
            $this->db->insert($this->table, $data);
        }

        echo PHP_EOL;
    }
}
