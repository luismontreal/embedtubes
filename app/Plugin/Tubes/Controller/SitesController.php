<?php

App::uses('TubesAppController', 'Tubes.Controller');

class SitesController extends TubesAppController {
  
/**
 * Name
 *
 * @var string
 * @access public
 */
  public $name = 'Sites';
  
  /**
 * Components
 *
 * @var array
 * @access public
 */
  public $components = array(		
		'Search.Prg' => array(
			'presetForm' => array(
				'paramType' => 'querystring',
			),
			'commonProcess' => array(
				'paramType' => 'querystring',
				'filterEmpty' => true,
			),
		),
	);
  
  /**
 * Models used by the Controller
 *
 * @var array
 * @access public
 */
	//public $uses = array('Tubes.Video');

  /**
 * Admin index
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		$this->set('title_for_layout', __d('croogo', 'Sites'));
		$this->Prg->commonProcess();
		$this->Site->recursive = 0;		

		$sites = $this->paginate();					
		$this->set(compact('sites'));
	}

/**
 * Admin edit
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_edit($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Site'));
		
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Site'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Site->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Site has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Site could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Site->read(null, $id);
		}				
		//hardcode admin_edit so it takes admin_form check CroogoAppCrontroller.php
		$this->render('admin_edit');
	}
	
/**
 * Admin add
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_add($id = null) {
		$this->set('title_for_layout', __d('croogo', 'Edit Site'));
		
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__d('croogo', 'Invalid Site'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Site->save($this->request->data)) {
				$this->Session->setFlash(__d('croogo', 'The Site has been saved'), 'default', array('class' => 'success'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__d('croogo', 'The Site could not be saved. Please, try again.'), 'default', array('class' => 'error'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Site->read(null, $id);
		}				
		//hardcode admin_edit so it takes admin_form check CroogoAppCrontroller.php
		$this->render('admin_edit');
	}

/**
 * Admin delete
 *
 * @param integer $id
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__d('croogo', 'Invalid id for Site'), 'default', array('class' => 'error'));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Site->delete($id)) {
			$this->Session->setFlash(__d('croogo', 'Site deleted'), 'default', array('class' => 'success'));
			$this->redirect(array('action' => 'index'));
		}
	}

}