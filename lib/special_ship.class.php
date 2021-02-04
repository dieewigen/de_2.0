<?php
class special_ship {

	public $user_id=-1;
	public $base_hp=100;
	public $base_shield=200;
	public $base_wp_min=20;
	public $base_wp_max=25;
	
	public $ship_level=1;

    function __construct($user_id){
		$this->user_id = intval($user_id);
    }

    function get_hp_max(){
        return $this->base_hp*$this->ship_level;
	}
	
	function get_shield_max(){
		return $this->base_shield*$this->ship_level;
	}

	function get_wp_min(){
		return $this->base_wp_min*$this->ship_level;
	}

	function get_wp_max(){
		return $this->base_wp_max*$this->ship_level;
	}


}


?>