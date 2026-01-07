@extends(backpack_view('blank'))

@php
   $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.edit') => false,
   ];

   $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
   <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
      bp-section="page-header">
      <h1 class="text-capitalize mb-0" bp-section="page-heading">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}
      </h1>
      <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">
         {!! $crud->getSubheading() ?? trans('backpack::crud.edit') . ' ' . $crud->entity_name !!}.</p>
      @if ($crud->hasAccess('list'))
         <p class="mb-0 ms-2 ml-2" bp-section="page-subheading-back-button">
            <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                     class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                  {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
         </p>
      @endif
   </section>
@endsection

@section('content')
   <div class="row" bp-section="crud-operation-update">
      <div class="{{ $crud->getEditContentClass() }}">
         {{-- Default box --}}

         @include('crud::inc.grouped_errors')

         <form method="post" action="{{ url($crud->route . '/' . $entry->getKey()) }}" @if ($crud->hasUploadFields('update', $entry->getKey())) enctype="multipart/form-data" @endif>
            {!! csrf_field() !!}
            {!! method_field('PUT') !!}

            @includeWhen($crud->model->translationEnabled(), 'crud::inc.edit_translation_notice')

            {{-- load the view from the application if it exists, otherwise load the one in the package --}}
            @if(view()->exists('vendor.backpack.crud.form_content'))
               @include('vendor.backpack.crud.form_content', ['fields' => $crud->fields(), 'action' => 'edit'])
            @else
               @include('crud::form_content', ['fields' => $crud->fields(), 'action' => 'edit'])
            @endif
            {{-- This makes sure that all field assets are loaded. --}}
            <div class="d-none" id="parentLoadedAssets">{{ json_encode(Basset::loaded()) }}</div>
            @include('crud::inc.form_save_buttons')
         </form>
      </div>

      <!-- Media Manager Section -->
      <div class="{{ $crud->getEditContentClass() }} mt-5">
         <div class="card">
            <div class="card-header bg-light">
               <h5 class="mb-0">Фотографии мотора</h5>
            </div>
            <div class="card-body">
               @if($entry && $entry->exists)
                  <livewire:engine-media-manager :engine="$entry" />
               @else
                  <div class="alert alert-warning">
                     <i class="la la-exclamation-triangle"></i> Сначала сохраните мотор, затем сможете добавлять фотографии.
                  </div>
               @endif
            </div>
         </div>
      </div>
   </div>

   @push('scripts')
      <script>
         // Уведомления при загрузке/удалении фото
         document.addEventListener('livewire:dispatch', function (event) {
            if (event.detail && event.detail.action === 'notify') {
               const payload = event.detail.payload[0];
               if (payload && payload.type && payload.message) {
                  // Используем встроенное уведомление Backpack
                  const message = payload.message;
                  const alertClass = payload.type === 'success' ? 'success' :
                     payload.type === 'error' ? 'danger' : 'warning';

                  const alertHtml = `<div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                     </div>`;

                  $('form').before(alertHtml);
                  setTimeout(() => { $('.alert').fadeOut(300); }, 3000);
               }
            }
         });
      </script>
   @endpush
@endsection