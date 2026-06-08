<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Provider;
use App\Models\User;

abstract class Controller
{
    protected function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }

    protected function currentPatient(): Patient
    {
        return $this->currentUser()->patient;
    }

    protected function currentProvider(): Provider
    {
        return $this->currentUser()->provider;
    }
}
