/* Multi-step consultation form. Without JavaScript every step stays visible and the form still submits. */
(function () {
	'use strict';
	document.querySelectorAll('form[data-steps]').forEach(function (form) {
		var steps = Array.prototype.slice.call(form.querySelectorAll('.step'));
		var dots = form.querySelectorAll('.steps-bar li');
		var prev = form.querySelector('[data-prev]');
		var next = form.querySelector('[data-next]');
		var submit = form.querySelector('[data-submit]');
		var cur = 0;

		function show(n) {
			cur = n;
			steps.forEach(function (s, i) { s.hidden = i !== n; });
			dots.forEach(function (d, i) { d.classList.toggle('is-on', i === n); d.classList.toggle('is-done', i < n); });
			prev.hidden = n === 0;
			next.hidden = n === steps.length - 1;
			submit.hidden = n !== steps.length - 1;
			var focusable = steps[n].querySelector('input,textarea');
			if (focusable && n > 0) { focusable.focus({ preventScroll: true }); }
		}

		function valid(n) {
			var ok = true;
			steps[n].querySelectorAll('[required]').forEach(function (el) {
				var good = el.type === 'radio'
					? !!steps[n].querySelector('input[name="' + el.name + '"]:checked')
					: el.type === 'checkbox' ? el.checked : el.checkValidity() && el.value.trim() !== '';
				el.setAttribute('aria-invalid', good ? 'false' : 'true');
				if (!good && ok) { el.focus(); }
				ok = ok && good;
			});
			return ok;
		}

		next.addEventListener('click', function () { if (cur < steps.length - 1 && valid(cur)) { show(cur + 1); } });
		prev.addEventListener('click', function () { show(cur - 1); });
		form.addEventListener('submit', function (e) {
			for (var i = 0; i < steps.length; i++) {
				if (!valid(i)) { e.preventDefault(); show(i); return; }
			}
		});
		show(0);
	});
})();
