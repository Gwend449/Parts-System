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

        // Используем кастомный вид для редактирования (с компонентом медиа)
        CRUD::setEditView('vendor.backpack.crud.engines_edit');
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

    protected function setupShowOperation()
    {
        CRUD::column('slug')
            ->label('Маркировка')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-4']); // компактная ширина

        CRUD::column('title')
            ->label('Название')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::column('price')
            ->label('Цена')
            ->type('number')
            ->wrapper(['class' => 'form-group col-md-2']);

        CRUD::column('brand')
            ->label('Марка')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::column('fit_for')
            ->label('Совместимость')
            ->type('textarea')
            ->wrapper(['class' => 'form-group col-md-10']);

        CRUD::column('description')
            ->label('Описание')
            ->type('textarea') // текстовое поле, растягивается
            ->wrapper(['class' => 'form-group col-12 text-ho']); // растянуть на всю ширину

        CRUD::column('oem')
            ->label('OEM')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-4']);
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
        CRUD::setFromDb();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(EnginesRequest::class);
        CRUD::setFromDb();
    }

    protected function setupImportOperation()
    {
        $this->withoutPrimaryKey();
        $this->setImportHandler(EnginesImport::class);
        $this->crud->setOperationSetting('importer', EnginesImport::class);
    }

    /**
     * Удаляет медиа файл из коллекции
     * Обработчик для delete_url в поле images
     */
    public function deleteMedia(\Illuminate\Http\Request $request)
    {
        $mediaId = $request->get('id');
        $engineId = $request->get('engine_id');

        if (!$mediaId || !$engineId) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        try {
            $engine = \App\Models\Engine::findOrFail($engineId);

            // Удаляем медиа
            $deleted = $engine->deleteMedia($mediaId);

            if ($deleted) {
                // Очищаем кэш изображений после удаления
                $cacheKey = 'engine_images_' . $engineId;
                \Illuminate\Support\Facades\Cache::forget($cacheKey);

                return response()->json(['success' => true, 'message' => 'Фотография удалена']);
            }

            return response()->json(['error' => 'Media not found'], 404);
        } catch (\Exception $e) {
            \Log::error('Delete media error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Получает список медиа для модального окна в админке
     */
    public function getMediaList(\Illuminate\Http\Request $request)
    {
        $engineId = $request->get('engine_id');

        \Log::info('getMediaList request', [
            'engine_id' => $engineId,
            'user_id' => backpack_user()->id ?? null,
            'url' => $request->fullUrl()
        ]);

        if (!$engineId) {
            \Log::warning('getMediaList: engine_id is missing');
            return response()->json(['error' => 'engine_id is required'], 400);
        }

        try {
            $engine = \App\Models\Engine::findOrFail($engineId);
            $mediaList = $engine->getMediaList();

            \Log::info('getMediaList success', [
                'engine_id' => $engineId,
                'media_count' => count($mediaList)
            ]);

            return response()->json(['media' => $mediaList]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('getMediaList: Engine not found', ['engine_id' => $engineId]);
            return response()->json(['error' => 'Engine not found'], 404);
        } catch (\Exception $e) {
            \Log::error('getMediaList error: ' . $e->getMessage(), [
                'engine_id' => $engineId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
