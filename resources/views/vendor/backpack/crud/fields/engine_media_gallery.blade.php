@php
   $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
   $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitEngineMediaGallery';
   $field['wrapper']['data-field-name'] = $field['wrapper']['data-field-name'] ?? $field['name'];

   // Получаем текущие изображения из MediaLibrary
   $existingMedia = [];
   if ($crud->getModel() && method_exists($crud->getModel(), 'getMedia')) {
      $existingMedia = $crud->getModel()->getMedia('images');
   }
@endphp

{{-- Engine Media Gallery Field --}}
@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')

{{-- Загруженные изображения из MediaLibrary --}}
@if(count($existingMedia) > 0)
   <div class="well well-sm existing-file mb-3" id="media-gallery">
      <div class="row" id="media-items">
         @foreach($existingMedia as $media)
            <div class="col-md-3 col-sm-4 col-xs-6 mb-2 media-item" data-media-id="{{ $media->id }}"
               data-engine-id="{{ $crud->getModel()->id }}">
               <div class="position-relative">
                  <img src="{{ $media->getUrl('thumb') }}" alt="{{ $media->name }}" class="img-thumbnail img-fluid"
                     style="max-height: 150px; width: 100%; object-fit: cover;">
                  <button type="button" class="btn btn-sm btn-danger delete-media-btn position-absolute top-0 end-0 m-1"
                     title="Удалить изображение">
                     <i class="la la-trash"></i>
                  </button>
               </div>
               <small class="text-muted d-block text-truncate mt-1">{{ $media->name }}</small>
            </div>
         @endforeach
      </div>
   </div>
@else
   <div class="alert alert-info mb-3" id="no-images-msg">
      Изображений не загружено. Добавьте новые изображения ниже.
   </div>
@endif

{{-- Форма загрузки новых изображений --}}
<div class="form-group">
   <label class="form-label mb-2">Добавить новые изображения</label>
   <input name="{{ $field['name'] }}[]" type="hidden" value="">
   <div class="backstrap-file">
      <input type="file" name="{{ $field['name'] }}[]" @include('crud::fields.inc.attributes', ['default_class' => 'file_input backstrap-file-input']) multiple accept="image/*">
      <label class="backstrap-file-label" for="customFile"></label>
   </div>
</div>

{{-- HINT --}}
@if (isset($field['hint']))
   <p class="help-block">{!! $field['hint'] !!}</p>
@endif

@include('crud::fields.inc.wrapper_end')

{{-- Extra CSS and JS for this field --}}
@push('crud_fields_styles')
   @bassetBlock('backpack/crud/fields/engine-media-gallery.css')
   <style type="text/css">
      .media-gallery-container {
         border: 1px solid rgba(0, 40, 100, .12);
         border-radius: 5px;
         padding: 10px;
         margin-bottom: 10px;
      }

      .media-item {
         transition: transform 0.2s;
      }

      .media-item:hover {
         transform: scale(1.02);
      }

      .media-item .delete-media-btn {
         opacity: 0;
         transition: opacity 0.2s;
      }

      .media-item:hover .delete-media-btn {
         opacity: 1;
      }

      #media-items {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
         gap: 10px;
      }

      .backstrap-file {
         position: relative;
         display: inline-block;
         width: 100%;
         height: calc(1.5em + 0.75rem + 2px);
         margin-bottom: 0;
      }

      .backstrap-file-input {
         position: relative;
         z-index: 2;
         width: 100%;
         height: calc(1.5em + 0.75rem + 2px);
         margin: 0;
         opacity: 0;
      }

      .backstrap-file-input:focus~.backstrap-file-label {
         border-color: #acc5ea;
         box-shadow: 0 0 0 0rem rgba(70, 127, 208, 0.25);
      }

      .backstrap-file-label {
         position: absolute;
         top: 0;
         right: 0;
         left: 0;
         z-index: 1;
         height: calc(1.5em + 0.75rem + 2px);
         padding: 0.375rem 0.75rem;
         font-weight: 400;
         line-height: 1.5;
         color: #5c6873;
         background-color: #fff;
         border: 1px solid #e4e7ea;
         border-radius: 0.25rem;
         cursor: pointer;
      }

      .backstrap-file-label::after {
         position: absolute;
         top: 0;
         right: 0;
         bottom: 0;
         z-index: 3;
         display: block;
         height: calc(1.5em + 0.75rem);
         padding: 0.375rem 0.75rem;
         line-height: 1.5;
         color: #5c6873;
         content: "Выбрать файлы";
         background-color: #f0f3f9;
         border-left: inherit;
         border-radius: 0 0.25rem 0.25rem 0;
      }

      .spinner-border {
         width: 1.5rem;
         height: 1.5rem;
      }
   </style>
   @endBassetBlock
@endpush

@push('crud_fields_scripts')
   @bassetBlock('backpack/crud/fields/engine-media-gallery.js')
   <script>
      function bpFieldInitEngineMediaGallery(element) {
         var fieldName = element.attr('data-field-name');
         var fileInput = element.find("input[type=file]");
         var mediaItems = element.find('#media-items');
         var noImagesMsg = element.find('#no-images-msg');
         var mediaGallery = element.find('#media-gallery');

         // Обработчик удаления существующего изображения
         element.on('click', '.delete-media-btn', function (e) {
            e.preventDefault();

            var mediaItem = $(this).closest('.media-item');
            var mediaId = mediaItem.data('media-id');
            var engineId = mediaItem.data('engine-id');
            var btn = $(this);

            // Показываем spinner
            btn.prop('disabled', true);
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

            // Отправляем AJAX запрос на удаление
            $.ajax({
               url: '{{ route("admin.engine.delete-media") }}',
               type: 'POST',
               data: {
                  id: mediaId,
                  engine_id: engineId,
                  _token: $('meta[name="csrf-token"]').attr('content')
               },
               success: function (response) {
                  // Удаляем элемент из DOM с анимацией
                  mediaItem.fadeOut(300, function () {
                     $(this).remove();

                     // Если нет больше элементов, показываем сообщение
                     if (mediaItems.find('.media-item').length === 0) {
                        mediaGallery.hide();
                        noImagesMsg.show();
                     }
                  });
               },
               error: function (error) {
                  console.error('Error deleting media:', error);
                  alert('Ошибка при удалении изображения');
                  btn.prop('disabled', false);
                  btn.html('<i class="la la-trash"></i>');
               }
            });
         });

         // Обработчик загрузки новых изображений
         fileInput.change(function () {
            let selectedFiles = [];

            Array.from($(this)[0].files).forEach(file => {
               selectedFiles.push({ name: file.name, type: file.type })
            });

            // Сохраняем данные о выбранных файлах
            element.find('input').first().val(JSON.stringify(selectedFiles)).trigger('change');

            // Обновляем label с информацией о выбранных файлах
            let files = '';
            selectedFiles.forEach(file => {
               files += '<span class="badge mt-1 mb-1 text-bg-secondary">' + file.name + '</span> ';
            });

            let inputLabel = element.find("label.backstrap-file-label");
            inputLabel.html(files + '<span class="position-absolute end-0 top-50 translate-middle-y me-2">Выбрать файлы</span>');
            inputLabel.attr('has-selected-files', 'true');
         });

         // Скрываем сообщение "нет изображений" если они есть
         if (mediaItems.find('.media-item').length > 0) {
            noImagesMsg.hide();
         }
      }
   </script>
   @endBassetBlock
@endpush