<?php
/**
 * CakeImpersonate : Impersonate Plugin
 * Copyright (c) jomweb
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) jomweb
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace CakeImpersonate\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array

     */
    public $fields = [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string', 'null' => true],
        'password' => ['type' => 'string', 'null' => true],
        'active' => ['type' => 'boolean', 'null' => true],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
    ];
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'name' => 'test-user',
            'password' => '12345678',
            'active' => true,
        ],
        [
            'id' => 2,
            'name' => 'tester',
            'password' => '12345678',
            'active' => false,
        ],
    ];
}
