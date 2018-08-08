<?php

class LanguageTest extends BrowserKitTestCase
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
        $this->visit('login')
            ->see('Zaloguj');

        $this->call('GET', '/lang/en');
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'en', false);
    }

    public function testLangSwitcherSetPolishCookie()
    {
        $this->visit('login')
            ->see('Zaloguj');

        $this->call('GET', '/lang/pl');
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'pl', false);

        $this->visit('login')
            ->see('Zaloguj');
    }

    public function testLangSwitcherSetEnglishFromRequestWithCookie()
    {
        $this->visit('login')
            ->see('Zaloguj');

        $cookie = ['lang' => 'en'];

        $this->call('GET', '/lang/pl', [], $cookie);
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'pl', false);

        $this->visit('login')
            ->dontSee('Zaloguj')
            ->see('Remember Me');
    }
}
