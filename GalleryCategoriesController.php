<?php
App::uses('AppController', 'Controller');
/**
 * GalleryCategories Controller
 *
 * @property GalleryCategory $GalleryCategory
 * @property PaginatorComponent $Paginator
 */
class GalleryCategoriesController extends AppController {

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
		$this->GalleryCategory->recursive = 0;
		if(isset($_GET['search']) && $_GET['search'] != ''){
			$param1 = ltrim($_GET['search']," ");
		    $param = rtrim($param1," ");
			$this->set('search', $param);
			$this->Paginator->settings = array(
				'conditions' => array('OR' => array('GalleryCategory.cat_name LIKE' => '%'.$param.'%')),
				'order' =>array('GalleryCategory.cat_id' => 'DESC'),
				'limit' => 10
			);
		}else{
			$this->set('search', '');
			$this->Paginator->settings = array(
				'order' =>array('GalleryCategory.cat_id' => 'DESC'),
				'limit' => 10
			);
		
		}
		$this->set('galleryCategories', $this->Paginator->paginate());
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
		if (!$this->GalleryCategory->exists($id)) {
			throw new NotFoundException(__('Invalid gallery category'));
		}
		$options = array('conditions' => array('GalleryCategory.' . $this->GalleryCategory->primaryKey => $id));
		$this->set('galleryCategory', $this->GalleryCategory->find('first', $options));
		$this->layout="admin_dashboard";
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->GalleryCategory->create();
			$this->request->data['GalleryCategory']['created_date'] = date("Y-m-d H:i:s");
			$this->request->data['GalleryCategory']['modified_date'] = date("Y-m-d H:i:s");
			$this->request->data['GalleryCategory']['cat_slug'] = $this->Common->generateSlug($this->request->data['GalleryCategory']['cat_name']);
			
			if($this->request->data['GalleryCategory']['cat_image']['name']!=''){
				$bannerimg= $this->request->data['GalleryCategory']['cat_image']['name'];
				//filter the image name //
				$bannerimg = time().$this->Common->cleanFileName($bannerimg);
				move_uploaded_file($this->request->data['GalleryCategory']['cat_image']['tmp_name'],WWW_ROOT.'files/gallery/'.$bannerimg);
				$this->request->data['GalleryCategory']['cat_image']=$bannerimg;
			}else{
				unset($this->request->data['GalleryCategory']['cat_image']);	
			}
			
			if ($this->GalleryCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The gallery category has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The gallery category could not be saved. Please, try again.'));
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
		if (!$this->GalleryCategory->exists($id)) {
			throw new NotFoundException(__('Invalid gallery category'));
		}
		if ($this->request->is(array('post', 'put'))) {
			
			$this->request->data['GalleryCategory']['modified_date'] = date("Y-m-d H:i:s");
			$this->request->data['GalleryCategory']['cat_slug'] = $this->Common->generateSlug($this->request->data['GalleryCategory']['cat_name']);
			
			if($this->request->data['GalleryCategory']['cat_image']['name']!=''){
				$bannerimg= $this->request->data['GalleryCategory']['cat_image']['name'];
				//filter the image name //
				$bannerimg = time().$this->Common->cleanFileName($bannerimg);
				move_uploaded_file($this->request->data['GalleryCategory']['cat_image']['tmp_name'],WWW_ROOT.'files/gallery/'.$bannerimg);
				$this->request->data['GalleryCategory']['cat_image']=$bannerimg;
			}else{
				unset($this->request->data['GalleryCategory']['cat_image']);	
			}
			
			
			if ($this->GalleryCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The gallery category has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The gallery category could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('GalleryCategory.' . $this->GalleryCategory->primaryKey => $id));
			$this->request->data = $this->GalleryCategory->find('first', $options);
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
		$this->GalleryCategory->id = $id;
		if (!$this->GalleryCategory->exists()) {
			throw new NotFoundException(__('Invalid gallery category'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->GalleryCategory->delete()) {
			$this->Session->setFlash(__('The gallery category has been deleted.'));
		} else {
			$this->Session->setFlash(__('The gallery category could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	/**
	 * admin_update_status method
	 *
	 * @throws NotFoundException
	 * @param integer $id
	 * @param integer $status 
	 * @return void
	 */
	public function admin_update_status($id=null, $status=null){
		
		$this->GalleryCategory->updateAll(array('is_active' => "'$status'"),	array('cat_id' => $id));
		
		echo "Status updated successfully.";
		exit;
	}
}
