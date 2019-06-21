<?php
/**
 * @author 	Gustavo Souza Gonçalves
 * @file 	View\Helper\TicketsHelper.php
 * @date 	22/08/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;


class TicketsHelper extends Helper
{	
	/**
	* Get Shower Type
	* @param [int] $type
	* @return [string] value
	*/
	public function getTicketShowerType($type = null){

		if (is_null($type)){
			// caso não seja banho
			return null;
		}

		return Configure::read('showerType')[$type];
	}
}