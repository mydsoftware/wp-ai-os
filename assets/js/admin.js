(function () {
	'use strict';

	const config = window.WP_AI_OS_Admin || {};
	const button = document.getElementById('wp-ai-os-scan');
	const status = document.getElementById('wp-ai-os-scan-status');
	const score = document.getElementById('wp-ai-os-score');
	const results = document.getElementById('wp-ai-os-results');
	const lastScan = document.getElementById('wp-ai-os-last-scan');

	if (!button || !config.restUrl || !config.nonce) {
		return;
	}

	const escapeHtml = (value) => {
		const div = document.createElement('div');
		div.textContent = String(value ?? '');
		return div.innerHTML;
	};

	const renderReport = (report) => {
		const reportScore = Number(report.score || 0);
		score.innerHTML = `${escapeHtml(reportScore)}<span>/ 100</span>`;

		results.innerHTML = '';

		(report.results || []).forEach((result) => {
			const row = document.createElement('tr');
			const recommendation = result.recommendation
				? `<br><small><strong>Recommendation:</strong> ${escapeHtml(result.recommendation)}</small>`
				: '';

			row.innerHTML = `
				<td><strong>${escapeHtml(result.title)}</strong></td>
				<td>${escapeHtml(result.score)}/${escapeHtml(result.max_score)}</td>
				<td>${escapeHtml(result.status)}</td>
				<td>${escapeHtml(result.message)}${recommendation}</td>
			`;

			results.appendChild(row);
		});

		if (report.scanned_at) {
			lastScan.textContent = `Last scan: ${report.scanned_at}`;
		}
	};

	button.addEventListener('click', async () => {
		button.disabled = true;
		status.textContent = 'Scanning…';

		try {
			const response = await fetch(`${config.restUrl}/readiness/scan`, {
			method: 'POST',
			headers: {
				'X-WP-Nonce': config.nonce,
				'Accept': 'application/json'
			}
		});

			const data = await response.json();

			if (!response.ok) {
				throw new Error(data.message || 'Scan failed.');
			}

			renderReport(data);
			status.textContent = 'Scan completed.';
		} catch (error) {
			status.textContent = error.message || 'Scan failed.';
		} finally {
			button.disabled = false;
		}
	});
})();
