document.addEventListener('DOMContentLoaded', function () {
	const printBtn = document.getElementById('printBtn');
	const downloadPdfBtn = document.getElementById('downloadPdfBtn');
	const slip = document.getElementById('slip');

	// Disable PDF button if libraries failed to load
	if (downloadPdfBtn && (!window.html2canvas || !window.jspdf)) {
		downloadPdfBtn.disabled = true;
		downloadPdfBtn.title = 'PDF libraries not loaded — try printing or check console for errors';
	}

	if (printBtn) {
		printBtn.addEventListener('click', function () {
			window.print();
		});
	}

	if (downloadPdfBtn && slip && window.html2canvas && window.jspdf) {
		downloadPdfBtn.addEventListener('click', async function () {
			try {
				const canvas = await html2canvas(slip, { scale: 2 });
				const imgData = canvas.toDataURL('image/png', 1.0);
				const { jsPDF } = window.jspdf;
				const pdf = new jsPDF('p', 'pt', 'a4');

				const pageWidth = pdf.internal.pageSize.getWidth();
				const pageHeight = pdf.internal.pageSize.getHeight();

				// Calculate image dimensions to fit A4
				const imgProps = { width: canvas.width, height: canvas.height };
				const ratio = Math.min(pageWidth / imgProps.width, pageHeight / imgProps.height);
				const imgWidth = imgProps.width * ratio;
				const imgHeight = imgProps.height * ratio;

				const marginX = (pageWidth - imgWidth) / 2;
				pdf.addImage(imgData, 'PNG', marginX, 40, imgWidth, imgHeight);
				pdf.save('transcript-slip.pdf');
			} catch (err) {
				console.error('PDF generation failed', err);
				alert('Failed to generate PDF. Please try printing instead.');
			}
		});
	}
});
