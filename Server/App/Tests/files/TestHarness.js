/**
 * Author: Levi
 * Created: 1/11/14
 */

(function($) {
	var DEFAULT_QUEUE = 'Default Queue';

	var testQueue = {};
	var queueOrder = [];
	var harnessErrors = [];
	var testingStarted = false;
	var testingComplete = false;
	var currentQueueIndex = 0;
	var testOptions = {
		asyncTimeout: 10
	};

	function TestHarness() {
		TestHarness.error('The TestHarness is a static class. Do not instantiate.');
	}

	window.TestHarness = TestHarness;

	TestHarness.configure = function(options) {
		if (options)
			$.extend(testOptions, options);
	};

	/**
	 *
	 * @param {Test} test
	 * @param {string} [queue]
	 */
	TestHarness.add = function(test, queue) {
		if (queue === undefined || queue === null) {
			queue = DEFAULT_QUEUE;
		}
		if (!testQueue[queue]) {
			TestHarness.createQueue(queue);
		}
		var thisQueue = testQueue[queue];
		test.number = thisQueue.tests.length + 1;
		thisQueue.tests.push(test);
		if (thisQueue.testNames[test.name]) {
			TestHarness.error('Duplicate test named ' + test.name + ' in ' + queue + ' queue');
		}
		thisQueue.testNames[test.name] = true;
	};

	/**
	 * @param {string} name
	 */
	TestHarness.createQueue = function(name) {
		if (testQueue[name]) {
			TestHarness.error('Queue ' + name + 'already exists.');
			return;
		}

		testQueue[name] = {
			tests: [],
			currentTestIndex: 0,
			complete: false,
			testNames: {}
		};
		queueOrder.push(name);
	};

	TestHarness.error = function(message) {
		harnessErrors.push(message);
	};

	TestHarness.run = function() {
		testingStarted = true;

		var runLoop = function() {
			updateDisplay();
			while (currentQueueIndex < queueOrder.length) {
				var queue = testQueue[queueOrder[currentQueueIndex]];
				while (!queue.complete) {
					if (queue.tests.length == 0) {
						queue.complete = true;
						break;
					}
					var curTest = queue.tests[queue.currentTestIndex];
					if (!curTest.isStarted()) {
						curTest.run();
						return;
					}
					if (!curTest.isComplete()) {
						return;
					}
					queue.currentTestIndex++;
					if (queue.currentTestIndex >= queue.tests.length) {
						// queue is finished
						queue.complete = true;
					}
				}

				// start next queue
				currentQueueIndex++;
			}

			// if we get here, all queues are complete
			window.clearInterval(runLoopInterval);
			testingComplete = true;
			updateDisplay();
		};

		var runLoopInterval = window.setInterval(runLoop, 10);
	};

	function TestException(name, message) {
		this.name = name;
		this.message = message;
	}

	/**
	 *
	 * @param {string} name
	 * @param {string} [queue]
	 * @constructor
	 */
	function Test(name, queue) {
		this.started = false;
		this.startTime = null;
		this.endTime = null;
		this.number = 0;

		/** @type {[TestStep]} */
		this.steps = [];
		this.nextStep = 0;
		/** @type {TestStep} */
		this.currentStep = null;

		this.name = name;

		this.pass = true;
		this.errors = [];

		TestHarness.add(this, queue);
	}

	Test.prototype.isStarted = function() {
		return this.started;
	};

	Test.prototype.isComplete = function() {
		return !!this.endTime;
	};

	Test.prototype.complete = function() {
		this.endTime = new Date();
	};

	/**
	 * @param {function(this:Test,TestStep)} test
	 */
	Test.prototype.step = function(test) {
		this.steps.push(new TestStep(test));
	};

	/**
	 * @param {function(this:Test,TestStep)} test
	 */
	Test.prototype.stepAsync = function(test) {
		this.steps.push(new TestStep(test, true));
	};

	/**
	 *
	 * @param {function(this:Test)} callback
	 */
	Test.prototype.wrap = function(callback) {
		var test = this;
		return function() {
			try {
				callback.apply(test, arguments);
			} catch (exception) {
				test.fail(exception.message || 'unknown exception', exception.name);
			}
		}
	};

	Test.prototype.run = function() {
		this.started = true;
		var test = this;
		this.nextStep = 0;
		var stepInterval = 0;
		function next() {
			if (test.nextStep == 0)
				test.startTime = new Date();
			if (test.pass === false) {
				window.clearInterval(stepInterval);
				return;
			}
			for (var i = test.nextStep; i <= test.steps.length; i++) {

				if (test.isComplete()) {
					window.clearInterval(stepInterval);
					return;
				}

				// is previous step complete?
				var prevStep = i > 0 ? test.steps[i-1] : null;
				if (prevStep && prevStep.async && !prevStep.isComplete()) {
					// check for timeout
					if (new Date() - prevStep.startTime > testOptions.asyncTimeout * 1000) {
						test.fail('step ' + i + ' did not complete', 'timeout');
						window.clearInterval(stepInterval);
						test.complete();
					} else {
						return;
					}
				}

				if (test.nextStep >= test.steps.length) {
					break;
				}
				// run next step
				var step = test.steps[i];
				test.currentStep = step;
				test.nextStep = i+1;
				test.wrap(function(step) {
					step.run(test);
				})(step);
				if (step.async) {
					return;
				}
			}
			window.clearInterval(stepInterval);
			test.complete();
		}

		stepInterval = window.setInterval(function() {
			next();
		}, 1);
	};

	Test.prototype.fail = function(message, type) {
		if (!type) {
			type = 'exception thrown';
		}
		this.pass = false;
		this.errors.push({type: type, message: message});
		this.endTime = new Date();
	};

	/**
	 * Assert truth is a boolean true
	 * @param {bool} truth
	 * @param {string} failMessage
	 */
	Test.prototype.assert = function(truth, failMessage) {
		if (truth !== true) {
			throw new TestException('assert failed', failMessage);
		}
	};

	/**
	 * Assert that the key exists in the object
	 * @param object
	 * @param {string|[string]} key
	 * @param {string} [failMessage]
	 */
	Test.prototype.assertExists = function(object, key, failMessage) {
		if ($.isArray(key)) {
			$.each(key, function(index, key) {
				if (!object.hasOwnProperty(key)) {
					throw new TestException('assertExists failed', failMessage || key);
				}
			});
		} else {
			if (!object.hasOwnProperty(key)) {
				throw new TestException('assertExists failed', failMessage || key);
			}
		}
	};

	window.Test = Test;

	/**
	 *
	 * @param {function(this:Test,TestStep)} testCallback
	 * @param {bool} [asynchronous]
	 * @constructor
	 */
	function TestStep(testCallback, asynchronous) {
		this.async = !!asynchronous;
		this.test = testCallback;
		this.completed = false;
		this.startTime = null;
	}

	/**
	 * Mark the test as complete (and by default, successful)
	 * Any failed assert will also automatically end the test and mark it complete
	 */
	TestStep.prototype.complete = function() {
		this.completed = true;
	};

	/**
	 * check if a test is complete
	 * @returns {boolean|*}
	 */
	TestStep.prototype.isComplete = function() {
		return this.completed;
	};

	/**
	 * run the test
	 * @param test
	 */
	TestStep.prototype.run = function(test) {
		this.startTime = new Date();
		this.test.call(test, this);
		if (!this.async)
			this.completed = true;
	};
	window.TestStep = TestStep;



	var container = null;
	var statusText = '';
	var stats = { tests: 0, failures: 0 };
	function updateDisplay() {
		if (!container) {
			container = $('#testResults');
			if (container.length == 0) {
				container = $('<div id="testResults"></div>');
				$('body').append(container);
			}
			initializeDisplay();
		}

		while (harnessErrors.length > 0) {
			var error = harnessErrors.shift();
			$('#testHarnessErrors').show().append($('<div></div>').text(error));
		}

		var status = '';
		if (testingComplete) {
			status = '';
			var statusDiv = $('#testStatus').empty();
			var className = stats.failures ? 'fail' : 'pass';
			statusDiv.append($('<span>Finished ('+stats.tests+' Tests, </span>'));
			statusDiv.append($('<span class="'+className+'">'+stats.failures+' Failed</span>'));
			statusDiv.append($('<span>)</span>'));
		} else if (testingStarted) {
			var queue = testQueue[queueOrder[currentQueueIndex]];
			var testNumber = queue.currentTestIndex + 1;
			var testCount = queue.tests.length;
			status = 'Running ' + queueOrder[currentQueueIndex] + ' (test ' + testNumber + ' of ' + testCount + ')';
		}
		if (statusText != status) {
			statusText = status;
			$('#testStatus').text(status);
		}


		for (var i=0; i<queueOrder.length; i++) {
			var queue = testQueue[queueOrder[i]];
			for (var j=0; j<queue.tests.length; j++) {
				/** @type {Test} test */
				var test = queue.tests[j];
				if (test.isComplete() && !test._rendered) {
					test._rendered = true;
					var table = $('#testResults'+i).append();
					table.append(generateTestResult(test));
					stats.tests++;
					stats.failures += !test.pass;
				}
			}
		}
	}

	function initializeDisplay() {
		container.append($('<div class="testSummaryBox"><h1>Status</h1><div id="testStatus"></div><div id="testSummary"></div></div><div id="testHarnessErrors" style="display:none"></div>'));
		for (var i=0; i<queueOrder.length; i++) {
			var queueName = queueOrder[i];
			container.append($('<h2></h2>').text(queueName));
			container.append($('<table class="results" id="testResults'+i+'"><tr><th>#</th><th>Result</th><th>Runtime</th><th>Test Name</th><th>Messages</th></tr></table>'));
		}
	}

	/**
	 * @param {Test} test
	 */
	function generateTestResult(test) {
		var row = $('<tr></tr>');
		row.append($('<td></td>').text(test.number));
		row.append($('<td></td>').addClass(test.pass ? 'pass' : 'fail').text(test.pass ? 'Pass' : 'Fail'));
		row.append($('<td></td>').text((test.endTime - test.startTime) / 1000));
		row.append($('<td></td>').text(test.name));
		var errors = $('<ul></ul>');
		$.each(test.errors, function(index, error) {
			errors.append($('<li class="error"></li>').append($('<span class="type"></span>').text(error.type + ': ')).append($('<span class="message"></span>').text(error.message)));
		});
		row.append($('<td></td>').append(errors));
		return row;
	}

})(jQuery);
