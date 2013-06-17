<?php

/**
 * Nodes Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Nodes.Controller.Component
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TubesComponent extends Component {


/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
           if ($controller->Node->type == 'video') {
		$controller->set('sites', $controller->Node->Video->Site->find('list'));
            }
	}

}
