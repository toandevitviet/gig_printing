<?php
class ModelGiftHello extends Model {
    public function HelloGift() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "user`"; 
        $implode = array();
        $query = $this->db->query($sql);
        return $query->row['email'];    
    }       
}
?>