<?php
/**
 * Created by PhpStorm.
 * User: challgren
 * Date: 2019-01-02
 * Time: 05:57
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
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
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
