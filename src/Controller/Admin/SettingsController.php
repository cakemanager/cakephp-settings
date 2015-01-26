<?php

namespace Settings\Controller\Admin;

use Settings\Controller\AppController;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Network\Exception\NotFoundException;

/**
 * Settings Controller
 *
 * @property \Settings\Model\Table\SettingsTable $Settings
 */
class SettingsController extends AppController
{

    public function beforeFilter(\Cake\Event\Event $event) {
        parent::beforeFilter($event);

        $this->loadModel('Settings.Configurations');

        $this->prefixes = Configure::read('Settings.Prefixes');

        $this->Menu->area('navbar');

        foreach($this->prefixes as $prefix) {
            $this->Menu->add($prefix['alias'], [
                'url' => [
                    'action' => 'prefix', $prefix['prefix'],
                ]
            ]);
        }
    }

    public function prefix($prefix = null) {

        if (!$prefix) {
            $prefix = 'App';
        }

        if (!$this->_prefixExists($prefix)) {
            throw new NotFoundException("The prefix-setting " . $prefix . " could not be found");
        }

        $prefix = Hash::get($this->prefixes, ucfirst($prefix));

        $settings = $this->Configurations->find('all')->where([
            'name LIKE' => $prefix['prefix'] . '%',
            'editable'  => 1,
        ])->order(['weight', 'id']);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $settings = $this->Configurations->patchEntities($settings, $this->request->data);
            foreach ($settings as $setting) {
                $this->Flash->success('The settings has been saved.');

                if (!$this->Configurations->save($setting)) {
                    $this->Flash->error('The settings could not be saved. Please, try again.');
                }
            }
            return $this->redirect([]);
        }

        $this->set(compact('prefix', 'settings'));
    }

    /**
     * View method
     *
     * @param string|null $id Setting id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = null) {
        $setting = $this->Configurations->get($id, [
            'contain' => []
        ]);
        $this->set('setting', $setting);
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add() {
        $setting = $this->Configurations->newEntity($this->request->data);
        if ($this->request->is('post')) {
            if ($this->Configurations->save($setting)) {
                $this->Flash->success('The setting has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The setting could not be saved. Please, try again.');
            }
        }
        $this->set(compact('setting'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null) {
        $setting = $this->Configurations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $setting = $this->Configurations->patchEntity($setting, $this->request->data);
            if ($this->Settings->save($setting)) {
                $this->Flash->success('The setting has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The setting could not be saved. Please, try again.');
            }
        }
        $this->set(compact('setting'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Setting id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $setting = $this->Configurations->get($id);
        if ($this->Configurations->delete($setting)) {
            $this->Flash->success('The setting has been deleted.');
        } else {
            $this->Flash->error('The setting could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Checks if a prefix exists
     *
     * @param string $prefix
     * @return boolean
     */
    private function _prefixExists($prefix) {

        if (Hash::get($this->prefixes, ucfirst($prefix)) == null) {
            return false;
        }

        return true;
    }

}
