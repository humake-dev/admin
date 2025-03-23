<?php

class TestCaseRentLimit extends CIPHPUnitTestCase
{
	public function setUp():void
    {
        $this->resetInstance();
        $this->CI->load->library('session');
        $this->CI->session->set_userdata(array('branch_id'=>1));

        $this->now=new DateTime('now');
        $this->day_period=$this->now->format('j');        
    }
    
    protected function insert_limit()
    {
        $users=array(1=>49116,2=>49117,3=>49118,4=>99666);

        for($a=1;$a<=$this->day_period;$a++) {
            $rand_key=array_rand($users,1);
            if($a<10) {
                $day='0'.$a;
            } else {
                $day=$a;
            }
            $user_id=$users[$rand_key];
            $transaction_date=$this->now->format('Y-m').'-'.$day;
            $order_id=$this->CI->Order->Insert(array('user_id'=>$user_id,'transaction_date'=>$transaction_date));
            $this->CI->OrderProduct->Insert(array('order_id'=>$order_id,'product_id'=>873));
        }
    }
    
    public function test_index()
    {
        $this->CI->load->model('Order');
        $this->CI->load->model('OrderProduct');        
        $this->CI->load->model('Enroll');
    }
}
