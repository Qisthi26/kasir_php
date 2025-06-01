<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Kantin Telkom</title>

    <style>
        body {
            padding-top: 60px;
        }
    </style>
</head>
<body>

    <!-- navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-light-subtle border-bottom border-danger border-4">
        <div class="container">
            <!-- logo -->
            <a class="navbar-brand" href="#about">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJBBUnkE6bLR4vXkghOR5P3sWRF3V3LjA3ZA&s" alt="logo" width="30">
            </a>
            <!-- menu navbar -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link"  href="#about">About Kantin</a>
                    </li>
                    <li class="nav-item hover">
                        <a class="nav-link" href="list2.php">Cafeteria List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list2.php #ts">How to Buy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Jumbotron Section -->
    <section class="pt-5">
        <div class="container py-5">
            <!-- Judul -->
            <h2 class="text-center fw-bold mb-4">Cafeteria</h2>
            <div class="row align-items-center justify-content-center">

            <!-- Gambar Kantin -->
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="shadow rounded overflow-hidden" style="height: 300px;">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-Wn0wYYlJX1a8whqKxKiqodeNxNrZ6IpSgQ&s" alt="Gambar Kantin"
                    class="img-fluid h-100 w-100 object-fit-cover">
                </div>
            </div>

            <!-- Video Kantin -->
            <div class="col-md-6">
                <div class="shadow rounded overflow-hidden" style="height: 300px;">
                    <div class="ratio ratio-16x9 h-100">
                        <iframe src="https://www.youtube.com/embed/VIDEO_ID" 
                                title="Video Kantin" 
                                allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <br>

    <!-- About Kantin Section -->
    <section id="about" class="py-5" style="scroll-margin-top: 80px;">
        <div class="container">
            <div class="row align-items-center">

                <!-- Logo Kantin -->
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJBBUnkE6bLR4vXkghOR5P3sWRF3V3LjA3ZA&s" alt="Logo Kantin" width="150" class="img-fluid">
                </div>

                <!-- Deskripsi Kantin -->
                <div class="col-md-8">
                    <h3 class="fw-bold mb-3">About Kantin</h3>
                    <p class="text-secondary">
                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Rem corporis maxime natus dicta reiciendis laudantium minus, ratione quis quisquam, vitae in eius expedita cumque iure omnis optio assumenda beatae sequi!
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p class="mb-0">&copy; 2025 Muna Aliya Bil Qisthi - SMK Telkom Jakarta.</p>
        </div>
    </footer>
</body>
</html>