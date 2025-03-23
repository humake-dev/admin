<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once 'ExtraConnModel.php';

class IstUser extends ExtraConnModel
{
    protected $table = 'Data_Person';
    protected $p_id = 'PersonID'; // primary key
    protected $accepted_attributes = array('PersonID');
    protected $right_id1 = '000';

    public function update(array $data)
    {
        $this->pdo->where(array('PersonID' => $data['user_id']));
        $count = $this->pdo->count_all_results($this->table . ' as t');

        if (!$count) {
            return true;
        }
        
        $sql = 'UPDATE ' . $this->table . ' SET PersonFirstName=N?,PersonLastName=N? WHERE PersonID=?';
        $this->pdo->query($sql, array($data['user_name'], $data['user_name'], $data['user_id']));

        $this->insert_cardholder($data);

        return $this->affect_acu($data);
    }

    public function stop(array $data)
    {
        $this->pdo->where(array('PersonID' => $data['user_id']));
        $count = $this->pdo->count_all_results($this->table . ' as t');

        $dateObj = new DateTime('now', $this->timezone);
        $dateObj->modify('-1 Days');
        $yester_day = $dateObj->format('Ymd');
        $dateObj->modify('-1 Days');
        $yester2_day = $dateObj->format('Ymd');

        if (empty($data['start_date'])) {
            $data['start_date'] = $yester2_day;
        }

        $data['end_date'] = $yester_day;

        if (!$count) {
            $this->insert($data);
        }

        $dateObj = new DateTime('now', $this->timezone);
        $dateObj->modify('-1 Days');
        $yester_day = $dateObj->format('Ymd');

        $this->pdo->query('UPDATE ' . $this->table . ' SET PersonExpirationDate=? WHERE PersonID=?', array($data['end_date'], $data['user_id']));

        $this->insert_cardholder($data);

        return $this->affect_acu($data);
    }

    protected function insert_cardholder($data)
    {
        $this->pdo->where(array('PersonID' => $data['user_id']));
        $count = $this->pdo->count_all_results('Data_Person_Cardholder as t');

        if ($count) {
            if (!empty($data['start_date']) and !empty($data['end_date'])) {
                $this->pdo->update('Data_Person_Cardholder', array('IDRF' => $data['card_no'], 'IDValidDateStart' => str_replace('-', '', $data['start_date']), 'IDValidDateEnd' => str_replace('-', '', $data['end_date']), 'IDValidDateStartEnable' => '1', 'IDValidDateEndEnable' => '1'), array('PersonID' => $data['user_id'], 'IDIndex' => '1'));
                $this->pdo->update('Data_Person_Cardholder', array('IDValidDateStart' => str_replace('-', '', $data['start_date']), 'IDValidDateEnd' => str_replace('-', '', $data['end_date'])), array('PersonID' => $data['user_id'], 'IDIndex' => '2'));
                $this->pdo->update('Data_Person_Cardholder', array('IDValidDateStart' => str_replace('-', '', $data['start_date']), 'IDValidDateEnd' => str_replace('-', '', $data['end_date'])), array('PersonID' => $data['user_id'], 'IDIndex' => '3'));
            } else {
                $this->pdo->update('Data_Person_Cardholder', array('IDRF' => $data['card_no']), array('PersonID' => $data['user_id'], 'IDIndex' => '1'));
            }
        } else {
            $i_data = array('PersonID' => $data['user_id'],
                'IDIndex' => '1',
                'IDRF' => $data['card_no'],
                'IDRFUSE' => '1',
                'IDPassword' => '0000',
                'IDAccessRightID1' => $this->right_id1,
                'IDType' => '0',
                'IDValidDateStart' => str_replace('-', '', $data['start_date']),
                'IDValidDateEnd' => str_replace('-', '', $data['end_date']),
                'IDValidDateStartEnable' => '1',
                'IDValidDateEndEnable' => '1',
                'IDCreditEnable' => '0',
                'IDCredit' => '0',
                'IDModeEnable' => '0',
                'IDMode' => '0',
                'IDStop' => '0',
                'IDOutputEnable' => '0',
                'IDOutputID' => '0',
                'IDAccessLevel' => '3',
                'IDAccessModeLevel' => '3',
                'IDAccessPINLevel' => '3',
                'IDAntipassbackEnable' => '0',
                'IDAntipassbackMode' => '0',
                'IDAntipassbackLevel' => '3',
                'IDAntipassbackModeLevel' => '3',
                'IDArmDisarmLevel' => '3',
                'IDVisitorLevel' => '3',
                'LastUpdateLoginID' => 'admin',
                'LastUpdateIP' => '127.0.0.1',
                'IndiAccessRightUse' => '0',
            );

            $this->pdo->insert('Data_Person_Cardholder', $i_data);

            $i_data['IDIndex'] = '2';
            $i_data['IDRF'] = '';
            $i_data['IDRFUSE'] = '0';
            $i_data['IDAccessRightID1'] = '';
            $i_data['IDStop'] = '1';
            $i_data['IDValidDateStartEnable'] = '0';
            $i_data['IDValidDateEndEnable'] = '0';
            $this->pdo->insert('Data_Person_Cardholder', $i_data);

            $i_data['IDIndex'] = '3';
            $this->pdo->insert('Data_Person_Cardholder', $i_data);
        }
    }

