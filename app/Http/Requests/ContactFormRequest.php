<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    */
   public function authorize(): bool
   {
      return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */
   public function rules(): array
   {
      return [
         'name' => 'required|string|min:2|max:255',
         'email' => 'required|email:rfc,dns',
         'phone' => 'required|regex:/^\+?[\d\s\(\)\-]{10,20}$/',
         'brand' => 'nullable|string|max:255',
         'model' => 'nullable|string|max:255',
         'message' => 'nullable|string|max:2000',
         'comment' => 'nullable|string|max:2000',
      ];
   }

   /**
    * Get custom error messages for validator rules.
    */
   public function messages(): array
   {
      return [
         'name.required' => 'Пожалуйста, введите ваше имя',
         'name.min' => 'Имя должно содержать минимум 2 символа',
         'name.max' => 'Имя не должно быть больше 255 символов',
         'email.required' => 'Пожалуйста, введите вашу почту',
         'email.email' => 'Пожалуйста, введите корректный адрес почты',
         'phone.required' => 'Пожалуйста, введите ваш телефон',
         'phone.regex' => 'Пожалуйста, введите корректный номер телефона',
         'brand.max' => 'Марка не должна быть больше 255 символов',
         'model.max' => 'Модель не должна быть больше 255 символов',
         'message.max' => 'Сообщение не должно быть больше 2000 символов',
         'comment.max' => 'Комментарий не должен быть больше 2000 символов',
      ];
   }
}
