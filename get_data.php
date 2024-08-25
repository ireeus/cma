<?php
if (isset($_GET['sessionName']) && isset($_GET['data'])) {
    $sessionName = $_GET['sessionName'];
    $data = $_GET['data'];
    $sessionFilePath = 'sessions/' . $sessionName . '_session.txt';

    if (file_exists($sessionFilePath)) {
        $fileContent = file_get_contents($sessionFilePath);
    } else {
        $fileContent = '';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gallery</title>
    <style>
        .gallery {
            position: relative;
            max-width: 100%;
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gallery-item {
            text-align: center;
            display: none;
            max-width: 100%;
            max-height: 100%;
            position: relative;
        }

        .gallery-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .gallery-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1;
        }

        .gallery-nav a {
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .gallery-nav a:hover {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
    <?php 
    include("lib/logfile.php");

    if (isset($fileContent) && !empty($fileContent)): ?>
        <div class="gallery">
            <?php
            $regex = '/\[file:\s*([^]]+\.jpg)\]/i';
            $matches = preg_match_all($regex, $fileContent, $imageNames, PREG_SET_ORDER);
            if ($matches) {
                foreach ($imageNames as $match) {
                    $imageName = $match[1];
                    echo "<div class='gallery-item'>";
                    echo "<img src='images/$imageName' alt='$imageName' data-name='$imageName'>";
                    echo "<div class='gallery-nav'>";
                    echo "<a href='#' class='prev-btn'> </a>";
                    echo "<a href='#' class='next-btn'> </a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No images found in the session file.</p>";
            }
            ?>
        </div>
    <?php else: ?>
        <p>No session file provided or the file is empty.</p>
    <?php endif; ?>

    <script>
        const galleryItems = document.querySelectorAll('.gallery-item');
        const data = '<?php echo $data; ?>'; // Get the data parameter from PHP

        let currentIndex = 0;

        // Find the index of the image received in the data parameter
        const initialImageIndex = Array.from(galleryItems).findIndex(item => {
            const imgName = item.querySelector('img').dataset.name;
            return imgName === data.replace('[file: ', '').replace(']', '');
        });

        // If the initial image is found, set the currentIndex to its index, otherwise start from 0
        currentIndex = initialImageIndex !== -1 ? initialImageIndex : 0;

        function showItem(index) {
            galleryItems.forEach((item, i) => {
                item.style.display = (i === index) ? 'block' : 'none';
            });
        }

        const prevBtns = document.querySelectorAll('.prev-btn');
        const nextBtns = document.querySelectorAll('.next-btn');

        function navigateGallery(direction) {
            currentIndex = (currentIndex + direction + galleryItems.length) % galleryItems.length;
            showItem(currentIndex);
        }

        prevBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                navigateGallery(-1);
            });
        });

        nextBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                navigateGallery(1);
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                navigateGallery(-1);
            } else if (e.key === 'ArrowRight') {
                navigateGallery(1);
            }
        });

        showItem(currentIndex);
    </script>
</body>
</html>
