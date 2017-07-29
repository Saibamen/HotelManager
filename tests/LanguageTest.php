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
            ->dontSee('E-Mail Address');
    }

    public function testLangSwitcherToEnglish()
    {
        $this->call('GET', '/lang/en');
        $this->assertRedirectedTo('login');
        $this->seeCookie('lang', 'en');
    }
}