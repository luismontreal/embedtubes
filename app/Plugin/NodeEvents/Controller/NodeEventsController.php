<?php

App::uses('NodeEventsAppController', 'NodeEvents.Controller');

class NodeEventsController extends NodeEventsAppController {
  
/**
 * Name
 *
 * @var string
 * @access public
 */
  public $name = 'NodeEvents';

/**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	public $uses = array('Node');
	
/**
 * Index
 *
 * @return void
 * @access public
 */
	public function index() {
		$this->request->params['named']['type'] = 'events';

		$this->paginate['Node']['order'] = 'NodeEvent.event_date ASC';
		$this->paginate['Node']['limit'] = Configure::read('Reading.nodes_per_page');
		$this->paginate['Node']['conditions'] = array(
			'Node.status' => 1,
			'NodeEvent.event_date > NOW()',
			'OR' => array(
				'Node.visibility_roles' => '',
				'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
			),
		);
		$this->paginate['Node']['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
			'NodeEvent',
			'NodeImage'
		);
		if (isset($this->request->params['named']['type'])) {
			$type = $this->Node->Taxonomy->Vocabulary->Type->find('first', array(
				'conditions' => array(
					'Type.alias' => $this->request->params['named']['type'],
				),
				'cache' => array(
					'name' => 'type_' . $this->request->params['named']['type'],
					'config' => 'nodes_index',
				),
			));
			if (!isset($type['Type']['id'])) {
				$this->Session->setFlash(__('Invalid content type.'), 'default', array('class' => 'error'));
				$this->redirect('/');
			}
			if (isset($type['Params']['nodes_per_page'])) {
				$this->paginate['Node']['limit'] = $type['Params']['nodes_per_page'];
			}
			$this->paginate['Node']['conditions']['Node.type'] = $type['Type']['alias'];
			$this->set('title_for_layout', $type['Type']['title']);
		}

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_index_' . $this->Croogo->roleId . '_' . Configure::read('Config.language');
			if (isset($type)) {
				$cacheNamePrefix .= '_' . $type['Type']['alias'];
			}
			$this->paginate['page'] = isset($this->request->params['named']['page']) ? $this->params['named']['page'] : 1;
			$cacheName = $cacheNamePrefix . '_' . $this->request->params['named']['type'] . '_' . $this->paginate['page'] . '_' . $this->paginate['Node']['limit'];
			$cacheNamePaging = $cacheNamePrefix . '_' . $this->request->params['named']['type'] . '_' . $this->paginate['page'] . '_' . $this->paginate['Node']['limit'] . '_paging';
			$cacheConfig = 'nodes_index';
			$nodes = Cache::read($cacheName, $cacheConfig);
			if (!$nodes) {
				$nodes = $this->paginate('Node');
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->request->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->request->params['paging'] = $paging;
				$this->helpers[] = 'Paginator';
			}
		} else {
			$nodes = $this->paginate('Node');
		}

		$this->set(compact('type', 'nodes'));
		$this->_viewFallback(array(
			'index_' . $type['Type']['alias'],
		));
	}
		
/**
 * View Fallback
 *
 * @param mixed $views
 * @return string
 * @access protected
 */
	protected function _viewFallback($views) {
		if (is_string($views)) {
			$views = array($views);
		}
    
		if ($this->theme) {
			$viewPaths = App::path('View');
			foreach ($views as $view) {
				foreach ($viewPaths as $viewPath) {
					$viewPath = $viewPath . 'Themed' . DS . $this->theme . DS . 'Nodes' . DS . $view . $this->ext;
					if (file_exists($viewPath)) {
						return $this->render($viewPath);
					}
				}
			}
		}
	}

}