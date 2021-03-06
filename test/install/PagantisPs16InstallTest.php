<?php

namespace Test\Install;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Test\Common\AbstractPs16Selenium;

/**
 * @requires prestashop16basic
 * @group prestashop16install
 */
class PagantisPs16InstallTest extends AbstractPs16Selenium
{
    /**
     * @throws \Exception
     */
    public function testInstallAndConfigurePagantisInPrestashop16()
    {
        $this->loginToBackOffice();
        $this->uploadPagantis();
        $this->configurePagantis();
        $this->configureLanguagePack('72', 'Español (Spanish)');
        $this->quit();
    }

    /**
     * @throws \Exception
     */
    public function configurePagantis()
    {
        $this->findByLinkText('Modules and Services')->click();
        $this->findById('moduleQuicksearch')->clear()->sendKeys('Pagantis');
        $this->installOrConfigureModule();

        // new prompt in module installation no thusted
        try {
            sleep(3);
            $this->findByCss('#moduleNotTrusted #proceed-install-anyway')->click();
        } catch (\Exception $exception) {
            // do nothing, no prompt
        };

        $this->findByCss('#pagantis_is_enabled_on + label')->click();
        $this->findById('pagantis_public_key')->clear()->sendKeys('tk_8517351ec6ae44b29f5dca6e');
        $this->findById('pagantis_private_key')->clear()->sendKeys('c580df9e0b7b40c3');
        $this->findByCss('#pagantis_simulator_is_enabled_on + label')->click();
        $this->findById('module_form_submit_btn')->click();
        $confirmationSearch = WebDriverBy::className('module_confirmation');
        $condition = WebDriverExpectedCondition::textToBePresentInElement(
            $confirmationSearch,
            'All changes have been saved'
        );
        $this->webDriver->wait($condition);
        $this->assertTrue((bool) $condition);
    }

    public function installOrConfigureModule()
    {
        try {
            $pagantisAnchor = $this->findById('anchorPagantis');
            $pagantisAnchorParent = $this->getParent($pagantisAnchor);
            $pagantisAnchorGrandParent = $this->getParent($pagantisAnchorParent);
            $this->moveToElementAndClick($pagantisAnchorGrandParent->findElement(
                WebDriverBy::partialLinkText('Install')
            ));
        } catch (\Exception $exception) {
            $pagantisAnchor = $this->findById('anchorPagantis');
            $pagantisAnchorParent = $this->getParent($pagantisAnchor);
            $pagantisAnchorGrandParent = $this->getParent($pagantisAnchorParent);
            $this->moveToElementAndClick($pagantisAnchorGrandParent->findElement(
                WebDriverBy::partialLinkText('Configure')
            ));
        }
    }
}
