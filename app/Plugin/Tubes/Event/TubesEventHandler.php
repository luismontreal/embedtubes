<?php

/**
 * Example Event Handler
 *
 * PHP version 5
 *
 * @category Event
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class TubesEventHandler extends Object implements CakeEventListener {

/**
 * implementedEvents
 *
 * @return array
 */
	public function implementedEvents() {
           
		return array(
		    'Controller.Nodes.onPaginate' => array(
			'callable' => 'onPaginate',
			),
		    'Model.Node.beforeFind' => array(
			'callable' => 'beforeFind',
			),
		);
	}

/**
 * onAdminLoginSuccessful
 *
 * @param CakeEvent $event
 * @return void
 */
	public function onPaginate($event) {
            if($event->data['type'] == 'video') {
                $event->data['paginate']['Node']['limit'] = 50;
                $event->data['paginate']['Node']['contain'][] = 'Video';
				//Unset User
				unset($event->data['paginate']['Node']['User']);				
            }
            return $event->data;
		
	}
	
	public function beforeFind($event) {
	    if(isset($event->data['options']['conditions']['Node.type']) && 
		    $event->data['options']['conditions']['Node.type'] == 'video') {
		
		$event->data['options']['contain'][] = 'Video';
		$event->data['options']['contain'][] = 'Video.Site';
	    }
	    
            return $event->data;
	}

}
