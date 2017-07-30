<?php

class LanguageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Session::start();
    }

    public function testDefaultLangIsPolish()
    {
        $this->visit('login')
            ->see('Zaloguj')
            ->see('Adres e-mail')
            ->see('Hasło')
            ->dontSee('E-Mail Address')
            ->dontSee('Pokoje')
            ->dontSee('Rooms');
    }

    public function testLangSwitcherSetEnglishCookie()
    {
        $this->visit('login');
        $this->call('GET', '/lang/en');
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'en');
    }

    public function testLangSwitcherSetPolishCookie()
    {
        $this->visit('login');
        $this->call('GET', '/lang/pl');
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'pl');
    }
}
