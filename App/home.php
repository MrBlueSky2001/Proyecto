<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inicio</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                background-color: #F8F5F2;
                font-family: 'Open Sans', sans-serif;
                background-size: cover;
                background-position: center;
                transition: background-image 1s ease-in-out;
            }
            .logo {
                max-width: 300px;
            }
            .custom-btn {
                background-color: #000000;
                color: #D4AF37;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                font-weight: 600;
                transition: background-color 0.3s, color 0.3s;
            }
            .custom-btn:hover {
                background-color: #D4AF37;
                color: #000000;
            }
            footer {
                background-color: #2F2F2F;
                color: #FFFFFF;
                text-align: center;
                padding: 10px 0;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Playfair Display', serif;
                color: #000000;
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <div class="container d-flex flex-column justify-content-center align-items-center flex-grow-1">
            <div class="row">
                <div class="col-12 text-center">
                    <img src="img/logo.jpg" alt="Logo" class="img-fluid logo">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="login.php" class="btn custom-btn">Iniciar Sesión</a>
                </div>
            </div>
        </div>

        <?php require_once 'footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Lista de GIFs en la carpeta GIF
            const gifs = ["GIF/01.gif", "GIF/02.gif"];
            let currentGifIndex = 0;

            // Cambia el fondo cada 20 segundos
            setInterval(() => {
                // Cambia al siguiente GIF
                currentGifIndex = (currentGifIndex + 1) % gifs.length;
                document.body.style.backgroundImage = `url('${gifs[currentGifIndex]}')`;
            }, 5000); // 20000ms = 20 segundos

            // Establece el primer GIF al cargar la página
            document.body.style.backgroundImage = `url('${gifs[currentGifIndex]}')`;
        </script>
    </body>
</html>
