<?php
/**
 * @author 	Gustavo Souza Gonçalves
 * @file 	View\Helper\AuthHelper.php
 * @date 	08/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class AuthHelper extends Helper
{
	public function User(){
		return $this->request->session()->read('Auth.User');
	}
}