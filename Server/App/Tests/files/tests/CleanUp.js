(function() {

	var queue = 'clean up';

	test = new Test('delete active sessions', queue);
	test.step(function() {
		// add addition steps to attempt to delete all sessions and corresponding data
		for (var i = 0; i < ApiTest.activeSessions.length; i++) {
			(function(session) {
				ApiTest.testApi(test, '/Tests/cleanUp/', {
					sessionId: session
				}, function(data) {
					this.currentStep.complete();
				}, function(message) {
					this.assert(message === 'Session does not exist', 'unknown error deleting session ' + session);
					this.currentStep.complete();
				});
			})(ApiTest.activeSessions[i]);
		}
	});

})();
