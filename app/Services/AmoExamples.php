<?php

namespace App\Services;

/**
 * Примеры использования AmoCRM API
 * 
 * Файл с примерами - можно использовать как шаблон для коода в контроллерах
 */

class AmoExamples
{
   // Пример создания контакта смотри ниже в комментариях

   /**
    * ПРИМЕР 1: Создать контакт в AmoCRM
    * 
    * use AmoCRM\Models\ContactModel;
    * use App\Services\AmoService;
    * 
    * $amo = new AmoService();
    * 
    * $contact = new ContactModel();
    * $contact->setFirstName('Иван');
    * $contact->setLastName('Петров');
    * 
    * $response = $amo->api()->contacts()->addOne($contact);
    * $contactId = $response->getId();
    */

   /**
    * ПРИМЕР 2: Создать сделку в AmoCRM
    * 
    * use AmoCRM\Models\LeadModel;
    * use App\Services\AmoService;
    * 
    * $amo = new AmoService();
    * 
    * $lead = new LeadModel();
    * $lead->setName('Сделка на 100 000 рублей');
    * $lead->setPrice(100000);
    * 
    * $response = $amo->api()->leads()->addOne($lead);
    * $leadId = $response->getId();
    */

   /**
    * ПРИМЕР 3: Полный цикл - контакт + сделка + примечание
    * 
    * use AmoCRM\Models\ContactModel;
    * use AmoCRM\Models\LeadModel;
    * use AmoCRM\Models\NoteModel;
    * use App\Services\AmoService;
    * 
    * $amo = new AmoService();
    * 
    * // 1. Создать контакт
    * $contact = new ContactModel();
    * $contact->setFirstName('Иван');
    * $contactResp = $amo->api()->contacts()->addOne($contact);
    * $contactId = $contactResp->getId();
    * 
    * // 2. Создать сделку
    * $lead = new LeadModel();
    * $lead->setName('Новый заказ');
    * $leadResp = $amo->api()->leads()->addOne($lead);
    * $leadId = $leadResp->getId();
    * 
    * // 3. Добавить примечание к сделке
    * $note = new NoteModel();
    * $note->setText('Примечание из Laravel');
    * $note->setEntityId($leadId);
    * $amo->api()->notes($leadId)->addOne($note);
    */
}