    public function insert(array $data)
    {
        $count = $this->get_count($data['user_id']);

        if ($count) {
            $sql = 'UPDATE ' . $this->table . ' SET PersonFirstName=N?,PersonLastName=N?,PersonRegistrationDate=?,PersonExpirationDate=? WHERE PersonID=?';
            $this->pdo->query($sql, array($data['user_name'], $data['user_name'], str_replace('-', '', $data['transaction_date']), str_replace('-', '', $data['end_date']), $data['user_id']));

            $this->insert_cardholder($data);
        } else {
            $zero = 0;
            $null = '';
            $sql = 'INSERT INTO ' . $this->table . '(
          PersonID,
          PersonLastName,
          PersonFirstName,
          CompanyID,
          DepartmentID,
          TitleID,
          CompanyName,
          DepartmentName,
          TitleName,
          PersonEmail,
          PersonOfficeNo,
          PersonHomeNo,
          PersonMobileNo,
          PersonSocialNo,
          PersonCarNo,
          PersonNationality,
          PersonAddress,
          PersonETC,
          PersonMemo,
          PersonRegistrationDate,
          PersonExpirationDate,
          LastUpdateLoginID,
          LastUpdateIP)
          VALUES(?,N?,N?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $this->pdo->query($sql, array($data['user_id'], $data['user_name'], $data['user_name'],
                $zero,
                $zero,
                $zero,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null,
                $null, $null, str_replace('-', '', $data['transaction_date']), str_replace('-', '', $data['end_date']), 'admin', '127.0.0.1',));

            $this->insert_cardholder($data);
        }

        return $this->affect_acu($data);
    }

    protected function affect_acu($data)
    {
        $this->pdo->where(array('SenderIPAddress' => $data['send_ip'], 'DestIPAddress' => $data['dest_ip']));
        $count = $this->pdo->count_all_results('Comm_Control');

        if ($count) {
            $this->pdo->update('Comm_Control', array('CommCommand' => 'RSTART'), array('DeviceID' => $data['device_id']));
        } else {
            $this->pdo->insert('Comm_Control', array('SenderIPAddress' => $data['send_ip'], 'DestIPAddress' => $data['dest_ip'], 'CommCommand' => 'RSTART', 'DestPort' => '', 'SystemWorkID' => '1', 'DeviceID' => $data['device_id']));
        }

        $this->pdo->where(array('SenderIPAddress' => $data['send_ip'], 'DestIPAddress' => $data['dest_ip'], 'DeviceID' => $data['device_id'], 'PacketData1' => $data['user_id']));
        $count2 = $this->pdo->count_all_results('Comm_Send_Packet');

        if ($count2) {
            $this->pdo->update('Comm_Send_Packet', array('PacketResult' => null, 'PacketStatus' => 'SW', 'PacketData1' => $data['user_id'], 'PacketData2' => $data['card_no']), array('SenderIPAddress' => $data['send_ip'], 'DestIPAddress' => $data['dest_ip'], 'DeviceID' => $data['device_id'], 'PacketData1' => $data['user_id']));
        } else {
            $this->pdo->insert('Comm_Send_Packet', array('SenderIPAddress' => $data['send_ip'], 'DestIPAddress' => $data['dest_ip'], 'SystemWorkID' => '1', 'DeviceID' => $data['device_id'], 'PacketCommand' => 'IDR1', 'PacketData1' => $data['user_id'], 'PacketData2' => $data['card_no'], 'PacketStatus' => 'SW'));
        }

        return true;
    }

    protected function get_content_data($id)
    {
        $this->pdo->select('p.*,dpc.IDAccessRightID1');
        $this->pdo->join('Data_Person_Cardholder as dpc', 'dpc.PersonID=p.PersonID');
        $this->pdo->where(array('p.PersonID' => $id, 'dpc.IDIndex' => 1));
        $query = $this->pdo->get($this->table . ' as p');

        return $query->row_array();
    }

    public function delete($id)
    {
        if (parent::delete($id)) {
            $this->pdo->delete('Data_Person_Cardholder', array($this->p_id => $id));
            $this->pdo->delete('Comm_Send_Packet', array('PacketData1' => $id));

            return true;
        } else {
            return false;
        }
    }
}
