<?php
App::uses('AppController', 'Controller');
/**
 * AdminEmailAccounts Controller
 *
 * @property AdminEmailAccount $AdminEmailAccount
 * @property PaginatorComponent $Paginator
 */
class AdminEmailAccountsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session','Common');
	
	public function beforeFilter()
	{
		if(!$this->Session->check('adminUser'))
		{
			$this->redirect(Router::url('/admin/', true));
		}
		else
		{
			$uid=$this->Session->read('adminUser');
			$this->loadModel('AdminLogin');
			$userres=$this->AdminLogin->find('first', array('conditions' => array('rid' => $uid)));
			$this->set('adminRes', $userres);
		}
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->AdminEmailAccount->recursive = 0;
		if(isset($_GET['search']) && $_GET['search'] != ''){
			$param1 = ltrim($_GET['search']," ");
		    $param = rtrim($param1," ");
			$this->set('search', $param);
			$this->Paginator->settings = array(
				'conditions' => array('OR' => array('AdminEmailAccount.email_id LIKE' => '%'.$param.'%')),
				'order' =>array('AdminEmailAccount.id' => 'DESC'),
				'limit' => 10
			);
		}else{
			$this->set('search', '');
			$this->Paginator->settings = array(
				'order' =>array('AdminEmailAccount.id' => 'DESC'),
				'limit' => 10
			);
		
		}
		$this->set('adminEmailAccounts', $this->Paginator->paginate());
		$this->layout="admin_dashboard";
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->AdminEmailAccount->exists($id)) {
			throw new NotFoundException(__('Invalid admin email account'));
		}
		$options = array('conditions' => array('AdminEmailAccount.' . $this->AdminEmailAccount->primaryKey => $id));
		$this->set('adminEmailAccount', $this->AdminEmailAccount->find('first', $options));
		$this->layout="admin_dashboard";
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->AdminEmailAccount->create();
			$this->request->data['AdminEmailAccount']['created_date'] = date("Y-m-d H:i:s");
			$this->request->data['AdminEmailAccount']['modified_date'] = date("Y-m-d H:i:s");
			if ($this->AdminEmailAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The admin email account has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The admin email account could not be saved. Please, try again.'));
			}
		}
		$this->layout="admin_dashboard";
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->AdminEmailAccount->exists($id)) {
			throw new NotFoundException(__('Invalid admin email account'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['AdminEmailAccount']['modified_date'] = date("Y-m-d H:i:s");
			if ($this->AdminEmailAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The admin email account has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The admin email account could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('AdminEmailAccount.' . $this->AdminEmailAccount->primaryKey => $id));
			$this->request->data = $this->AdminEmailAccount->find('first', $options);
		}
		$this->layout="admin_dashboard";
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->AdminEmailAccount->id = $id;
		if (!$this->AdminEmailAccount->exists()) {
			throw new NotFoundException(__('Invalid admin email account'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->AdminEmailAccount->delete()) {
			$this->Session->setFlash(__('The admin email account has been deleted.'));
		} else {
			$this->Session->setFlash(__('The admin email account could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	public function admin_update_status($id=null, $status=null){
		$this->AdminEmailAccount->updateAll(array('is_active' => "'$status'"),	array('id' => $id));
		echo "Status updated successfully.";
		exit;
	}
}
