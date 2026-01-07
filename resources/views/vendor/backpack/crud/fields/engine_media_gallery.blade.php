@php
   $field['wrapper'] = $field['wrapper'] ?? $field['wrapperAttributes'] ?? [];
   $field['wrapper']['data-init-function'] = $field['wrapper']['data-init-function'] ?? 'bpFieldInitEngineMediaGallery';
   $field['wrapper']['data-field-name'] = $field['wrapper']['data-field-name'] ?? $field['name'];

   // Получаем текущие изображения из MediaLibrary
   // Используем getCurrentEntry() для получения текущей записи при обновлении
   $existingMedia = [];
   $engineId = null;
   
   try {
      $entryId = $crud->getCurrentEntryId();
      
      if ($entryId !== false) {
         $entry = $crud->getEntry($entryId);
         
         if ($entry && $entry->exists && method_exists($entry, 'getMedia')) {
            $existingMedia = $entry->getMedia('images');
            $engineId = $entry->id;
         }
      }
   } catch (\Exception $e) {
      \Log::error('Error getting media in engine_media_gallery: ' . $e->getMessage());
      $existingMedia = [];
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
               data-engine-id="{{ $engineId }}">
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
   <div class="backstrap-file">
      <input type="file" name="{{ $field['name'] }}[]" @include('crud::fields.inc.attributes', ['default_class' => 'file_input backstrap-file-input']) multiple accept="image/jpeg,image/png,image/webp">
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
         var engineId = null;
         
         // Получаем engine_id из первого элемента или из скрытого поля формы
         var firstMediaItem = mediaItems.find('.media-item').first();
         if (firstMediaItem.length) {
            engineId = firstMediaItem.data('engine-id');
         } else {
            // Пытаемся получить из скрытого поля id формы
            var formIdInput = $('form input[name="id"]');
            if (formIdInput.length) {
               engineId = formIdInput.val();
            }
         }

         // Функция для обновления списка изображений
         function refreshMediaList() {
            if (!engineId) {
               // Пытаемся получить engine_id из формы
               var formIdInput = $('form input[name="id"]');
               if (formIdInput.length && formIdInput.val()) {
                  engineId = formIdInput.val();
               } else {
                  console.log('EngineMediaGallery: No engine ID found, skipping refresh');
                  return; // Нет ID, значит это создание новой записи
               }
            }

            console.log('EngineMediaGallery: Refreshing media list for engine ID:', engineId);

            $.ajax({
               url: '{{ route("admin.engine.media-list") }}',
               type: 'GET',
               data: {
                  engine_id: engineId
               },
               success: function (response) {
                  console.log('EngineMediaGallery: Received media list:', response);
                  
                  if (response.media && response.media.length > 0) {
                     // Очищаем текущий список
                     mediaItems.empty();
                     
                     // Добавляем новые изображения
                     response.media.forEach(function(media) {
                        var deleteButton = '';
                        // Показываем кнопку удаления только для изображений из MediaLibrary
                        if (media.id && media.type !== 'folder') {
                           deleteButton = '<button type="button" class="btn btn-sm btn-danger delete-media-btn position-absolute top-0 end-0 m-1" title="Удалить изображение">' +
                              '<i class="la la-trash"></i>' +
                              '</button>';
                        } else {
                           // Для изображений из папки показываем подсказку
                           deleteButton = '<span class="badge bg-info position-absolute top-0 end-0 m-1" title="Изображение из папки, удалите вручную">Папка</span>';
                        }
                        
                        var mediaItem = $('<div>')
                           .addClass('col-md-3 col-sm-4 col-xs-6 mb-2 media-item')
                           .attr('data-media-id', media.id || '')
                           .attr('data-engine-id', engineId)
                           .attr('data-media-type', media.type || 'uploaded')
                           .html(
                              '<div class="position-relative">' +
                                 '<img src="' + media.thumb + '" alt="' + media.name + '" class="img-thumbnail img-fluid" style="max-height: 150px; width: 100%; object-fit: cover;">' +
                                 deleteButton +
                              '</div>' +
                              '<small class="text-muted d-block text-truncate mt-1">' + media.name + '</small>'
                           );
                        mediaItems.append(mediaItem);
                     });
                     
                     // Показываем галерею и скрываем сообщение
                     if (!mediaGallery.length) {
                        // Создаем галерею если её нет
                        var galleryHtml = '<div class="well well-sm existing-file mb-3" id="media-gallery">' +
                           '<div class="row" id="media-items"></div>' +
                           '</div>';
                        element.find('label').after(galleryHtml);
                        mediaGallery = element.find('#media-gallery');
                        mediaItems = element.find('#media-items');
                     }
                     
                     mediaGallery.show();
                     noImagesMsg.hide();
                     
                     console.log('EngineMediaGallery: Displayed ' + response.media.length + ' image(s)');
                  } else {
                     // Нет изображений
                     mediaItems.empty();
                     if (mediaGallery.length) {
                        mediaGallery.hide();
                     }
                     noImagesMsg.show();
                     console.log('EngineMediaGallery: No images found');
                  }
               },
               error: function (xhr) {
                  console.error('EngineMediaGallery: Error loading media list:', xhr);
                  if (xhr.responseJSON && xhr.responseJSON.error) {
                     console.error('EngineMediaGallery: Error message:', xhr.responseJSON.error);
                  }
               }
            });
         }

         // Обработчик удаления существующего изображения
         element.on('click', '.delete-media-btn', function (e) {
            e.preventDefault();

            var mediaItem = $(this).closest('.media-item');
            var mediaId = mediaItem.data('media-id');
            var mediaType = mediaItem.data('media-type');
            var engineId = mediaItem.data('engine-id');
            var btn = $(this);

            // Не удаляем изображения из папки
            if (!mediaId || mediaType === 'folder') {
               alert('Это изображение из папки. Удалите его вручную из папки public/images/engines/');
               return;
            }

            // Подтверждение удаления
            if (!confirm('Вы уверены, что хотите удалить это изображение?')) {
               return;
            }

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
                  if (response.success) {
                     // Удаляем элемент из DOM с анимацией
                     mediaItem.fadeOut(300, function () {
                        $(this).remove();

                        // Если нет больше элементов, показываем сообщение
                        if (mediaItems.find('.media-item').length === 0) {
                           mediaGallery.hide();
                           noImagesMsg.show();
                        }
                     });
                  } else {
                     alert(response.message || 'Ошибка при удалении изображения');
                     btn.prop('disabled', false);
                     btn.html('<i class="la la-trash"></i>');
                  }
               },
               error: function (xhr) {
                  console.error('Error deleting media:', xhr);
                  var errorMessage = 'Ошибка при удалении изображения';
                  if (xhr.responseJSON && xhr.responseJSON.error) {
                     errorMessage = xhr.responseJSON.error;
                  }
                  alert(errorMessage);
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

         // Обновляем список изображений при загрузке страницы
         // Это гарантирует, что после редиректа после сохранения изображения будут отображены
         function initMediaRefresh() {
            var formIdInput = $('form input[name="id"]');
            if (formIdInput.length && formIdInput.val()) {
               var currentEngineId = formIdInput.val();
               
               // Обновляем engineId если он изменился
               if (currentEngineId !== engineId) {
                  engineId = currentEngineId;
               }
               
               // Обновляем список изображений через небольшую задержку
               // чтобы дать время странице полностью загрузиться
               setTimeout(function() {
                  refreshMediaList();
               }, 800);
            }
         }

         // Вызываем при загрузке страницы
         $(document).ready(function() {
            initMediaRefresh();
         });

         // Также обновляем при изменении скрытого поля id (когда Backpack устанавливает ID после создания)
         var formIdInput = $('form input[name="id"]');
         if (formIdInput.length) {
            var idObserver = new MutationObserver(function(mutations) {
               var currentId = formIdInput.val();
               if (currentId && currentId !== engineId) {
                  engineId = currentId;
                  setTimeout(function() {
                     refreshMediaList();
                  }, 500);
               }
            });
            
            idObserver.observe(formIdInput[0], { 
               attributes: true, 
               attributeFilter: ['value'],
               childList: false, 
               subtree: false 
            });
         }

         // Обновляем список после успешной отправки формы с файлами
         var form = element.closest('form');
         if (form.length) {
            form.on('submit', function(e) {
               // Проверяем, есть ли файлы для загрузки
               var hasFiles = false;
               if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                  hasFiles = true;
               }
               
               // Если есть файлы, устанавливаем флаг для обновления после редиректа
               if (hasFiles && engineId) {
                  sessionStorage.setItem('refreshEngineMedia_' + engineId, 'true');
               }
            });
         }

         // Проверяем флаг при загрузке страницы и обновляем список если нужно
         $(document).ready(function() {
            var formIdInput = $('form input[name="id"]');
            if (formIdInput.length && formIdInput.val()) {
               var currentEngineId = formIdInput.val();
               var shouldRefresh = sessionStorage.getItem('refreshEngineMedia_' + currentEngineId);
               
               if (shouldRefresh === 'true') {
                  sessionStorage.removeItem('refreshEngineMedia_' + currentEngineId);
                  engineId = currentEngineId;
                  
                  // Обновляем список через задержку чтобы дать время Observer обработать файлы
                  setTimeout(function() {
                     refreshMediaList();
                  }, 1500);
               }
            }
         });

         // Сохраняем функцию refreshMediaList в элементе для доступа извне
         element.data('refreshMediaList', refreshMediaList);
      }
   </script>
   @endBassetBlock
@endpush