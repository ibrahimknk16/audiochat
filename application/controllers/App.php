<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{

	public function index()
	{
		$data['rooms'] = $this->db->get('rooms')->result_array();
		//pre($data['rooms']);die;
		$this->load->view('index', $data);
	}

	public function room()
	{
		$roomID = $this->uri->segment(3);
		$userID = 'guest-' . createToken(8);
		$rooms = $this->db->get('rooms')->result_array();
		$room = $this->db->where('roomID', $roomID)->get('rooms')->row_array();
		$data = [
			'room' => $room,
			'rooms' => $rooms,
			'roomID' => $roomID,
			'userID' => $userID,
		];
		$this->load->view('index', $data);
	}

	public function createRandomRoom()
	{
		$room = createToken(32);
		$roomLangs = ['tr', 'en', 'de', 'fr', 'es', 'it', 'ru', 'ar', 'fa', 'zh', 'ja', 'ko'];
		$randomRoomTitle = $room . ' - ' . $roomLangs[array_rand($roomLangs)];
		$roomArray = [
			'roomID' => $room,
			'roomLang' => $roomLangs[array_rand($roomLangs)],
			'roomTitle' => $randomRoomTitle,
		];
		$this->db->insert('rooms', $roomArray);
	}
}
