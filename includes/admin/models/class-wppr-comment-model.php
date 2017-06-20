<?php
class WPPR_Comment_Model extends WPPR_Model_Abstract {

	public function get_user_review() {
		return $this->get_var( 'cwppos_show_userreview' );
	}
}
