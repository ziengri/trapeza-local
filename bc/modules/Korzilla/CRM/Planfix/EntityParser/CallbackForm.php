<?php

namespace App\modules\Korzilla\CRM\Planfix\EntityParser;

class CallbackForm
{
    private $form;

    public function __construct($form)
    {
        $this->form = $form;    
    }

    public function getClientName()
    {
        return $this->form['Name'] ?? null;
    }
    
    public function getClientPhone()
    {
        return $this->form['phone'] ?? null;
    }

    public function getClientEmail()
    {
        return $this->form['Email'] ?? null;
    }

    public function getDescription()
    {
        $description = "Клиент просит Вас перезвонить ему.";

        if ($this->form['city']) {
            $description .= $description ? "<br/>" : '';
            $description .= "Город: {$this->form['city']}";
        }

        if ($this->form['time']) {
            $description .= $description ? "<br/>" : '';
            $description .= "Удобное время для связи: {$this->form['time']}";
        }

        if ($this->form['subname']) {
            $description .= $description ? "<br/>" : '';
            $description .= "Запрос со страницы: {$this->form['subname']}";
        }
        
        return $description;
    }
}