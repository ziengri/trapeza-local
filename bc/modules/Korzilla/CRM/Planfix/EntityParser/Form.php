<?php

namespace App\modules\Korzilla\CRM\Planfix\EntityParser;

class Form
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
        return $this->form['Subject'] ?? null;
    }

    public function getClientEmail()
    {
        return $this->form['Email'] ?? null;
    }

    public function getDescription()
    {
        return $this->form['mailtext'] ?? null;
    }
}