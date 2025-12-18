<!DOCTYPE html>
<html lang="en">
<!-- Connects to the Database -->
<?php include 'db.php'; ?> 
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Welcome to St Alphonsus RC Primary School</title>
<style> 

    * {

      margin: 0;

      padding: 0;

      box-sizing: border-box;

      font-family: 'Poppins', sans-serif;

    }

    body {

      background-color: #f7f8fc;

      color: #333;

    }

    /* Navigation */

    .navbar {

      display: flex;

      justify-content: space-between;

      align-items: center;

      background-color: #004080;

      padding: 1rem 2rem;

      color: white;

    }

    .navbar .logo {

      height: 60px;

    }

    .nav-links {

      list-style: none;

      display: flex;

      gap: 1.5rem;

    }

    .nav-links a {

      text-decoration: none;

      color: white;

      font-weight: 500;

    }

    .nav-links a:hover {

      text-decoration: underline;

    }

    /* Hero Section */

    .hero {

      background: url('school-banner.jpg') center/cover no-repeat;

      height: 70vh;

      display: flex;

      align-items: center;

      justify-content: center;

      text-align: center;

      color: white;

      position: relative;

    }

    .hero::after {

      content: "";

      position: absolute;

      inset: 0;

      background: rgba(0, 64, 128, 0.5);

    }

    .hero-content {

      position: relative;

      z-index: 1;

    }

    .hero h1 {

      font-size: 2.5rem;

      margin-bottom: 0.5rem;

    }

    .hero p {

      font-size: 1.2rem;

      margin-bottom: 1rem;

    }

    button {

      background-color: #ffcc00;

      border: none;

      padding: 0.8rem 1.5rem;

      border-radius: 5px;

      cursor: pointer;

      font-weight: 600;

    }

    button:hover {

      background-color: #ffdb4d;

    }

    /* About Section */

    .about {

      padding: 3rem 2rem;

      text-align: center;

    }

    .about h2 {

      color: #004080;

      margin-bottom: 1rem;

    }

    /* Gallery */

    .gallery {

      background-color: #e9efff;

      padding: 3rem 2rem;

      text-align: center;

    }

    .image-grid {

      display: flex;

      flex-wrap: wrap;

      gap: 1rem;

      justify-content: center;

    }

    .image-grid img {

      width: 300px;

      border-radius: 10px;

      box-shadow: 0 4px 8px rgba(0,0,0,0.1);

    }

    /* Footer */

    footer {

      background-color: #004080;

      color: white;

      text-align: center;

      padding: 1rem 0;

      font-size: 0.9rem;

    }
</style> 
</head>
<body>
<header>
<nav class="navbar">
<img src="school logo.png" alt="St Alphonsus RC Primary School Logo" class="logo" />
<ul class="nav-links">
<li><a href="login.php">Login</a></li>
<li><a href="parent_form.php">Parent Form</a></li>
<li><a href="class_form.php">Class Form</a></li>
<li><a href="teacher_form.php">Teacher Form</a></li>
<li><a href="student_form.php">Student Form</a></li>
</ul>
</nav>
<section class="hero">
<div class="hero-content">
<h1>Welcome to St Alphonsus RC Primary School</h1>
<p>“Learning, Growing, and Succeeding Together.”</p>
<button id="learnMoreBtn">Learn More</button>
</div>
</section>
</header>
<main>
<section class="about">
<h2>About Our School</h2>
<p>

        St Alphonsus RC Primary School is a vibrant and caring community dedicated 

        to helping every child reach their full potential. We provide a nurturing 

        environment rooted in faith, respect, and academic excellence.
</p>
</section>
<section class="gallery">
<h2>Our School Life</h2>
<div class="image-grid">
<img src="school1.png" alt="School Playground" />
<img src="school2.webp" alt="Classroom Activity" />
<img src="school3.jpg" alt="Students Learning" />
</div>
</section>
</main>
<footer>
<p>© 2025 St Alphonsus RC Primary School | All Rights Reserved</p>
</footer>
<script>

    document.getElementById('learnMoreBtn').addEventListener('click', () => {

      window.scrollTo({

        top: document.querySelector('.about').offsetTop,

        behavior: 'smooth'

      });

    });
</script>
</body>
</html>
 