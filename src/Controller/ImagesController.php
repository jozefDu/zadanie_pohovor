<?php

namespace App\Controller;
class ImagesController extends AppController
{
public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Flash'); // Include the FlashComponent
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

    public function images()
    {
	$image = $this->Images->newEntity();
        if ($this->request->is('post')) {
            $image = $this->Images->patchEntity($image, $this->request->data);
            if ($this->Images->save($image)) {
                $this->Flash->success(__('Your image has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your image.'));
        }
	//$this->set('image', $image);
	$this->redirect(['action' => 'index']);
    }

    public function add()
    {
        $image = $this->Images->newEntity();
        if ($this->request->is('post')) {
            $image = $this->Images->patchEntity($image, $this->request->data);
            if ($this->Images->save($image)) {
                $this->Flash->success(__('Your image has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your image.'));
        }
        $this->set('image', $image);
    }
}
