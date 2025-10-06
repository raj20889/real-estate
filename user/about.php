<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Real Estate Listings</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            
        }
        
        .overlay {
      
            min-height: 100vh;
        }

       
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: rgba(0, 123, 255, 0.9);
            padding: 15px 20px;
            backdrop-filter: blur(5px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 0 5px;
            background-color: rgba(0, 86, 179, 0.8);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .navbar a:hover {
            background-color: rgba(0, 61, 130, 0.9);
            transform: translateY(-2px);
        }

        
        .about {
            text-align: center;
            padding: 80px 20px;
            background-color: rgba(255, 255, 255, 0.8);
            margin: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .about h1 {
            font-size: 36px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .about p {
            font-size: 18px;
            color: #555;
            max-width: 800px;
            margin: auto;
            line-height: 1.6;
        }

    
        .team {
            padding: 50px 20px;
            text-align: center;
        }
        .team h2 {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 40px;
        }
        .team-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            padding: 0 20px;
        }
        .team-member {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 250px;
            transition: all 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        .team-member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid #007bff;
        }
        .team-member h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #333;
        }
        .team-member p {
            font-size: 16px;
            color: #666;
            font-style: italic;
        }

    
        .contact {
            text-align: center;
            padding: 50px 20px;
            background-color: rgba(255, 255, 255, 0.8);
            margin: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .contact h2 {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .contact p {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }
        .contact-info {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgba(0, 123, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
<div class="overlay">
    
    <div class="navbar">
        <div class="logo">🏡 Real Estate Listings</div>
        <div>
            <a href="user_dashboard.php">Home</a>
            <a href="about.php">About</a>
        </div>
    </div>

    <div class="about">
        <h1>About Us</h1>
        <p>Welcome to Real Estate Listings, your premier destination for finding the perfect property. With years of experience in the industry, our mission is to connect buyers and sellers with the best real estate opportunities while providing exceptional service and expertise.</p>
    </div>

    
    <div class="team">
        <h2>Meet Our Team</h2>
        <div class="team-container">
            <div class="team-member">
               
                <h3>MOHIT RAJ</h3>
                <p>Founder & CEO</p>
            </div>
            <div class="team-member">
              
                <h3>NIDHIN JOSEPH ABRAHAM</h3>
                <p>Chief Technology Officer</p>
            </div>
            <div class="team-member">
             
                <h3>MUHAMMED UNAIS.P</h3>
                <p>Marketing Manager</p>
            </div>
            <div class="team-member">
              
                <h3>MUHAMMED AMAN.P.P</h3>
                <p>Sales Director</p>
            </div>
            <div class="team-member">
             
                <h3>NAVANEETH B</h3>
                <p>Customer Relations</p>
            </div>
            <div class="team-member">
                
                <h3>SHAHWAZ ALAM</h3>
                <p>Property Specialist</p>
            </div>
        </div>
    </div>

    <div class="contact">
        <h2>Contact Us</h2>
        <div class="contact-info">
            <p><strong>Email:</strong> info@realestate.com</p>
            <p><strong>Phone:</strong> +91 7549958621</p>
            <p><strong>Address:</strong> Cochin University College of Engineering Pullincunno, Alappuzha, Kerala 688504</p>
            <p><strong>Hours:</strong> Monday-Friday: 9am-6pm</p>
        </div>
    </div>
</div>
</body>
</html>