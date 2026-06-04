/* ═══════════════════════════════════════════════════════════════════════
   staff-update-student-profile.js
   Handles:
     1. State → LGA cascade dropdown
     2. CSV drag-drop zone + file selection
     3. Live CSV preview (first 5 data rows)
     4. Enable/disable upload submit button
     5. Client-side validation for the manual Add Student form
   ═══════════════════════════════════════════════════════════════════════ */

/* ── 1. State → LGA data ───────────────────────────────────────────── */
const lgasByState = {
  Abia: ['Aba North','Aba South','Arochukwu','Bende','Ikwuano','Isiala Ngwa North','Isiala Ngwa South','Isuikwuato','Obi Ngwa','Ohafia','Osisioma','Ugwunagbo','Ukwa East','Ukwa West','Umuahia North','Umuahia South','Umunneochi'],
  Adamawa: ['Demsa','Fufore','Ganye','Girei','Gombi','Guyuk','Hong','Jada','Lamurde','Madagali','Maiha','Mayo Belwa','Michika','Mubi North','Mubi South','Numan','Shelleng','Song','Toungo','Yola North','Yola South'],
  'Akwa Ibom': ['Abak','Eastern Obolo','Eket','Esit Eket','Essien Udim','Etim Ekpo','Etinan','Ibeno','Ibesikpo Asutan','Ibiono Ibom','Ika','Ikono','Ikot Abasi','Ikot Ekpene','Ini','Itu','Mbo','Mkpat Enin','Nsit Atai','Nsit Ibom','Nsit Ubium','Obot Akara','Okobo','Onna','Oron','Oruk Anam','Udung Uko','Ukanafun','Uruan','Urue-Offong/Oruko','Uyo'],
  Anambra: ['Aguata','Anambra East','Anambra West','Anaocha','Awka North','Awka South','Ayamelum','Dunukofia','Ekwusigo','Idemili North','Idemili South','Ihiala','Njikoka','Nnewi North','Nnewi South','Ogbaru','Onitsha North','Onitsha South','Orumba North','Orumba South','Oyi'],
  Bauchi: ['Alkaleri','Bauchi','Bogoro','Damban','Darazo','Dass','Gamawa','Ganjuwa','Giade','Itas/Gadau',"Jama'are",'Katagum','Kirfi','Misau','Ningi','Shira','Tafawa Balewa','Toro','Warji','Zaki'],
  Bayelsa: ['Brass','Ekeremor','Kolokuma/Opokuma','Nembe','Ogbia','Sagbama','Southern Ijaw','Yenagoa'],
  Benue: ['Ado','Agatu','Apa','Buruku','Gboko','Guma','Gwer East','Gwer West','Katsina-Ala','Konshisha','Kwande','Logo','Makurdi','Obi','Ogbadibo','Ohimini','Oju','Okpokwu','Otukpo','Tarka','Ukum','Ushongo','Vandeikya'],
  Borno: ['Abadam','Askira/Uba','Bama','Bayo','Biu','Chibok','Damboa','Dikwa','Gubio','Guzamala','Gwoza','Hawul','Jere','Kaga','Kala/Balge','Konduga','Kukawa','Kwaya Kusar','Mafa','Magumeri','Maiduguri','Marte','Mobbar','Monguno','Ngala','Nganzai','Shani'],
  'Cross River': ['Abi','Akamkpa','Akpabuyo','Bakassi','Bekwarra','Biase','Boki','Calabar Municipal','Calabar South','Etung','Ikom','Obanliku','Obubra','Obudu','Odukpani','Ogoja','Yakurr','Yala'],
  Delta: ['Aniocha North','Aniocha South','Bomadi','Burutu','Ethiope East','Ethiope West','Ika North East','Ika South','Isoko North','Isoko South','Ndokwa East','Ndokwa West','Okpe','Oshimili North','Oshimili South','Patani','Sapele','Udu','Ughelli North','Ughelli South','Ukwuani','Uvwie','Warri North','Warri South','Warri South West'],
  Ebonyi: ['Abakaliki','Afikpo North','Afikpo South','Ebonyi','Ezza North','Ezza South','Ikwo','Ishielu','Ivo','Izzi','Ohaozara','Ohaukwu','Onicha'],
  Edo: ['Akoko-Edo','Egor','Esan Central','Esan North-East','Esan South-East','Esan West','Etsako Central','Etsako East','Etsako West','Igueben','Ikpoba-Okha','Oredo','Orhionmwon','Ovia North-East','Ovia South-West','Owan East','Owan West','Uhunmwonde'],
  Ekiti: ['Ado Ekiti','Efon','Ekiti East','Ekiti South-West','Ekiti West','Emure','Gbonyin','Ido Osi','Ijero','Ikere','Ikole','Ilejemeje','Irepodun/Ifelodun','Ise/Orun','Moba','Oye'],
  Enugu: ['Aninri','Awgu','Enugu East','Enugu North','Enugu South','Ezeagu','Igbo Etiti','Igbo Eze North','Igbo Eze South','Isi Uzo','Nkanu East','Nkanu West','Nsukka','Oji River','Udenu','Udi','Uzo Uwani'],
  FCT: ['Abaji','Bwari','Gwagwalada','Kuje','Kwali','Municipal Area Council'],
  Gombe: ['Akko','Balanga','Billiri','Dukku','Funakaye','Gombe','Kaltungo','Kwami','Nafada','Shongom','Yamaltu/Deba'],
  Imo: ['Aboh Mbaise','Ahiazu Mbaise','Ehime Mbano','Ezinihitte','Ideato North','Ideato South','Ihitte/Uboma','Ikeduru','Isiala Mbano','Isu','Mbaitoli','Ngor Okpala','Njaba','Nkwerre','Nwangele','Obowo','Oguta','Ohaji/Egbema','Okigwe','Onuimo','Orlu','Orsu','Oru East','Oru West','Owerri Municipal','Owerri North','Owerri West'],
  Jigawa: ['Auyo','Babura','Biriniwa','Birnin Kudu','Buji','Dutse','Gagarawa','Garki','Gumel','Guri','Gwaram','Gwiwa','Hadejia','Jahun','Kafin Hausa','Kazaure','Kiri Kasama','Kiyawa','Kaugama','Maigatari','Malam Madori','Miga','Ringim','Roni','Sule Tankarkar','Taura','Yankwashi'],
  Kaduna: ['Birnin Gwari','Chikun','Giwa','Igabi','Ikara','Jaba',"Jema'a",'Kachia','Kaduna North','Kaduna South','Kagarko','Kajuru','Kaura','Kauru','Kubau','Kudan','Lere','Makarfi','Sabon Gari','Sanga','Soba','Zangon Kataf','Zaria'],
  Kano: ['Ajingi','Albasu','Bagwai','Bebeji','Bichi','Bunkure','Dala','Dambatta','Dawakin Kudu','Dawakin Tofa','Doguwa','Fagge','Gabasawa','Garko','Garun Mallam','Gaya','Gezawa','Gwale','Gwarzo','Kabo','Kano Municipal','Karaye','Kibiya','Kiru','Kumbotso','Kunchi','Kura','Madobi','Makoda','Minjibir','Nasarawa','Rano','Rimin Gado','Rogo','Shanono','Sumaila','Takai','Tarauni','Tofa','Tsanyawa','Tudun Wada','Ungogo','Warawa','Wudil'],
  Katsina: ['Bakori','Batagarawa','Batsari','Baure','Bindawa','Charanchi','Dan Musa','Dandume','Danja','Daura','Dutsi','Dutsin-Ma','Faskari','Funtua','Ingawa','Jibia','Kafur','Kaita','Kankara','Kankia','Katsina','Kurfi','Kusada',"Mai'Adua",'Malumfashi','Mani','Mashi','Matazu','Musawa','Rimi','Sabuwa','Safana','Sandamu','Zango'],
  Kebbi: ['Aleiro','Arewa Dandi','Argungu','Augie','Bagudo','Birnin Kebbi','Bunza','Dandi','Fakai','Gwandu','Jega','Kalgo','Koko/Besse','Maiyama','Ngaski','Sakaba','Shanga','Suru','Wasagu/Danko','Yauri','Zuru'],
  Kogi: ['Adavi','Ajaokuta','Ankpa','Bassa','Dekina','Ibaji','Idah','Igalamela Odolu','Ijumu','Kabba/Bunu','Kogi','Lokoja','Mopa Muro','Ofu','Ogori/Magongo','Okehi','Okene','Olamaboro','Omala','Yagba East','Yagba West'],
  Kwara: ['Asa','Baruten','Edu','Ekiti','Ifelodun','Ilorin East','Ilorin South','Ilorin West','Irepodun','Isin','Kaiama','Moro','Offa','Oke Ero','Oyun','Pategi'],
  Lagos: ['Agege','Ajeromi-Ifelodun','Alimosho','Amuwo-Odofin','Apapa','Badagry','Epe','Eti Osa','Ibeju-Lekki','Ifako-Ijaiye','Ikeja','Ikorodu','Kosofe','Lagos Island','Lagos Mainland','Mushin','Ojo','Oshodi-Isolo','Shomolu','Surulere'],
  Nasarawa: ['Akwanga','Awe','Doma','Karu','Keana','Keffi','Kokona','Lafia','Nasarawa','Nasarawa Egon','Obi','Toto','Wamba'],
  Niger: ['Agaie','Agwara','Bida','Borgu','Bosso','Chanchaga','Edati','Gbako','Gurara','Katcha','Kontagora','Lapai','Lavun','Magama','Mariga','Mashegu','Mokwa','Munya','Paikoro','Rafi','Rijau','Shiroro','Suleja','Tafa','Wushishi'],
  Ogun: ['Abeokuta North','Abeokuta South','Ado-Odo/Ota','Ewekoro','Ifo','Ijebu East','Ijebu North','Ijebu North East','Ijebu Ode','Ikenne','Imeko Afon','Ipokia','Obafemi Owode','Odeda','Odogbolu','Ogun Waterside','Remo North','Sagamu','Yewa North','Yewa South'],
  Ondo: ['Akoko North-East','Akoko North-West','Akoko South-East','Akoko South-West','Akure North','Akure South','Ese Odo','Idanre','Ifedore','Ilaje','Ile Oluji/Okeigbo','Irele','Odigbo','Okitipupa','Ondo East','Ondo West','Ose','Owo'],
  Osun: ['Atakunmosa East','Atakunmosa West','Aiyedaade','Aiyedire','Boluwaduro','Boripe','Ede North','Ede South','Egbedore','Ejigbo','Ife Central','Ife East','Ife North','Ife South','Ifedayo','Ifelodun','Ila','Ilesa East','Ilesa West','Irepodun','Irewole','Isokan','Iwo','Obokun','Odo Otin','Ola Oluwa','Olorunda','Oriade','Orolu','Osogbo'],
  Oyo: ['Afijio','Akinyele','Atiba','Atisbo','Egbeda','Ibadan North','Ibadan North-East','Ibadan North-West','Ibadan South-East','Ibadan South-West','Ibarapa Central','Ibarapa East','Ibarapa North','Ido','Irepo','Iseyin','Itesiwaju','Iwajowa','Kajola','Lagelu','Ogbomosho North','Ogbomosho South','Ogo Oluwa','Olorunsogo','Oluyole','Ona Ara','Orelope','Ori Ire','Oyo East','Oyo West','Saki East','Saki West','Surulere'],
  Plateau: ['Barkin Ladi','Bassa','Bokkos','Jos East','Jos North','Jos South','Kanam','Kanke','Langtang North','Langtang South','Mangu','Mikang','Pankshin',"Qua'an Pan",'Riyom','Shendam','Wase'],
  Rivers: ['Abua/Odual','Ahoada East','Ahoada West','Akuku-Toru','Andoni','Asari-Toru','Bonny','Degema','Eleme','Emohua','Etche','Gokana','Ikwerre','Khana','Obio/Akpor','Ogba/Egbema/Ndoni','Ogu/Bolo','Okrika','Omuma','Opobo/Nkoro','Oyigbo','Port Harcourt','Tai'],
  Sokoto: ['Binji','Bodinga','Dange Shuni','Gada','Goronyo','Gudu','Gwadabawa','Illela','Isa','Kebbe','Kware','Rabah','Sabon Birni','Shagari','Silame','Sokoto North','Sokoto South','Tambuwal','Tangaza','Tureta','Wamako','Wurno','Yabo'],
  Taraba: ['Ardo Kola','Bali','Donga','Gashaka','Gassol','Ibi','Jalingo','Karim Lamido','Kurmi','Lau','Sardauna','Takum','Ussa','Wukari','Yorro','Zing'],
  Yobe: ['Bade','Bursari','Damaturu','Fika','Fune','Geidam','Gujba','Gulani','Jakusko','Karasuwa','Machina','Nangere','Nguru','Potiskum','Tarmuwa','Yunusari','Yusufari'],
  Zamfara: ['Anka','Bakura','Birnin Magaji/Kiyaw','Bukkuyum','Bungudu','Gummi','Gusau','Kaura Namoda','Maradun','Maru','Shinkafi','Talata Mafara','Chafe','Zurmi'],
};

