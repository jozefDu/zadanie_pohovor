<?php

namespace App\Controller;

use Cake\Utility\Text;
use Cake\ORM\TableRegistry;

class ImagesController extends AppController
{
public function initialize()
    {
        parent::initialize();
	$this->loadComponent('Flash'); // Include the FlashComponent
	$this->loadComponent('Image');
	$this->loadComponent('Query');
    }

    public function index()
    {
        $images = $this->Images->find('all');
        $this->set(compact('images'));
    }

    public function view($id = null)
    {
        $image = $this->Images->get($id);
        $this->set(compact('image'));
    }

public function add()
{

	$image = $this->Images->newEntity();
  if (!empty($this->request->data)) {
    if (!empty($this->request->data['photo']['name'])) {

	$file = $this->request->data['photo'];
	//debug($file);

	$ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
	$type_of_file = strtolower(strstr($file['type'], '/', true)); //get type of file

	//$arr_ext = array('jpg', 'jpeg', 'gif'); //set allowed extensions
	$arr_types = array('image'); //set allowed types

	$setNewFileName = time() . "_" . rand(000000, 999999);
	//debug($setNewFileName);

	if (in_array($type_of_file, $arr_types)) {
        //do the actual uploading of the file. First arg is the tmp name, second arg is where we are putting it
	//App::import('Component', 'Image');
        //prepare the filename for database entry 
    	$imageFileName = $setNewFileName . '.' . $ext;
	$this->Image->prepare($file['tmp_name']);
	$this->Image->crop($this->request->data['Šírka'],$this->request->data['Výška'],$this->request->data['left_position'],
$this->request->data['top_position']);//width,height,left,top,Red,Green,Blue
    	$this->Image->save(WWW_ROOT . 'img/avatar/' . $imageFileName);

	$this->Image->prepare(WWW_ROOT . 'img/avatar/' . $imageFileName);
	$this->Image->resize(100,100);//width,height,left,top,Red,Green,Blue
    	$this->Image->save(WWW_ROOT . 'img/thubnails/' . $imageFileName);

    	//debug(WWW_ROOT . '/upload/avatar/' . $setNewFileName . '.' . $ext);
    	//move_uploaded_file($file['tmp_name'], WWW_ROOT . '/upload/avatar/' . $setNewFileName . '.' . $ext);
    	} else{
	$this->Flash->error(__('this is not image.'));
	return $this->setAction('index');
	}
      }


        
        $image = $this->Images->patchEntity($image, $this->request->data);
	$date = date('Y-m-d H:i:s');
	//debug($date);
	//debug($image);
	$image->created = $date;
	$image->photo = $imageFileName;
	$image->photo_dir = WWW_ROOT . 'img/avatar/' . $setNewFileName . '.' . $ext;
         if ($this->Images->save($image)) {
                $this->Flash->success(__('Your image has been saved.'));
		return $this->setAction('index');                
         }
         $this->Flash->error(__('Unable to add your image.'));
  }
        $this->set('image', $image);
    
 }

public function query()
{
  if (!empty($this->request->data)) {
    if (!empty($this->request->data['query'])) {
	
	//ziskanie dat z textarea
	$data = $this->request->data['query'];
	$this->Query->parseAndValidate($data);
	
	//debug($this->Query->makeQuery());
	$images = $this->Query->makeQuery();
	$this->set(compact('images'));
   }
}
}
}




