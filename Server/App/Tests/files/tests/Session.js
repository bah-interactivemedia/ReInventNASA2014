(function() {
	var queue = '/session/';

	// Start session tests
	var test = new Test('start session (invalid attempts)', queue);
	ApiTest.testApi(test, '/session/start/', {}, null, null, 'called without deviceId', false);

	test = new Test('start session', queue);
	ApiTest.testApi(test, '/session/start/', {deviceId: ApiTest.deviceId}, function(data) {
		this.assertExists(data, 'sessionIdentifier', 'response.sessionIdentifier exists');
		this.assert(data.isLoggedIn === false, 'response.isLoggedIn == false');
		this.assert(data.hasAccount === false, 'response.hasAccount == false');
		ApiTest.currentSession = data.sessionIdentifier;
		this.currentStep.complete();
	}, null);

	// Renew session tests
	test = new Test('renew session (invalid attempts)', queue);
	test.stepAsync(function(step) {
		ApiTest.callApi('/session/renew/', {deviceId: ApiTest.deviceId}, this.wrap(function() {
			this.assert(false, 'Called without sessionId was successful')
		}), this.wrap(function(message) {
			this.currentStep.complete();
		}));
	});

	ApiTest.testApi(test, '/session/renew/', {deviceId: ApiTest.deviceId, sessionId: 123}, null, null,
		'Called with invalid sessionId', false);

	test = new Test('renew session', queue);
	ApiTest.testApi(test, '/session/renew/', {deviceId: ApiTest.deviceId}, function(data) {
		this.assertExists(data, 'sessionIdentifier', 'sessionIdentifier');
		this.assert(data.isLoggedIn === false, 'response.isLoggedIn == false');
		this.assert(data.hasAccount === false, 'response.hasAccount == false');
		ApiTest.currentSession = data.sessionIdentifier;
		ApiTest.activeSessions.push(data.sessionIdentifier);
		this.currentStep.complete();
	}, null, false, true);


})();