/* ── 2. State → LGA cascade ────────────────────────────────────────── */
const stateSelect = document.querySelector('#state_origin');
const lgaSelect   = document.querySelector('#lga');

function resetLgaSelect(label = 'Select state first') {
  lgaSelect.innerHTML = '';
  const opt = document.createElement('option');
  opt.value = '';
  opt.textContent = label;
  lgaSelect.appendChild(opt);
}

if (stateSelect && lgaSelect) {
  stateSelect.addEventListener('change', () => {
    const lgas = lgasByState[stateSelect.value] || [];
    resetLgaSelect(lgas.length ? 'Select LGA' : 'Select state first');
    lgaSelect.disabled = lgas.length === 0;
    lgas.forEach((lga) => {
      const opt = document.createElement('option');
      opt.value = lga;
      opt.textContent = lga;
      lgaSelect.appendChild(opt);
    });
  });
}

/* ── 3. CSV drag-drop zone ─────────────────────────────────────────── */
const csvInput      = document.getElementById('student_csv');
const csvDropZone   = document.getElementById('csvDropZone');
const fileInfo      = document.getElementById('fileInfo');
const fileNameSpan  = document.getElementById('fileName');
const fileClearBtn  = document.getElementById('fileClear');
const csvSubmitBtn  = document.getElementById('csvSubmitBtn');
const csvPreviewWrap  = document.getElementById('csvPreviewWrap');
const csvPreviewHead  = document.getElementById('csvPreviewHead');
const csvPreviewBody  = document.getElementById('csvPreviewBody');
const csvPreviewNote  = document.getElementById('csvPreviewNote');

