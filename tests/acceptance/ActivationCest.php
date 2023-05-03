<?php

class ActivationCest {
	// tests
	public function testActivation( AcceptanceTester $I ) {
		$pluginSlug = 'wpgraphql-for-facetwp';

		$I->wantTo( 'activate and deactivate the plugin correctly' );

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginActivated( $pluginSlug );
		$I->deactivatePlugin( $pluginSlug );

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginDeactivated( $pluginSlug );
		$I->activatePlugin( $pluginSlug );

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginActivated( $pluginSlug );
	}
}
