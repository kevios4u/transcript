document.addEventListener('DOMContentLoaded', function () {
  const selectButtons = document.querySelectorAll('.select-application');
  const regInput = document.getElementById('reg_no');
  const recipientInput = document.getElementById('recipient_id');
  const noteInput = document.getElementById('process_note');
  const statusSelect = document.getElementById('process_status');

  if (!selectButtons || selectButtons.length === 0) return;

  const defaultNotes = {
    verified: 'Verified by staff, ready for processing.',
    processing: 'In processing. Prepare transcript documents and verify recipient details.',
    approved: 'Approved for release. Transcript request meets all requirements.',
    completed: 'Completed. Transcript has been processed and finalized.'
  };

  function updateNoteForStatus(selectedStatus) {
    if (!noteInput) return;
    const currentNote = noteInput.value.trim();
    const suggestedNote = defaultNotes[selectedStatus] || '';

    if (!suggestedNote) return;

    if (currentNote === '' || Object.values(defaultNotes).includes(currentNote)) {
      noteInput.value = suggestedNote;
    }
  }

  selectButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const reg = btn.getAttribute('data-reg');
      const recipient = btn.getAttribute('data-recipient');
      if (regInput) regInput.value = reg;
      if (recipientInput) recipientInput.value = recipient || '';
      if (statusSelect) {
        statusSelect.value = 'verified';
        updateNoteForStatus('verified');
      }
      // scroll to form
      const form = document.getElementById('processForm');
      if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  if (statusSelect) {
    statusSelect.addEventListener('change', function () {
      updateNoteForStatus(statusSelect.value);
    });
  }
});
