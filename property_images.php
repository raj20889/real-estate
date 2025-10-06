<?php
include 'db/connect.php';


$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$sql = "SELECT image1, image2, image3 FROM properties WHERE id = $property_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Images</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
            position: relative;
        }
        
        
        .back-btn {
            position: fixed;
            top: 25px;
            left: 25px;
            display: inline-flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.9);
            color: #2c3e50;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .back-btn:hover {
            background-color: white;
            transform: translateX(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .back-btn svg {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin: 40px 0 30px;
            font-weight: 600;
        }
        
        .property-gallery {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        
        .property-image {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .property-image:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }
        
        .property-image img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
        }
        
        .image-number {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        p {
            text-align: center;
            color: #7f8c8d;
            font-size: 18px;
            grid-column: 1 / -1;
            padding: 40px 0;
        }
        
        
        .image-viewer {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .image-viewer img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
        }
        
        .close-btn {
            position: absolute;
            top: 30px;
            right: 30px;
            color: white;
            font-size: 30px;
            cursor: pointer;
            background: rgba(0, 0, 0, 0.5);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .property-gallery {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 10px;
            }
            
            .property-image img {
                height: 220px;
            }
            
            .back-btn {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
                font-size: 14px;
            }
            
            .close-btn {
                top: 15px;
                right: 15px;
                width: 40px;
                height: 40px;
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

    <a href="javascript:history.back()" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
        </svg>
        Back
    </a>

    <h2>Property Images</h2>
    
    <div class="property-gallery">
        <?php if ($row) { ?>
            <?php if (!empty($row['image1'])) { ?>
                <div class="property-image" onclick="openImageViewer('<?php echo htmlspecialchars($row['image1']); ?>')">
                    <img src="<?php echo htmlspecialchars($row['image1']); ?>" alt="Property Image">
                    <div class="image-number">1</div>
                </div>
            <?php } ?>
            
            <?php if (!empty($row['image2'])) { ?>
                <div class="property-image" onclick="openImageViewer('<?php echo htmlspecialchars($row['image2']); ?>')">
                    <img src="<?php echo htmlspecialchars($row['image2']); ?>" alt="Property Image">
                    <div class="image-number">2</div>
                </div>
            <?php } ?>
            
            <?php if (!empty($row['image3'])) { ?>
                <div class="property-image" onclick="openImageViewer('<?php echo htmlspecialchars($row['image3']); ?>')">
                    <img src="<?php echo htmlspecialchars($row['image3']); ?>" alt="Property Image">
                    <div class="image-number">3</div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>No images found for this property.</p>
        <?php } ?>
    </div>
    

    <div class="image-viewer" id="imageViewer" onclick="closeImageViewer()">
        <span class="close-btn" onclick="event.stopPropagation(); closeImageViewer()">&times;</span>
        <img id="fullscreenImage" src="" alt="Fullscreen Property Image">
    </div>

    <script>
        function openImageViewer(imageSrc) {
            event.stopPropagation();
            const viewer = document.getElementById('imageViewer');
            const fullscreenImage = document.getElementById('fullscreenImage');
            
            fullscreenImage.src = imageSrc;
            viewer.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageViewer() {
            const viewer = document.getElementById('imageViewer');
            viewer.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImageViewer();
            }
        });
    </script>
</body>
</html>

<?php

mysqli_close($conn);
?>