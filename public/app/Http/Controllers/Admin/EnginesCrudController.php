<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EnginesRequest;
use App\Imports\EnginesImport;
use Backpack\ImportOperation\ImportOperation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EnginesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EnginesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \RedSquirrelStudio\LaravelBackpackImportOperation\ImportOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Engine::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/engines');
        CRUD::setEntityNameStrings('двигатель', 'двигатели');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('slug')->label('Маркировка');
        CRUD::column('title')->label('Название');
        CRUD::column('price')->label('Цена');
        CRUD::column('brand')->label('Бренд');
        CRUD::column('oem')->label('OEM');

        CRUD::set('import.unique_by', 'slug');

        CRUD::set('import.columns', [
            'slug' => ['label' => 'Маркировка'],
            'title' => ['label' => 'Название'],
            'price' => ['label' => 'Цена'],
            'brand' => ['label' => 'Марка'],
            'fit_for' => ['label' => 'Совместимость'],
            'description' => ['label' => 'Описание'],
            'oem' => ['label' => 'OEM'],
        ]);

        CRUD::set('import.file_field', 'file');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EnginesRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupImportOperation()
    {
        $this->withoutPrimaryKey();
        $this->setImportHandler(EnginesImport::class);
        $this->crud->setOperationSetting('importer', EnginesImport::class);
    }
}
