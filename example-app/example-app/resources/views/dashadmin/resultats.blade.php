@extends('dashadmin.home')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashadmin') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Les campagnes</a></li>
                <li class="breadcrumb-item active">Critères et Réponses</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h1 class="text-center" style="font-size: 2rem;">
                    Résultat du Champ : <span style="color: #008000;">{{ $nomChampAssocie }}</span>
                </h1>
      
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

                <style>
                        #downloadPdf {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 10px;
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }

    #downloadPdf i {
        margin: 0;
    }

    #downloadPdf:hover {
        background-color: #0056b3;
    }
                    .reference-title {
                        color: #007bff;
                        font-weight: bold;
                    }

                    .critere-title {
                        color: #28a745;
                        font-weight: bold;
                    }

                    .preuve-title {
                        color: #dc3545;
                        font-weight: bold;
                    }

                    .critere-box {
                        margin-bottom: 20px;
                        padding: 15px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        background-color: #f9f9f9;
                    }

                    .preuve-options {
                        margin-left: 20px;
                        padding: 10px;
                        border-left: 3px solid #007bff;
                    }

                    .preuve-options span {
                        color: #000; /* Texte noir pour les preuves */
                    }
                </style>
<button id="downloadPdf" class="btn btn-primary">
    <i class="fas fa-download"></i>
</button>

                @if($criteria->count())
                    @php
                        $currentReference = null;
                        $displayedCriteria = [];
                        $displayedProofs = [];
                    @endphp

                    <div class="custom-container">
                        @foreach($criteria as $criterion)
                            @if(!isset($displayedCriteria[$criterion->critere->id]))
                                @if($currentReference !== $criterion->critere->reference->nom)
                                    @if($currentReference)
                                        </div> <!-- Close previous reference block -->
                                    @endif

                                    <div class="reference-block">
                                        <h4 class="reference-title"><span style="color: #000000;"><b>Reference : </b></span>{{ $criterion->critere->reference->nom ?? 'No Reference Name' }}</h4>
                                        @php
                                            $currentReference = $criterion->critere->reference->nom;
                                        @endphp
                                @endif

                                <div class="critere-box">
                                    <h5 class="critere-title"><span style="color: #000000;"><b>{{$criterion->critere->signature}} : </b></span>{{ $criterion->critere->nom ?? 'No Criterion Name' }}</h5>

                                    @foreach($criterion->critere->preuves as $preuve)
                                        @if(!isset($displayedProofs[$preuve->id]))
                                            @php
                                                $displayedProofs[$preuve->id] = true;
                                            @endphp

                                            <div class="preuve-options">
                                                <span class="preuve-title"><b>Preuve :</b> {{ $preuve->description ?? 'No Description' }}</span>
                                                <!-- Vérifiez l'ID de la preuve avant d'afficher le score et les informations -->
                                                @php
                                                    $eval = $criteria->where('idfiliere', $filiereInviteId)
                                                                     ->where('idpreuve', $preuve->id)
                                                                     ->first();
                                                @endphp

                                                @if($eval)
                                                    @if($eval->score === 2)
                                                        @if($preuve->fichier) 
                                                            <a href="{{ route('downloadFile', ['filename' => basename($preuve->fichier->fichier)]) }}">Télécharger</a>
                                                        @else
                                                            <p>No file path available.</p>
                                                        @endif
                                                    @elseif($eval->score === -1)
                                                        <span class="text-danger">Non</span>
                                                    @elseif($eval->score === 0)
                                                        <span class="text-warning">{{ $eval->commentaire ?? 'No Comment' }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @php
                                    $displayedCriteria[$criterion->critere->id] = true;
                                @endphp
                            @endif
                        @endforeach
                        </div> <!-- Close last reference block -->
                    </div>
                    <div class="chart-container mt-5">
                        <h2 class="text-center">Graphique des Scores des Critères</h2>
                        <canvas id="criteriaChart" width="400" height="200"></canvas>
                    </div>
                @else
                    <p>No criteria available.</p>
                @endif
            </div>
        </div>
    </section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('criteriaChart').getContext('2d');

var criteriaLabels = @json(array_column($criteresScores, 'critere')); // Labels from criteria names
var criteriaScores = @json(array_column($criteresScores, 'score')); // Aggregated scores

var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: criteriaLabels,
        datasets: [{
            label: 'Scores des Critères',
            data: criteriaScores,
            backgroundColor: criteriaScores.map(score => score > 0 ? 'green' : score < 0 ? 'yellow' : 'red'),
            borderColor: 'black',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            }
        }
    }
});

    });
    const { jsPDF } = window.jspdf;
    document.getElementById('downloadPdf').addEventListener('click', function () {
    var element = document.querySelector('.card-body'); // Sélectionnez la partie à convertir en PDF

    html2canvas(element, {
        scale: 2 // Augmente la qualité du canvas
    }).then(function(canvas) {
        var imgData = canvas.toDataURL('image/png');
        var pdf = new jsPDF('p', 'mm', 'a4');

        var imgWidth = 210; // Largeur du PDF en mm (A4)
        var pageHeight = 295; // Hauteur du PDF en mm (A4)
        var imgHeight = canvas.height * imgWidth / canvas.width;
        var heightLeft = imgHeight;

        var position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save('Critères_et_Réponses.pdf'); // Nom du fichier PDF téléchargé
    });
});


</script>

@endsection
