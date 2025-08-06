<?php session_start();?>

<?php include_once __DIR__ . '/components/navbar.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Second Chance Shelter</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-box">
            <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>
    <div class="main-home-container">
        <div class="homepage-container">
            <img src="images/Volunteer.jpg" alt="Shelter Community" class="homepage-image margin-left">
            <div class="homepage-content">
                <h1>Welcome to Second Chance Shelter</h1>
                <p>Second Chance Shelter is dedicated to providing support, dignity, and opportunity to individuals and families facing homelessness in the area.</p>
                <a href="pages/register.php" class="btn">Join Us</a>
            </div>
        </div>

        <div class="testimonial-section">
            <div class="testimonial active">
                <p>"Second Chance Shelter gave me the tools to get back on my feet. Forever grateful!"</p>
                <span>- Maria T.</span>
            </div>
            <div class="testimonial">
                <p>"The community and support here are unmatched. They truly care."</p>
                <span>- James K.</span>
            </div>
            <div class="testimonial">
                <p>"Without this shelter, I wouldn't have found stability. Thank you!"</p>
                <span>- Aisha B.</span>
            </div>
        </div>
    </div>
    <script>
        let index = 0;
        const testimonials = document.querySelectorAll('.testimonial');
        setInterval(() => {
            testimonials.forEach((t, i) => t.classList.toggle('active', i === index));
            index = (index + 1) % testimonials.length;
        }, 5000);
    </script>

   

    <div class="services-overview" id="services-section">
    <h2>Our Core Services</h2>
    <div class="services-grid">
        <div class="service-box">
            <h3>Emergency Housing</h3>
            <p>Providing a safe place to sleep and essential care for those in need.</p>
        </div>
        <div class="service-box">
            <h3>Meal Support</h3>
            <p>Three nutritious meals a day, every day, for our residents and visitors.</p>
        </div>
        <div class="service-box">
            <h3>Job Assistance</h3>
            <p>Career workshops, resume help, and job placement support.</p>
        </div>
    </div>
</div> 
<div class="volunteer-preview">
    <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
        <h2 style="align-self: center; margin-bottom: 0;">Want to Make a Difference?</h2>
        <p style="max-width: 600px; text-align: right;">Join our incredible team of volunteers and be a part of the change. From helping serve meals to organizing events, your time can brighten someone's future.</p>
        <a href="/pages/services.php" class="btn" style="margin-top: 10px;">Explore Volunteer Opportunities</a>
    </div>
</div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Second Chance Shelter. All rights reserved.</p>
        <p>Contact us: info@secondchanceshelter.org</p>
    </footer>
</body>
</html>