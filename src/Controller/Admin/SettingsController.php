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
