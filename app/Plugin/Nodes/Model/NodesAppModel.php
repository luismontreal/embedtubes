<?php

App::uses('AppModel', 'Model');

/**
 * Nodes App Model
 *
 * @category Nodes.Model
 * @package  Croogo.Nodes.Model
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesAppModel extends AppModel {
    public function find($type = 'first', $options = array()) {
	$options = Croogo::dispatchEvent('Model.Node.beforeFind', $this, array('options' => $options))->data['options'];
        return parent::find($type, $options);
    }

}
