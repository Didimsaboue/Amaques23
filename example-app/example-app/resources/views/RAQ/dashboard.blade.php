@extends('RAQ.home')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashRAQ') }}">messages.Dashboard</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12">
                <div class="row">
                    <!-- Referentiel Card -->
                    <div class="col-xxl-2 col-md-3 mb-2">
                        <div class="card info-card referentiel-card">
                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" id="referentielDropdown">
                                    <li class="dropdown-header text-start">
                                        <h6>Filtrer</h6>
                                    </li>
                                    @foreach($referentiels as $referentiel)
                                    <li><a class="dropdown-item" href="#" data-id="{{ $referentiel->id }}">{{ $referentiel->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Référentiel <span>| Sélectionner</span></h5>
                            </div>
                        </div>
                    </div><!-- End Referentiel Card -->

                    <!-- Champs Card -->
                    <div class="col-xxl-2 col-md-3 mb-2">
                        <div class="card info-card champs-card">
                            <div class="card-body">
                                <h5 class="card-title">Champs <span>| Référentiel</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="champsCount">Chargement...</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Champs Card -->

                    <!-- References Card -->
                    <div class="col-xxl-2 col-md-3 mb-2">
                        <div class="card info-card references-card">
                            <div class="card-body">
                                <h5 class="card-title">Références <span>| Référentiel</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-collection"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="referencesCount">Chargement...</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End References Card -->

                    <!-- Criteres Card -->
                    <div class="col-xxl-2 col-md-3 mb-2">
                        <div class="card info-card criteres-card">
                            <div class="card-body">
                                <h5 class="card-title">Critères <span>| Référentiel</span></h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-list-check"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 id="criteresCount">Chargement...</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Criteres Card -->

                </div>
            </div><!-- End Left side columns -->

            <!-- Selected Referentiel Display -->
            <div class="col-lg-6 mb-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Référentiel Sélectionné</h5>
                        <div id="selectedReferentielName"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Display and Download Buttons -->
        <div class="card">
            <div class="card-body">
                <h5  class="card-title text-center">Détails</h5>
                <div class="d-flex justify-content-end mb-3">
                    <div>
                        <button id="downloadPDF" class="btn btn-primary me-2">
                            <i class="bi bi-download"></i>  PDF
                        </button>
                        <button id="downloadExcel" class="btn btn-success me-2">
                            <i class="bi bi-download"></i> Excel
                        </button>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Champs</th>
                            <th scope="col">Références</th>
                            <th scope="col">Critères</th>
                        </tr>
                    </thead>
                    <tbody id="detailsTable">
                        <!-- Les données seront insérées ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Line Chart -->
        <div id="lineChart"></div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const referentielDropdown = document.getElementById('referentielDropdown');
        const detailsTable = document.getElementById('detailsTable');
        const selectedReferentielName = document.getElementById('selectedReferentielName');
        const champsCount = document.getElementById('champsCount');
        const referencesCount = document.getElementById('referencesCount');
        const criteresCount = document.getElementById('criteresCount');

        function fetchReferentielData(referentielId) {
            fetch(`/api/referentiel/${referentielId}/data`)
                .then(response => response.json())
                .then(data => {
                    champsCount.innerText = data.champsCount;
                    referencesCount.innerText = data.referencesCount;
                    criteresCount.innerText = data.criteresCount;

                    // Update the table
                    detailsTable.innerHTML = ''; // Clear previous data
                    data.details.forEach(detail => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${detail.champs}</td>
                            <td>${detail.references}</td>
                            <td>${detail.criteres}</td>
                        `;
                        detailsTable.appendChild(row);
                    });

                    // Add total row
                    const totalRow = document.createElement('tr');
                    totalRow.innerHTML = `
                        <td><strong>Total</strong></td>
                        <td>${data.totalReferences}</td>
                        <td>${data.totalCriteres}</td>
                    `;
                    detailsTable.appendChild(totalRow);
                })
                .catch(error => console.error('Error fetching referentiel data:', error));
        }

        function loadDefaultReferentiel() {
            const firstReferentielId = referentielDropdown.querySelector('.dropdown-item').getAttribute('data-id');
            const firstReferentielName = referentielDropdown.querySelector('.dropdown-item').innerText.trim();
            selectedReferentielName.innerText = firstReferentielName;
            fetchReferentielData(firstReferentielId);
        }

        referentielDropdown.addEventListener('click', function(event) {
            if (event.target && event.target.matches('.dropdown-item')) {
                event.preventDefault();
                const referentielId = event.target.getAttribute('data-id');
                const referentielName = event.target.innerText.trim();
                selectedReferentielName.innerText = referentielName;
                fetchReferentielData(referentielId);
            }
        });

        document.querySelector("#downloadPDF").addEventListener('click', () => {
            const element = document.getElementById('detailsTable');
            html2pdf().from(element).save();
        });

        document.querySelector("#downloadExcel").addEventListener('click', () => {
            const wb = XLSX.utils.table_to_book(document.getElementById('detailsTable'), {sheet: "Sheet JS"});
            XLSX.writeFile(wb, "table_data.xlsx");
        });

        // Load the default referentiel on page load
        loadDefaultReferentiel();
    });
</script>

@endsection