function formatBytes(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / 1048576).toFixed(2) + ' MB';
}

function handleFile(file) {
  if (!file) return;

  // Validate extension in JS too
  const ext = file.name.split('.').pop().toLowerCase();
  if (ext !== 'csv') {
    showFileError('Only .csv files are accepted.');
    return;
  }
  if (file.size > 5 * 1024 * 1024) {
    showFileError('File exceeds the 5 MB size limit.');
    return;
  }

  // Show file info row
  fileNameSpan.textContent = `${file.name}  (${formatBytes(file.size)})`;
  fileInfo.classList.add('is-visible');
  csvDropZone.style.display = 'none';
  csvSubmitBtn.disabled = false;

  // Parse and render preview
  renderCsvPreview(file);
}

function showFileError(msg) {
  clearFile();
  // Reuse flash styling — create a temporary inline message
  const errDiv = document.createElement('p');
  errDiv.style.cssText = 'color:#c0392b;font-size:13px;margin-top:6px;';
  errDiv.textContent = msg;
  csvDropZone.after(errDiv);
  setTimeout(() => errDiv.remove(), 4000);
}

function clearFile() {
  csvInput.value = '';
  fileInfo.classList.remove('is-visible');
  csvDropZone.style.display = '';
  csvSubmitBtn.disabled = true;
  csvPreviewWrap.classList.remove('is-visible');
  csvPreviewHead.innerHTML = '';
  csvPreviewBody.innerHTML = '';
  csvPreviewNote.textContent = '';
}

