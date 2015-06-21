<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Settings\Test\TestCase\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Settings\Core\Setting;

/**
 * Settings\Controller\Admin\SettingsController Test Case
 */
class SettingsControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Settings' => 'plugin.settings.settings_configurations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        Setting::clear();

        parent::tearDown();
    }

    /**
     * Test authorization
     *
     * @return void
     */
    public function testAuthorization()
    {
        // prefix
        $this->get('/admin/settings/settings/prefix');
        $this->assertRedirect('/users/login');

        // setting a wrong role_id
        $this->session(['Auth' => ['User' => ['role_id' => 2]]]);

        // prefix
        $this->get('/admin/settings/settings/prefix');
        $this->assertResponseError();
    }

    /**
     * Test prefix method
     *
     * @return void
     */
    public function testPrefixView()
    {
        $this->session(['Auth.User' => [
            'id' => 5,
            'email' => 'info@cakemanager.org',
            'role_id' => 1,
        ]]);

        Setting::write('App.Key1', 'Value1');
        Setting::write('App.Key2', 1, ['editable'=>1, 'type'=>'checkbox', 'value'=>1]);

        Setting::write('CM.Key1', 'Value1');
        Setting::write('CM.Key2', 'Value2');
        Setting::write('CM.Key3', 'Value3');

        Configure::write('Settings.Prefixes.App', 'Application');
        Configure::write('Settings.Prefixes.CM', 'CakeManager');

        $this->get('/admin/settings/settings/prefix/App');
        $this->assertResponseOk();

        $this->assertResponseContains('<form method="post" accept-charset="utf-8" action="/admin/settings/settings/prefix/App">');
        $this->assertResponseContains('<input type="hidden" name="0[id]" id="0-id" value="1">');
        $this->assertResponseContains('<div class="input text"><label for="0-value">Key1</label>');
        $this->assertResponseContains('<input type="text" name="0[value]" options="" id="0-value" value="Value1"></div>');
        $this->assertResponseContains('<input type="hidden" name="1[id]" id="1-id" value="2">');
        $this->assertResponseContains('<div class="input checkbox"><input type="hidden" name="1[value]" value="0">');
        $this->assertResponseContains('<label for="1-value"><input type="checkbox" name="1[value]" value="1" options="" checked="checked" id="1-value">Key2</label></div>');
        $this->assertResponseContains('<button type="submit">Submit</button></form>');

        $this->get('/admin/settings/settings/prefix/CM');
        $this->assertResponseOk();

        $this->assertResponseContains('<form method="post" accept-charset="utf-8" action="/admin/settings/settings/prefix/CM">');
        $this->assertResponseContains('<input type="hidden" name="0[id]" id="0-id" value="3">');
        $this->assertResponseContains('<div class="input text"><label for="0-value">Key1</label>');
        $this->assertResponseContains('<input type="text" name="0[value]" options="" id="0-value" value="Value1"></div>');
        $this->assertResponseContains('<input type="hidden" name="1[id]" id="1-id" value="4">');
        $this->assertResponseContains('<div class="input text"><label for="1-value">Key2</label>');
        $this->assertResponseContains('<input type="text" name="1[value]" options="" id="1-value" value="Value2"></div>');
        $this->assertResponseContains('<input type="hidden" name="2[id]" id="2-id" value="5">');
        $this->assertResponseContains('<div class="input text"><label for="2-value">Key3</label>');
        $this->assertResponseContains('<input type="text" name="2[value]" options="" id="2-value" value="Value3"></div>');
        $this->assertResponseContains('<button type="submit">Submit</button></form>');
    }

    /**
     * Test prefix method
     *
     * @return void
     */
    public function testPrefixPost()
    {
        $this->session(['Auth.User' => [
            'id' => 5,
            'email' => 'info@cakemanager.org',
            'role_id' => 1,
        ]]);

        Setting::write('App.Key1', 'Value1');
        Setting::write('App.Key2', 'Value2');

        Configure::write('Settings.Prefixes.App', 'Application');

        $data = [
            0 => [
                'id' => '1',
                'value' => 'Value1Modified'
            ],
            1 => [
                'id' => '2',
                'value' => 'Value2Modified'
            ],
        ];

        $this->assertEquals('Value1', Setting::read('App.Key1'));
        $this->assertEquals('Value2', Setting::read('App.Key2'));

        $this->post('/admin/settings/settings/prefix/App', $data);
        $this->assertResponseSuccess();

        $this->assertEquals('Value1Modified', Setting::read('App.Key1'));
        $this->assertEquals('Value2Modified', Setting::read('App.Key2'));
    }
}
