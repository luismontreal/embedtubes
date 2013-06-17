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
                if ($controller->action == 'view') {
                    $nid = $controller->viewVars['node']['Node']['id'];
                    $videoInfo = $controller->Node->Video->findByNodeId($nid, array('Video.*', 'Site.*'));
                    $controller->viewVars['node'] = array_merge($controller->viewVars['node'], $videoInfo);
                } elseif ($controller->action == 'admin_edit' || $controller->action == 'admin_add') {
                    $controller->set('sites', $controller->Node->Video->Site->find('list'));
                }
            }
	}

}
