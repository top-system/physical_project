<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use TopSystem\TopAdmin\Models\DataType;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $dataType = $this->dataType('slug', 'users');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'users',
                'display_name_singular' => __('admin::seeders.data_types.user.singular'),
                'display_name_plural'   => __('admin::seeders.data_types.user.plural'),
                'icon'                  => 'admin-person',
                'model_name'            => 'TopSystem\\TopAdmin\\Models\\User',
                'policy_name'           => 'TopSystem\\TopAdmin\\Policies\\UserPolicy',
                'controller'            => 'TopSystem\\TopAdmin\\Http\\Controllers\\AdminUserController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'menus');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'menus',
                'display_name_singular' => __('admin::seeders.data_types.menu.singular'),
                'display_name_plural'   => __('admin::seeders.data_types.menu.plural'),
                'icon'                  => 'admin-list',
                'model_name'            => 'TopSystem\\TopAdmin\\Models\\Menu',
                'controller'            => '',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }

        $dataType = $this->dataType('slug', 'roles');
        if (!$dataType->exists) {
            $dataType->fill([
                'name'                  => 'roles',
                'display_name_singular' => __('admin::seeders.data_types.role.singular'),
                'display_name_plural'   => __('admin::seeders.data_types.role.plural'),
                'icon'                  => 'admin-lock',
                'model_name'            => 'TopSystem\\TopAdmin\\Models\\Role',
                'controller'            => 'TopSystem\\TopAdmin\\Http\\Controllers\\AdminRoleController',
                'generate_permissions'  => 1,
                'description'           => '',
            ])->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }
}
