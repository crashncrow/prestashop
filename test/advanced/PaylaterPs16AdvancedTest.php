<?php

namespace Test\Advanced;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Test\Common\AbstractPs16Selenium;

/**
 * @requires prestashop16install
 * @requires prestashop16register
 *
 * @group prestashop16advanced
 */
class PaylaterPs16InstallTest extends AbstractPs16Selenium
{
    /**
     * @REQ5 BackOffice should have 2 inputs for setting the public and private API key
     * @REQ6 BackOffice inputs for API keys should be mandatory upon save of the form.
     *
     * @throws  \Exception
     */
    public function testPublicAndPrivateKeysInputs()
    {
        $this->loginToBackOffice();
        $this->getPaylaterBackOffice();

        //2 elements exist:
        $validatorSearch = WebDriverBy::id('pmt_public_key');
        $condition = WebDriverExpectedCondition::visibilityOfElementLocated($validatorSearch);
        $this->waitUntil($condition);
        $this->assertTrue((bool) $condition);
        $validatorSearch = WebDriverBy::id('pmt_private_key');
        $condition = WebDriverExpectedCondition::visibilityOfElementLocated($validatorSearch);
        $this->waitUntil($condition);
        $this->assertTrue((bool) $condition);

        //save with empty public Key
        $this->findById('pmt_public_key')->clear();
        $this->findById('module_form_submit_btn')->click();
        $validatorSearch = WebDriverBy::className('module_error');
        $condition = WebDriverExpectedCondition::visibilityOfElementLocated($validatorSearch);
        $this->waitUntil($condition);
        $this->assertTrue((bool) $condition);
        $this->assertContains('Please add a Paga+Tarde API Public Key', $this->webDriver->getPageSource());
        $this->findById('pmt_public_key')->clear()->sendKeys($this->configuration['publicKey']);

        //save with empty private Key
        $this->findById('pmt_private_key')->clear();
        $this->findById('module_form_submit_btn')->click();
        $validatorSearch = WebDriverBy::className('module_error');
        $condition = WebDriverExpectedCondition::visibilityOfElementLocated($validatorSearch);
        $this->waitUntil($condition);
        $this->assertTrue((bool) $condition);
        $this->assertContains('Please add a Paga+Tarde API Private Key', $this->webDriver->getPageSource());
        $this->findById('pmt_private_key')->clear()->sendKeys($this->configuration['secretKey']);
        $this->quit();
    }

    /**
     * @REQ9 BackOffice Simulator Product Page
     * @REQ11 BackOffice Simulator Start and Max installments
     * @REQ12 BackOffice MinAmount (product simulator part)
     * @REQ19 Simulator Shown
     * @REQ20 Simulator Installments check
     * @REQ21 Simulator Min Amount
     *
     * @throws \Exception
     */
    public function testSimulatorInProductPage()
    {
        $this->goToProduct();
        $simulatorDiv = $this->findByClass('PmtSimulator');
        $simulatorType = $simulatorDiv->getAttribute('data-pmt-type');
        $numQuota = $simulatorDiv->getAttribute('data-pmt-num-quota');
        $maxInstallments = $simulatorDiv->getAttribute('data-pmt-max-ins');

        $this->assertEquals(6, $simulatorType);
        $this->assertEquals(3, $numQuota);
        $this->assertEquals(12, $maxInstallments);

        // Check min amount simulator
        // Test no longer avaliable with .env file
        // $this->loginToBackOffice();
        // $this->getPaylaterBackOffice();
        // $this->findById('pmt_display_min_amount')->clear()->sendKeys(500);
        // $this->findById('module_form_submit_btn')->click();
        //
        // $this->goToProduct(false);
        // $html = $this->webDriver->getPageSource();
        // $this->assertNotContains('PmtSimulator', $html);
        //
        // Hide simulator
        // $this->getPaylaterBackOffice();
        // $this->findById('pmt_display_min_amount')->clear()->sendKeys(1);
        // $this->findByCss('#pmt_simulator_is_enabled_off + label')->click();
        // $this->findById('module_form_submit_btn')->click();
        //
        // $this->goToProduct(false);
        // $html = $this->webDriver->getPageSource();
        // $this->assertNotContains('PmtSimulator', $html);
        //
        // Restore default simulator
        // $this->getPaylaterBackOffice();
        // $this->findById('pmt_display_min_amount')->clear()->sendKeys(1);
        // $this->findByCss('#pmt_simulator_is_enabled_on + label')->click();
        // $this->findById('module_form_submit_btn')->click();
        //
        // $this->goToProduct();
        // $simulatorDiv = $this->findByClass('PmtSimulator');
        // $simulatorType = $simulatorDiv->getAttribute('data-pmt-type');
        // $numQuota = $simulatorDiv->getAttribute('data-pmt-num-quota');
        // $maxInstallments = $simulatorDiv->getAttribute('data-pmt-max-ins');
        //
        // $this->assertEquals(6, $simulatorType);
        // $this->assertEquals(3, $numQuota);
        // $this->assertEquals(12, $maxInstallments);
        $this->quit();
    }

    /**
     * @REQ17 BackOffice Panel should have visible Logo and links
     *
     * @throws \Exception
     */
    public function testBackOfficeHasLogoAndLinkToPmt()
    {
        //Change Title
        $this->loginToBackOffice();
        $this->getPaylaterBackOffice();
        $html = $this->webDriver->getPageSource();
        $this->assertContains('logo_pagamastarde.png', $html);
        $this->assertContains('Login Paga+Tarde', $html);
        $this->assertContains('https://bo.pagamastarde.com', $html);
        $this->quit();
    }
}
