<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnginesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'brand' => 'required|string|max:255',
            'oem' => 'required|string|max:255',
            'fit_for' => 'required|string|max:1000',
            'description' => 'nullable|string',

            'images' => 'nullable|array|max:6',
            'images.*' => 'sometimes|file|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Получаем файлы из request
        $files = $this->file('images');
        
        // Если файлы есть, фильтруем только валидные UploadedFile объекты
        if ($files && is_array($files)) {
            $validFiles = array_filter($files, function ($file) {
                return $file instanceof \Illuminate\Http\UploadedFile && $file->isValid();
            });
            
            // Обновляем данные запроса
            $this->merge([
                'images' => array_values($validFiles) // array_values для переиндексации
            ]);
        } elseif ($files === null) {
            // Если файлов нет, устанавливаем пустой массив
            $this->merge(['images' => []]);
        }
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'images.*.mimes' => 'Допустимые форматы: JPG, PNG, WEBP',
            'images.*.max' => 'Размер изображения не должен превышать 5 MB',
            'images.max' => 'Можно загрузить не более 6 изображений',
        ];
    }
}
