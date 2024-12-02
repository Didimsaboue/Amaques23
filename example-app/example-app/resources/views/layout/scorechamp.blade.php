@extends('layout.layout-index')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Graphique des Scores des Critères</title>
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
        <!-- Inclure Font Awesome pour les icônes -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            .custom-container {
                max-width: 1200px;
                margin: auto;
                padding: 20px;
            }

            .box {
                border: 1px solid #000;
                padding: 20px;
                margin-bottom: 20px;
                width: 48%;
                box-sizing: border-box;
                position: relative;
                /* Pour positionner le bouton à l'intérieur */
                overflow: hidden;
                /* Pour masquer l'icône qui déborde */
            }

            .box-title {
                font-weight: bold;
                margin-bottom: 10px;
            }

            .champ-title {
                color: blue;
                /* Couleur bleue uniquement pour le nom du champ */
            }

            .box-footer {
                margin-top: 10px;
                padding: 10px;
                border-top: 1px solid #000;
            }

            .box-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .download-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: #007bff;
                color: white;
                border: none;
                padding: 5px 10px;
                cursor: pointer;
                border-radius: 5px;
                z-index: 1;
            }

            .message-box {
                width: 100%;
                text-align: center;
                padding: 20px;
                background-color: #f0f0f0;
                border: 1px solid #ccc;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <main id="main" class="main">
            <div class="custom-container">
                <div id="chartsContainer" class="box-container"></div>
                <div id="messageBox" class="message-box" style="display: none;">
                    Aucun champ évalué pour le moment.
                </div>
                <button id="go-home" class="btn btn-secondary mt-3">Retour à l'accueil</button>
            </div>
        </main>

        <script>
            async function fetchScores() {
                const response = await fetch('/get-scores');
                return await response.json();
            }

            function generateChart(containerId, champData) {
                const chart = anychart.column3d();
                const labels = champData.criteres.map(critere => critere.critere);

                // Créer les données en prenant en compte les scores
                const data = champData.criteres.map(critere => {
                    let fillColor;
                    if (critere.score > 0) {
                        fillColor = 'green'; // Vert pour positif
                    } else if (critere.score === 0) {
                        fillColor = 'red'; // Rouge pour zéro
                    } else {
                        fillColor = 'yellow'; // Jaune pour négatif
                    }

                    return {
                        x: critere.critere,
                        value: critere.score, // Laisser la valeur négative
                        fill: fillColor
                    };
                });

                // turn on chart animation
                chart.animation(true);

                // make the chart responsive
                chart.bounds(0, 0, '100%', '100%');

                // set chart padding
                chart.padding([10, 40, 5, 20]);

                chart.data(data);
                chart.title(`Scores des Critères - ${champData.champ}`);

                // Ajuster les paramètres de l'axe Y pour afficher correctement les scores négatifs et positifs
                chart.yScale().minimum(-5); // Ajuster la valeur minimale pour que les scores négatifs soient visibles
                chart.yScale().maximum(5); // Ajuster la valeur maximale si nécessaire

                // set titles for axes
                chart.xAxis().title('Critère');
                chart.yAxis().title('Preuves');
                chart.container(containerId);
                chart.draw();
            }


            document.addEventListener('DOMContentLoaded', async () => {
                const scores = await fetchScores();
                const chartsContainer = document.getElementById('chartsContainer');
                const messageBox = document.getElementById('messageBox');

                // Vérifier s'il y a un message à afficher (aucun champ évalué)
                if (scores.hasOwnProperty('message')) {
                    messageBox.style.display = 'block';
                    return;
                }

                scores.forEach((champData, index) => {
                    const box = document.createElement('div');
                    box.className = 'box';

                    const downloadBtn = document.createElement('button');
                    downloadBtn.className = 'download-btn';
                    downloadBtn.id = `download-stats-${index}`;
                    downloadBtn.innerHTML = '<i class="fas fa-download"></i>';
                    downloadBtn.addEventListener('click', () => {
                        html2canvas(box).then(canvas => {
                            const link = document.createElement('a');
                            link.download = `stats-${champData.champ}.png`;
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        });
                    });
                    box.appendChild(downloadBtn);

                    const boxTitle = document.createElement('div');
                    boxTitle.className = 'box-title';
                    boxTitle.innerHTML =
                        `Niveau de conformité du champ: <span class="champ-title">${champData.champ}</span>`;
                    box.appendChild(boxTitle);

                    const canvasId = `chart-${index}`;
                    const div = document.createElement('div');
                    div.id = canvasId;
                    div.style.width = '100%';
                    div.style.height = '400px';
                    box.appendChild(div);

                    const boxFooter = document.createElement('div');
                    boxFooter.className = 'box-footer';
                    boxFooter.innerHTML =
                        `Taux de conformité au critère du <span class="champ-title">${champData.champ}</span> : ${champData.tauxConformite.toFixed(2)}%`;
                    box.appendChild(boxFooter);

                    chartsContainer.appendChild(box);

                    generateChart(canvasId, champData);
                });

                document.getElementById('go-home').addEventListener('click', () => {
                    window.location.href = '/indexevaluation';
                });
            });
        </script>
    </body>

    </html>
@endsection