// Click on drop zone triggers file input
csvDropZone && csvDropZone.addEventListener('click', () => csvInput.click());
csvDropZone && csvDropZone.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); csvInput.click(); }
});

// Drag events
['dragenter', 'dragover'].forEach(evt =>
  csvDropZone && csvDropZone.addEventListener(evt, (e) => {
    e.preventDefault();
    csvDropZone.classList.add('drag-over');
  })
);
['dragleave', 'drop'].forEach(evt =>
  csvDropZone && csvDropZone.addEventListener(evt, (e) => {
    e.preventDefault();
    csvDropZone.classList.remove('drag-over');
  })
);
csvDropZone && csvDropZone.addEventListener('drop', (e) => {
  const file = e.dataTransfer.files[0];
  if (file) {
    // Assign to input for form submission
    const dt = new DataTransfer();
    dt.items.add(file);
    csvInput.files = dt.files;
    handleFile(file);
  }
});

// Normal file input change
csvInput && csvInput.addEventListener('change', () => {
  handleFile(csvInput.files[0]);
});

// Clear button
fileClearBtn && fileClearBtn.addEventListener('click', clearFile);

/* ── 4. Live CSV preview ───────────────────────────────────────────── */
function renderCsvPreview(file) {
  const reader = new FileReader();
  reader.onload = (e) => {
    const text = e.target.result;
    const lines = text.split(/\r?\n/).filter(l => l.trim() !== '');

    if (lines.length === 0) return;

    // Simple CSV row parser (handles quoted commas)
    function parseCsvRow(line) {
      const result = [];
      let current = '';
      let inQuotes = false;
      for (let i = 0; i < line.length; i++) {
        const ch = line[i];
        if (ch === '"') {
          inQuotes = !inQuotes;
        } else if (ch === ',' && !inQuotes) {
          result.push(current.trim());
          current = '';
        } else {
          current += ch;
        }
      }
      result.push(current.trim());
      return result;
    }

    const headers = parseCsvRow(lines[0]);
    const dataLines = lines.slice(1);
    const previewRows = dataLines.slice(0, 5);
    const totalData = dataLines.length;

    // Build header
    csvPreviewHead.innerHTML = '';
    headers.forEach(h => {
      const th = document.createElement('th');
      th.textContent = h;
      csvPreviewHead.appendChild(th);
    });

    // Build body
    csvPreviewBody.innerHTML = '';
    previewRows.forEach(line => {
      const cols = parseCsvRow(line);
      const tr = document.createElement('tr');
      headers.forEach((_, i) => {
        const td = document.createElement('td');
        td.textContent = cols[i] ?? '';
        tr.appendChild(td);
      });
      csvPreviewBody.appendChild(tr);
    });

    // Note
    csvPreviewNote.textContent = totalData > 5
      ? `Showing 5 of ${totalData} data rows.`
      : `${totalData} data row${totalData !== 1 ? 's' : ''} found.`;

    csvPreviewWrap.classList.add('is-visible');
  };
  reader.readAsText(file);
}

