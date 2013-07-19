<?php
$I = new WebGuy($scenario);
$I->wantTo('AJAX whoops pretty exception');
$I->amOnPage('/');
$I->sendAjaxGetRequest('index.php');
$I->see('RuntimeException');
