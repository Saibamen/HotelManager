<?php

class LoginTest extends TestCase
{
    public function testEmptyLogin() {
        $this->visit('/login')
            ->see('Zaloguj')
            ->type('', 'email')
            ->type('', 'password')
            ->press('Zaloguj')
            ->seePageIs('/login')
            ->dontSee('Cześć')
            ->see('Pole adres e-mail jest wymagane.')
            ->see('Pole hasło jest wymagane.');
    }

    public function testSimpleFailLogin() {
        $this->visit('/login')
            ->see('Zaloguj')
            ->type('badEmail', 'email')
            ->type('badPass', 'password')
            ->press('Zaloguj')
            ->seePageIs('/login')
            ->dontSee('Cześć')
            ->see('Błędny login lub hasło.');
    }
}
