    @extends('layout.main')

    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="card card-default">
                <div class="card-header">
                    <h4 class="card-title">Gates Monitoring Report</h4>
                </div>

                <div class="card-body">

                    {{-- Filter --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label><b>Filter Tanggal</b></label>
                            <input type="date" id="filter_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label><b>Filter Source</b></label>
                            <select id="filter_source" class="form-control">
                                @foreach($sources as $src)
                                    <option value="{{ $src->id }}">{{ $src->location_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label><b>Interval (Menit)</b></label>
                            <select id="filter_interval" class="form-control">
                                <option value="5">5 Menit</option>
                                <option value="15" selected>15 Menit</option>
                                <option value="30">30 Menit</option>
                                <option selected value="60">60 Menit</option>
                            </select>
                        </div>
                    </div>

                    <div class="card mb-3" id="utilCard">
                        <div class="card-header d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                            <h5 class="font-weight-bold mb-0">Utilisasi Gate (%)</h5>

                            <div class="d-flex align-items-center ml-auto">
                                <select id="filter_gate_util" class="form-control form-control-sm d-inline-block" style="width:140px;">
                                    <option value="all">Semua Gate</option>
                                    <option value="G01">G01</option>
                                    <option value="G02">G02</option>
                                    <option value="G03">G03</option>
                                    <option value="G04">G04</option>
                                    <option value="G05">G05</option>
                                    <option value="G06">G06</option>
                                </select>

                                <button class="btn btn-sm btn-tool ml-2" type="button"
                                    data-toggle="collapse" data-target="#utilCardBody"
                                    aria-expanded="true" aria-controls="utilCardBody">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="collapse show" id="utilCardBody">
                            <div class="card-body">

                                <div class="row">
                                    <!-- Bar Chart -->
                                    <div class="col-md-6 d-flex flex-column align-items-center">
                                        <h6 class="font-weight-bold">Durasi Waktu (Menit)</h6>
                                        <canvas id="barTimeChart" height="150"></canvas>
                                    </div>

                                    <!-- Donut Chart -->
                                    <div class="col-md-6 d-flex flex-column align-items-center">
                                        <h6 class="font-weight-bold">Utilisasi Gate (%)</h6>
                                        <canvas id="utilChart" height="150"></canvas>
                                        <div id="utilSummary" class="mt-2 text-center font-weight-bold"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Collapsible Chart --}}
                    <div class="mb-4 border rounded-lg">
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light border-bottom">
                            <h5 class="font-weight-bold mb-0">Gate Book</h5>
                            <button class="btn btn-sm btn-tool" type="button" data-toggle="collapse" data-target="#gateChartWrapper" aria-expanded="true" aria-controls="gateChartWrapper">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="gateChartWrapper">
                            <div id="gateChartContainer" class="relative w-full min-w-[900px] p-2 overflow-auto"></div>
                        </div>
                    </div>

                    {{-- Tabel Data (opsional) --}}
                    <table id="gates-table" class="table table-bordered table-striped mt-4">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>No Shipment</th>
                                <th>Gate</th>
                                <th>Point (Source)</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Type</th>
                                <th>Duration (Minutes)</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
            <!-- Modal Activity -->
            <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg">

                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title font-weight-bold" id="activityModalLabel">Detail Status Gate</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">

                            <!-- Judul Slot -->
                            <div id="slotTitle" class="h5 font-weight-bold text-primary mb-2">
                                <!-- ex: G02 – Slot 22:00 – 23:00 -->
                            </div>

                            <!-- Status -->
                            <div id="slotStatus" class="mb-3 font-weight-bold text-success">
                                <!-- ex: Aktivitas: Slot Kosong -->
                            </div>

                            <!-- Segmen -->
                            <div id="slotSegment" class="text-muted mb-3">
                                <!-- ex: Segmen Jadwal Utama: 00:00 – 06:00 -->
                            </div>

                            <!-- STATUS BUTTON -->
                            <div class="text-center mb-3">
                                <button class="btn btn-success btn-block font-weight-bold" id="slotGeneralStatus">
                                    <!-- STATUS UMUM: BUKA -->
                                </button>
                            </div>

                            <!-- Keterangan -->
                            <label class="font-weight-bold">Keterangan:</label>
                            <div class="p-2 bg-light rounded border" id="slotDescription">
                                <!-- dynamic -->
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button class="btn btn-primary btn-block" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    $(document).ready(function() {

        window.gateOperational = {
            @foreach ($gateOperational as $gate => $g)
                "{{ $gate }}": {
                    timestart: "{{ $g['timestart'] }}",
                    timeend:   "{{ $g['timeend'] }}"
                },
            @endforeach
        };

        const gateList = ['G01','G02','G03','G04','G05','G06'];
        let currentResolutionMins = 60; // 1 jam default

        // Ambil data dari server dan buat grid chart
        function loadChart(date, source) {
            $.ajax({
                url: "{{ route('report.gates.data') }}",
                data: { date: date, source: source },
                success: function(res) {
                    renderGrid(res.data);
                }
            });
        }

        // Transform data server menjadi struktur per gate
        function toMinutes(timeStr) {
            const [h, m] = timeStr.split(':').map(Number);
            return h*60 + m;
        }

        function transformData(data) {
            const schedule = {};
            gateList.forEach(g => schedule[g] = []);

            data.forEach(row => {
                let gateId = row.gate || row.gate_name;
                gateId = 'G' + gateId.replace(/\D/g,'').padStart(2,'0');

                console.log("RAW gate:", row.gate, "→ PARSED:", gateId);

                const start = (row.timestart || '00:00:00').slice(0,5);
                const end   = (row.timeend   || start).slice(0,5);

                schedule[gateId].push({
                    start: toMinutes(start),
                    end: toMinutes(end),
                    truck: row.truck || '-',
                    type: row.type || '-',
                    noshipment: row.noshipment || '',
                    color: row.color || '#0a8830ff' // default merah
                });
            });

            return schedule;
        }

        function renderGrid(data) {

            const schedule = transformData(data);
            const container = document.getElementById('gateChartContainer');
            container.innerHTML = '';
            const interval = parseInt($('#filter_interval').val()) || 30;

            // Header...
            const header = document.createElement('div');
            header.className = 'd-flex border-bottom font-weight-bold';
            header.innerHTML = '<div class="p-1 border-right" style="width:80px;">Waktu</div>' +
                gateList.map(g => `<div class="p-1 border-right text-center" style="flex:1;">${g}</div>`).join('');
            container.appendChild(header);

            // Waktu slot
            for(let hour=0; hour<24; hour++){
                for(let min=0; min<60; min+=interval){
                    const slotTime = `${String(hour).padStart(2,'0')}:${String(min).padStart(2,'0')}`;
                    const slotMinutes = toMinutes(slotTime);

                    const row = document.createElement('div');
                    row.className = 'd-flex border-bottom';

                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'p-1 border-right';
                    timeDiv.style.width = '80px';
                    timeDiv.style.fontSize = '12px';
                    timeDiv.textContent = slotTime;
                    row.appendChild(timeDiv);

                    gateList.forEach(gateId => {

                        const cellData = schedule[gateId].filter(s => s.start <= slotMinutes && s.end > slotMinutes);

                        const cellDiv = document.createElement('div');
                        cellDiv.className = `p-1 border-right text-center`;
                        cellDiv.style.flex = '1';
                        cellDiv.style.fontSize = '12px';

                        // ================
                        // 🚀 PENTING — PENGECEKAN JAM OPERASIONAL
                        // ================
                        const op = window.gateOperational[gateId];

                        if (!op) {
                            console.warn("Gate operational not found:", gateId);
                            cellDiv.style.backgroundColor = "#ccc";
                            cellDiv.textContent = "N/A";
                            row.appendChild(cellDiv);
                            return;
                        }

                        const slotHHMMSS = slotTime + ":00";  // misal: "22:30:00"

                        const isInOperational =
                            slotHHMMSS >= op.timestart && slotHHMMSS < op.timeend;

                        // ================
                        // Logic warna
                        // ================
                        if (cellData.length) {
                            // BOOKED
                            cellDiv.style.backgroundColor = cellData[0].color;
                            cellDiv.style.color = "#fff";
                            cellDiv.textContent = `${cellData[0].truck} (${cellData[0].type})`;
                        } else {
                            // SEL KOSONG → AVAILABLE / UNAVAILABLE
                            if (!isInOperational) {
                                cellDiv.style.backgroundColor = "#d3d3d3";
                                cellDiv.textContent = "Unavailable";
                            } else {
                                cellDiv.style.backgroundColor = "#66afccff";
                                cellDiv.textContent = "Available";
                            }
                        }

                        // simpan metadata
                        cellDiv.dataset.gate = gateId;
                        cellDiv.dataset.time = slotTime;
                        cellDiv.dataset.activity = JSON.stringify(cellData);
                        cellDiv.dataset.operational = isInOperational ? "1" : "0";

                        row.appendChild(cellDiv);
                    });

                    container.appendChild(row);
                }
            }
            document.querySelectorAll('#gateChartContainer div[data-activity]').forEach(cell => {
                cell.addEventListener('click', function () {

                    const list = JSON.parse(this.dataset.activity);
                    console.log("ACTIVITY LIST:", list);
                    if (list.length) {
                        console.log("NOSHIPMENT:", list[0].noshipment);
                    }
                    const gate  = this.dataset.gate;
                    const time  = this.dataset.time;
                    const isInOperational = this.dataset.operational === "1";

                    // Jika tidak ada activity → modal tetap tampil untuk slot kosong
                    const item = list.length ? list[0] : null;

                    // Hitung end time (interval 30 menit atau 60 menit)
                    let [h, m] = time.split(":").map(Number);
                    let interval = parseInt($('#filter_interval').val()) || 30;
                    let endMinutes = h * 60 + m + interval;
                    let endHH = String(Math.floor(endMinutes / 60) % 24).padStart(2, "0");
                    let endMM = String(endMinutes % 60).padStart(2, "0");

                    let slotRange = `${time} - ${endHH}:${endMM}`;

                    //
                    // 🟦 SET JUDUL MODAL
                    //
                    $('#slotTitle').text(`${gate} – Slot ${slotRange}`);

                    //
                    // 🟦 STATUS
                    //
                    if (!item) {
                        // Slot kosong → cek jam operasional
                        if (!isInOperational) {
                            $('#slotStatus').text("Aktivitas: Unavailable");
                            $('#slotGeneralStatus')
                                .text("UNAVAILABLE")
                                .removeClass('btn-success btn-danger')
                                .addClass('btn-secondary');

                            $('#slotDescription').text("Slot ini berada di luar jam operasional.");
                        } else {
                            $('#slotStatus').text("Aktivitas: Available");
                            $('#slotGeneralStatus')
                                .text("OPEN")
                                .removeClass('btn-danger btn-secondary')
                                .addClass('btn-success');

                            $('#slotDescription').text("Slot kosong, siap untuk booking.");
                        }
                    } else {
                        // Slot BOOKED
                        $('#slotStatus').text(`Aktivitas: ${item.type}`);
                        $('#slotGeneralStatus')
                            .text("BOOKED")
                            .removeClass('btn-success btn-secondary')
                            .addClass('btn-danger');

                        $('#slotDescription').text(
                            `Sedang digunakan oleh ${item.noshipment} (Nopol: ${item.truck}).`
                        );
                    }

                    // 🟩 TAMPILKAN MODAL
                    $('#activityModal').modal('show');

                });
            });
            window.latestSchedule = schedule;
            calculateUtilization();
            calculateGateDurations();
        }


        let table = null;

        // Load table DataTables
        function loadTable(date, source) {
            table = $('#gates-table').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: { url: "{{ route('report.gates.data') }}", data: { date, source } },
                columns: [
                    { data: 'id' },
                    { data: 'noshipment' },
                    { data: 'gate_name' },
                    { data: 'source_name' },
                    { data: 'timestart' },
                    { data: 'timeend' },
                    { data: 'type' },
                    { data: 'duration_minutes' }
                ]
            });

            // ================
            // 🚀 Fix penting → ambil gateOperational dari server via DataTables
            // ================
            table.on('xhr.dt', function () {
                let json = table.ajax.json();

                if (json.gateOperational) {
                    window.gateOperational = {};

                    Object.keys(json.gateOperational).forEach(gate => {
                        const op = json.gateOperational[gate];

                        // pastikan bentuknya uniform
                        window.gateOperational[gate] = {
                            timestart: op.timestart ?? op['timestart'],
                            timeend:   op.timeend   ?? op['timeend']
                        };
                    });

                    console.log("UPDATED gateOperational:", window.gateOperational);
                }
            });
        }

        // Inisialisasi load pertama
        function reloadAll() {
            const date = $('#filter_date').val();
            const source = $('#filter_source').val();
            loadChart(date, source);
            loadTable(date, source);
        }

        $('#filter_date, #filter_source, #filter_interval').on('change', function(){
            const date = $('#filter_date').val();
            const source = $('#filter_source').val();
            loadChart(date, source);
            loadTable(date, source);
        });

        // Load pertama
        $(document).ready(function() {
            const date = $('#filter_date').val();
            const source = $('#filter_source').val();
            loadChart(date, source);
            loadTable(date, source);
        });


        let utilChart = null;
        let barTimeChart = null;

        // Minimize Card
        $("#toggleUtilCard").on("click", function () {
            $("#utilCardBody").toggle();
            $(this).text($(this).text() === "Minimize" ? "Maximize" : "Minimize");
        });

        $("#filter_gate_util").on("change", function () {
            calculateUtilization();
        });

        function renderBarTimeChart(bookedMinutes, availableMinutes) {
            const ctx = document.getElementById("barTimeChart").getContext("2d");

            bookedMinutes = parseInt(bookedMinutes) || 0;
            availableMinutes = parseInt(availableMinutes) || 0;

            if (barTimeChart) barTimeChart.destroy();

            barTimeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Durasi Total (menit)'],
                    datasets: [
                        {
                            label: 'Available',
                            data: [availableMinutes],
                            backgroundColor: '#66afccff',
                            stack: 'timeStack'   // 🔥 WAJIB
                        },
                        {
                            label: 'Booked',
                            data: [bookedMinutes],
                            backgroundColor: '#3877b6ff',
                            stack: 'timeStack'   // 🔥 WAJIB
                        }
                    ]
                },
                options: {
                    responsive: true,
                    indexAxis: 'x', // 🔥 pastikan vertical & bukan horizontal
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: { display: true }
                    }
                }
            });
        }

        function mergeIntervals(intervals) {
            if (!intervals || intervals.length === 0) return [];
            intervals.sort((a,b) => a.start - b.start);
            const merged = [ Object.assign({}, intervals[0]) ];

            for (let i = 1; i < intervals.length; i++) {
                const cur = intervals[i];
                const last = merged[merged.length - 1];
                if (cur.start <= last.end) {
                    // overlap -> extend
                    last.end = Math.max(last.end, cur.end);
                } else {
                    merged.push(Object.assign({}, cur));
                }
            }
            return merged;
        }

        function calculateUtilization() {
            if (!window.latestSchedule) return;

            const schedule = window.latestSchedule;
            const selectedGate = $("#filter_gate_util").val();

            let totalOperationalMinutes = 0;
            let totalBookedMinutes = 0;

            Object.keys(schedule).forEach(gate => {
                if (selectedGate !== "all" && gate !== selectedGate) return;

                const gateSched = schedule[gate] || [];
                const op = window.gateOperational[gate];

                if (!op || !op.timestart || !op.timeend) {
                    console.warn("No operational hours for", gate, op);
                    return;
                }

                // parse op start/end (minutes). allow shift over midnight
                let opStart = toMinutes(op.timestart.slice(0,5));
                let opEnd = toMinutes(op.timeend.slice(0,5));
                if (opEnd <= opStart) {
                    // crosses midnight -> extend opEnd by 24h
                    opEnd += 1440;
                }
                const opMinutes = opEnd - opStart;
                totalOperationalMinutes += opMinutes;

                // build intervals clipped to operational window
                const intervals = [];
                gateSched.forEach(s => {
                    // copy s.start/s.end (they are minutes in [0..1439])
                    let sStart = s.start;
                    let sEnd = s.end;

                    // If booking might cross midnight, normalize by allowing +1440 when needed
                    // Here assume bookings don't span days; if they might, adjust data upstream.
                    if (sEnd <= sStart) sEnd += 1440;

                    // clip to op window
                    const startClipped = Math.max(sStart, opStart);
                    const endClipped = Math.min(sEnd, opEnd);

                    if (endClipped > startClipped) {
                        intervals.push({ start: startClipped, end: endClipped });
                    }
                });

                // merge overlapping bookings (prevents double-count)
                const merged = mergeIntervals(intervals);

                // sum booked minutes for this gate
                const gateBooked = merged.reduce((sum, iv) => sum + (iv.end - iv.start), 0);
                totalBookedMinutes += gateBooked;

                // debug per gate
                console.log(`[UTIL] ${gate} op ${opStart}->${opEnd} (${opMinutes}m) bookedIntervals:`, intervals, "merged:", merged, `booked=${gateBooked}m`);
            });

            // totals
            const totalAvailableMinutes = Math.max(0, totalOperationalMinutes - totalBookedMinutes);

            const bookedPct = totalOperationalMinutes
                ? ((totalBookedMinutes / totalOperationalMinutes) * 100).toFixed(1)
                : 0;
            const availablePct = (100 - bookedPct).toFixed(1);

            // Render Chart
            renderUtilChart(bookedPct, availablePct);

            // show minutes (integer)
            renderBarTimeChart(Math.round(totalBookedMinutes), Math.round(totalAvailableMinutes));

            $("#utilSummary").html(`
                <span class="text-danger">Booked: ${bookedPct}% (${Math.round(totalBookedMinutes)} m)</span> |
                <span class="text-success">Available: ${availablePct}% (${Math.round(totalAvailableMinutes)} m)</span>
            `);

            // general debug
            console.log("[UTIL TOTAL] operational:", totalOperationalMinutes, "booked:", totalBookedMinutes, "available:", totalAvailableMinutes);
        }

        function renderUtilChart(bookedPct, availPct) {
            const ctx = document.getElementById("utilChart").getContext("2d");

            if (utilChart !== null) utilChart.destroy();

            utilChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Booked (%)', 'Available (%)'],
                    datasets: [{
                        data: [bookedPct, availPct],
                        backgroundColor: ['#66afccff', '#3877b6ff'],
                    }]
                },
                options: {
                    responsive: true,
                    cutout: "55%",
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }

        // Javascript: fungsi hitung total menit per gate dan render bar chart
        let timeBarChart = null;

        function calculateGateDurations() {
        if (!window.latestSchedule) return;

            const schedule = window.latestSchedule;
            const durations = {};

            Object.keys(schedule).forEach(gate => {
            let total = 0;
            schedule[gate].forEach(s => {
                const dur = s.end - s.start;
                total += dur;
                });
                durations[gate] = total;
            });

            renderTimeBarChart(durations);
        }

        function renderTimeBarChart(durations) {
        const ctx = document.getElementById("timeBarChart").getContext("2d");

        if (timeBarChart !== null) timeBarChart.destroy();

        timeBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
            labels: Object.keys(durations),
            datasets: [{
            label: 'Total Waktu (menit)',
            data: Object.values(durations),
            backgroundColor: '#3399ff'
            }]
        },
            options: {
                responsive: true,
                plugins: {
                legend: { display: false }
                },
                scales: {
                y: {
                beginAtZero: true
                        }
                    }
                }
            });
        }

    });
    </script>
    @endpush
    @endsection
