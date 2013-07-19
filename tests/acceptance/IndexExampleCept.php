<?php
$I = new WebGuy($scenario);
$I->wantTo('Default whoops pretty exception');
$I->amOnPage('/');
$I->see('Whoops! There was an error.');
