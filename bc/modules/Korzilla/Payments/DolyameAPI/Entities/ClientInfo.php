<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use DateTime;
use InvalidArgumentException;
use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class ClientInfo implements Arrayable
{
    private $firstName = null;
    private $lastName = null;
    private $middleName = null;
    private $birthDate = null;
    private $phone = null;
    private $email;

    public function setFirstName(string $firstName): ClientInfo
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(string $lastName): ClientInfo
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setMiddleName(string $middleName): ClientInfo
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function setBirthDate(string $birthDate): ClientInfo
    {
        $d = DateTime::createFromFormat('Y-m-d', $birthDate);

        if (!$d) {
            throw new InvalidArgumentException(
                "wrong date format"
            );
        }

        $this->birthDate = $d;
        return $this;
    }

    public function setPhone(string $phone): ClientInfo
    {
        if (strpos($phone, '+') !== 0) {
            throw new InvalidArgumentException(
                "wrong phone format, should start with '+' sign"
            );
        }
        $this->phone = $phone;
        return $this;
    }

    public function setEmail(string $email): ClientInfo
    {
        $this->email = $email;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
        if ($this->birthDate) {
            $data['birthdate'] = $this->birthDate;
        }
        if ($this->email) {
            $data['email'] = $this->email;
        }

        return $data;
    }
}