/* ── 5. Client-side validation for Add Student form ───────────────── */
const addStudentForm = document.getElementById('addStudentForm');

addStudentForm && addStudentForm.addEventListener('submit', (e) => {
  const required = addStudentForm.querySelectorAll('[required]');
  let firstInvalid = null;

  required.forEach(field => {
    field.style.borderColor = '';
    if (!field.value.trim()) {
      field.style.borderColor = '#c0392b';
      if (!firstInvalid) firstInvalid = field;
    }
  });

  if (firstInvalid) {
    e.preventDefault();
    firstInvalid.focus();
    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});

// Reset red border on input
addStudentForm && addStudentForm.querySelectorAll('[required]').forEach(field => {
  field.addEventListener('input', () => { field.style.borderColor = ''; });
  field.addEventListener('change', () => { field.style.borderColor = ''; });
});

/* ── 6. Auto-dismiss flash alert after 8 s ─────────────────────────── */
const flashAlert = document.getElementById('flashAlert');
if (flashAlert) {
  setTimeout(() => {
    flashAlert.style.transition = 'opacity 0.5s ease';
    flashAlert.style.opacity = '0';
    setTimeout(() => flashAlert.remove(), 500);
  }, 8000);
}

/* ── 7. Student records search & filter ────────────────────────────── */
const studentSearch     = document.getElementById('studentSearch');
const programmeFilter   = document.getElementById('programmeFilter');
const studentTableBody  = document.getElementById('studentTableBody');
const recordsCountNote  = document.getElementById('recordsCountNote');

function filterStudentTable() {
  if (!studentTableBody) return;

  const query   = (studentSearch ? studentSearch.value : '').toLowerCase().trim();
  const progVal = programmeFilter ? programmeFilter.value : '';
  const rows    = studentTableBody.querySelectorAll('.student-row');

  // Remove any existing no-results row
  const noResults = studentTableBody.querySelector('.no-results-row');
  if (noResults) noResults.remove();

  let visible = 0;

  rows.forEach((row) => {
    const cells = row.querySelectorAll('td');
    // Searchable columns: name (1), reg_no (2), department (6)
    const name  = cells[1] ? cells[1].textContent.toLowerCase() : '';
    const regNo = cells[2] ? cells[2].textContent.toLowerCase() : '';
    const dept  = cells[6] ? cells[6].textContent.toLowerCase() : '';
    // Programme column (4)
    const prog  = cells[4] ? cells[4].textContent.trim() : '';

    const matchesSearch = !query || name.includes(query) || regNo.includes(query) || dept.includes(query);
    const matchesProg   = !progVal || prog === progVal;

    if (matchesSearch && matchesProg) {
      row.style.display = '';
      visible++;
    } else {
      row.style.display = 'none';
    }
  });

  // Update count note
  if (recordsCountNote) {
    const total = rows.length;
    if (query || progVal) {
      recordsCountNote.textContent = `Showing ${visible} of ${total} record${total !== 1 ? 's' : ''}.`;
    } else {
      recordsCountNote.textContent = '';
    }
  }

  // Show no-results message when everything is hidden
  if (visible === 0 && rows.length > 0) {
    const colCount = rows[0].querySelectorAll('td').length;
    const tr = document.createElement('tr');
    tr.className = 'no-results-row';
    const td = document.createElement('td');
    td.colSpan = colCount;
    td.textContent = 'No students match your search.';
    tr.appendChild(td);
    studentTableBody.appendChild(tr);
  }
}

// Debounce helper for search input
let searchTimer;
studentSearch && studentSearch.addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(filterStudentTable, 200);
});

programmeFilter && programmeFilter.addEventListener('change', filterStudentTable);
