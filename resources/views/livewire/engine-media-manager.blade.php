<div class="card shadow-sm">
   <div class="card-header bg-light">
      <h5 class="mb-0">Управление фотографиями мотора</h5>
   </div>

   <div class="card-body">
      <!-- Flash сообщения (успех, ошибка, предупреждение) -->
      @if($message = session('success'))
         <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="la la-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>
      @endif

      @if($message = session('error'))
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="la la-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>
      @endif

      @if($message = session('warning'))
         <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="la la-exclamation-triangle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>
      @endif

      <!-- Существующие фото -->
      @if($images)
         <div class="mb-4">
            <h6 class="fw-bold mb-3">Загруженные фотографии ({{ count($images) }})</h6>
            <div class="row g-3">
               @foreach($images as $image)
                  <div class="col-md-3 col-sm-4">
                     <div class="position-relative border rounded overflow-hidden" style="aspect-ratio: 1;">
                        <img src="{{ $image['thumb'] }}" alt="{{ $image['name'] }}" class="w-100 h-100"
                           style="object-fit: cover;">

                        <button type="button" wire:click="deleteImage({{ $image['id'] }})"
                           class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" title="Удалить фотографию">
                           <i class="la la-trash"></i>
                        </button>

                        <small class="position-absolute bottom-0 start-0 bg-dark text-white px-2 py-1 small">
                           {{ $image['size'] }} MB
                        </small>
                     </div>
                     <small class="text-muted d-block mt-2 text-truncate">{{ $image['name'] }}</small>
                  </div>
               @endforeach
            </div>
         </div>
      @else
         <div class="alert alert-info mb-4">
            <i class="la la-info-circle"></i> Фотографий еще не загружено
         </div>
      @endif

      <!-- Загрузка новых фото -->
      <div class="border-top pt-4">
         <h6 class="fw-bold mb-3">Добавить новые фотографии</h6>

         {{-- Ошибки валидации --}}
         @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
               <strong>Ошибки:</strong>
               <ul class="mb-0">
                  @foreach($errors->all() as $error)
                     <li>{{ $error }}</li>
                  @endforeach
               </ul>
               <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
         @endif

         <div class="mb-3">
            <label class="form-label">Выберите файлы (JPG, PNG, WEBP)</label>
            <input type="file" wire:model="uploadedFiles" multiple accept="image/jpeg,image/png,image/webp"
               class="form-control @error('uploadedFiles.*') is-invalid @enderror">
            <small class="text-muted d-block mt-1">Максимум 5 MB на файл, не более 6 фото всего</small>
            @error('uploadedFiles.*')
               <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
         </div>

         <!-- Превью загруженных файлов -->
         @if($uploadedFiles)
            <div class="mb-3">
               <h6 class="small fw-bold mb-2">Готово к загрузке ({{ count($uploadedFiles) }})</h6>
               <div class="row g-2">
                  @foreach($uploadedFiles as $key => $file)
                     <div class="col-md-2">
                        <div class="position-relative border rounded overflow-hidden" style="aspect-ratio: 1;">
                           <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-100 h-100"
                              style="object-fit: cover;">

                           <button type="button" wire:click="removeUploadedFile({{ $key }})"
                              class="btn btn-sm btn-warning position-absolute top-0 end-0 m-1" title="Удалить из очереди">
                              <i class="la la-times"></i>
                           </button>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
         @endif

         <div class="d-flex gap-2">
            <button type="button" wire:click="saveMedia" class="btn btn-primary">
               <i class="la la-upload"></i> Загрузить фото
            </button>

            @if($uploadedFiles)
               <button type="button" wire:click="$set('uploadedFiles', [])" class="btn btn-secondary">
                  Очистить
               </button>
            @endif
         </div>
      </div>
   </div>
</div>

@push('scripts')
   <script>
      // Автоматически скрывать alert через 4 секунды
      document.addEventListener('DOMContentLoaded', function () {
         const alerts = document.querySelectorAll('.alert');
         alerts.forEach(alert => {
            setTimeout(() => {
               const bsAlert = new bootstrap.Alert(alert);
               bsAlert.close();
            }, 4000);
         });
      });

      // Обработчик Livewire событий для динамических alert'ов
      document.addEventListener('livewire:updated', function () {
         const alerts = document.querySelectorAll('.alert');
         alerts.forEach(alert => {
            // Пропускаем уже установленные таймауты
            if (!alert.dataset.alertTimer) {
               alert.dataset.alertTimer = true;
               setTimeout(() => {
                  const bsAlert = new bootstrap.Alert(alert);
                  bsAlert.close();
               }, 4000);
            }
         });
      });
   </script>
@endpush