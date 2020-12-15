<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Help_Model extends CI_Model 
{


/*Search Help Center*/

 	public function fetchHelpLinks() {
 		$getmenu_subId = $this->db->get('crs_help_links')->result();
 		
 		foreach($getmenu_subId as $id_key) {
 			$link_array[] = $this->getHelpLinks($id_key->menu_id, $id_key->sub_menu_id);
 		}
 		return $link_array; 
 	}

 	public function getHelpLinks($menu_id, $sub_menu_id) {
 		$where = 'menu_id = '.$menu_id.' AND sub_menu_id = '.$sub_menu_id;
 		$this->db->select('*');
 		$this->db->from('crs_sub_menus');
 		$this->db->where($where);

 		return $this->db->get()->result();

 	}

}
