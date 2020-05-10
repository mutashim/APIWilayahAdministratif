<?php
header("Content-Type: application/json");

class WilayahAdministratif
{
	protected $db;
	public function __construct()
	{
		require_once "Database.php";
		$config['host'] = "localhost";
		$config['user'] = "admin";
		$config['pass'] = "admin";
		$config['name'] = "indonesia";
		$this->db = new Database($config);
		$url = "";
		
		if(isset($_SERVER['PATH_INFO']) ) {
            $url = rtrim(substr($_SERVER['PATH_INFO'],1), '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
        }
		//$this->controller = $url[0];
		//unset($url[0]);
		//call_user_func_array([$this,$this->controller], $url);
	}
	
	public function index()
	{
		showTrue(["message" => "Selamat datang di API Wilayah Administratif Indonesia."]);
	}
	public function notfound()
	{
		showFalse(400, "method not found");
	}
	public function provinces($id = null)
	{
		if($id == null){
			$data['provinces'] = $this->getProvincies();
			showTrue($data);
		}else{
			showTrue($this->getProvince($id));
		}
	}	
	public function province($id){
		$data = $this->getProvince($id);
		//$data['cities'] = $this->getRegencies($id);
		showTrue($data);
	}
	public function cities($id = null)
	{
		if($id == null){
			$data['regencies'] = $this->getRegenciesAll();
			showTrue($data);
		}else{
			$data['province'] = $this->getProvince($id);
			$data['regencies'] = $this->getRegencies($id);
			showTrue($data);
		}
	}
	public function city($id = null)
	{
		$data = $this->getRegency($id);
		//$data['districts'] = $this->getDistricts($id);
		showTrue($data);
	}
	public function regencies($id = null){
		$this->cities($id);
	}
	public function regency($id = null){
		$this->city($id);
	}
	public function districts($id = null)
	{
		if($id == null){
			showFalse(400, "parameter province required");
		}

		$data['regency'] = $this->getRegency($id);
		$data['districts'] = $this->getDistricts($id);
		showTrue($data);		
	}
	public function district($id = null)
	{
		if($id == null){
			showFalse(400, "parameter distric id required");
		}
		
		$data = $this->getDistrict($id);
		//$data['villages'] = $this->getVillages($id);
		showTrue($data);
	}
	public function villages($id = null)
	{
		if($id == null){
			showFalse(400, "parameter distric id required");
		}
		
		$data['distric'] = $this->getDistrict($id);
		$data['villages'] = $this->getVillages($id);
		showTrue($data);
		
	}
	public function village($id){
		showTrue($this->getVillage($id));
	}
	
	public function id($wilayahid = null)
	{
		if($wilayahid == null){
			showFalse(400, "parameter distric id required");
		}
		
		$data = [];	
		
		$wid = $wilayahid;
		
		if(strlen($wid) == 10){
			$data['village'] = $this->getVillage($wid);
		}
		
		if(strlen($wid) >= 6){
			$data['distric'] = $this->getDistrict(substr($wid,0,6));
		}
		
		if(strlen($wid) >= 4){
			$data['regency'] = $this->getRegency(substr($wid,0,4));
		}
		
		if(strlen($wid) >= 2){
			$data['province'] = $this->getProvince(substr($wid,0,2));
		}
		
		showTrue($data);
	}
	public function sub($id = null)
	{
		if($id == null){
			$data['provinces'] = $this->getProvincies();
		}
		
		if(strlen($id) == 2){
			$this->regencies($id);
		}
		
		if(strlen($id) == 4){
			$this->districts($id);
		}
		
		if(strlen($id) == 6){
			$this->villages($id);
		}
		
		if(strlen($id) == 10){
			$this->village($id);
		}
		
		showTrue($data);
	}
	
	
	
	function getVillages($id){
		return $this->db->query("SELECT * FROM villages WHERE district_id = '$id'")->getResult();
	}
	function getVillage($id){
		return $this->db->query("SELECT * FROM villages WHERE id = '$id'")->getRow();
	}
	function getDistricts($id){
		return $this->db->query("SELECT * FROM districts WHERE regency_id = '$id'")->getResult();
	}
	function getDistrict($id){
		return $this->db->query("SELECT * FROM districts WHERE id = '$id'")->getRow();
	}
	function getRegencies($id){
		return $this->db->query("SELECT * FROM regencies WHERE province_id = '$id'")->getResult();
	}
	function getRegenciesAll(){
		return $this->db->query("SELECT * FROM regencies")->getResult();
	}
	function getRegency($id){
		return $this->db->query("SELECT * FROM regencies WHERE id = '$id'")->getRow();
	}
	function getProvincies(){
		return $this->db->query("SELECT * FROM provinces")->getResult();
	}
	function getProvince($id){
		return $this->db->query("SELECT * FROM provinces WHERE id = '$id'")->getRow();
	}
}

function showTrue($data){
	$resp['success'] = true;
	$resp['time'] = time();
	$resp['data'] = $data;
	echo json_encode($resp);
	die;
}
function showFalse($errno, $errme){
	$resp['success'] = false;
	$resp['errno'] = $errno;
	$resp['message'] = $errme;
	echo json_encode($resp);
	die;
}