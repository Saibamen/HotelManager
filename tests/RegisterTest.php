<?php

class RegisterTest extends TestCase
{
    public function setUp() {
        parent::setUp();
        Session::start();
    }

    public function testEmptyRegister() {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('', 'name')
            ->type('', 'email')
            ->type('', 'password')
            ->type('', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Cześć')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailRegister() {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('bad', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Cześć')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Hasło musi mieć przynajmniej 6 znaków.');
    }

    public function testSimpleFailRegisterBadPassConfirm() {
        $this->visit('register')
            ->see('Zarejestruj')
            ->type('name', 'name')
            ->type('badEmail', 'email')
            ->type('badPass4', 'password')
            ->type('badPassConfirm', 'password_confirmation')
            ->press('Zarejestruj')
            ->seePageIs('register')
            ->dontSee('Cześć')
            ->see('Format adres e-mail jest nieprawidłowy.')
            ->see('Potwierdzenie hasło nie zgadza się.')
            ->assertNull(\Illuminate\Support\Facades\Input::get('password'));
    }
}
